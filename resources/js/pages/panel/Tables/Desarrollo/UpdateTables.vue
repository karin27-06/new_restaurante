<script setup>
import { ref, watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Select from 'primevue/select';
//import Textarea from 'primevue/textarea';
import Dropdown from 'primevue/dropdown';  // Importamos Dropdown
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    tableId: Number
});
const emit = defineEmits(['update:visible', 'updated']);

const toast = useToast();
const serverErrors = ref({});
const submitted = ref(false);
const loading = ref(false);

const dialogVisible = ref(props.visible);
watch(() => props.visible, (val) => dialogVisible.value = val);
watch(dialogVisible, (val) => emit('update:visible', val));

const table = ref({
    name: '',
    tablenum: '',
    capacity: null,
    state: true,
    idArea: null,
    idFloor: null
});


const areas = ref([]);
const pisos = ref([]);

watch(() => props.visible, async (val) => {
    if (val && props.tableId) {
        await fetchTable();
        await fetchAreas();
        await fetchFloors();
    }
});

const fetchTable = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(`/mesa/${props.tableId}`);
        const t = data.table;
        table.value = {
            name: t.name,
            tablenum: t.tablenum,
            capacity: t.capacity,
            state: t.state,
            idArea: t.idArea,
            idFloor: t.idFloor
        };
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'No se pudo cargar la mesas',
            life: 3000
        });
    } finally {
        loading.value = false;
    }
};

const fetchAreas = async () => {
    try {
        const { data } = await axios.get('/area', { params: { state: 1 } });
        areas.value = data.data.map(c => ({ label: c.name, value: c.id }));
    } catch (e) {
        toast.add({ severity: 'warn', summary: 'Advertencia', detail: 'No se pudieron cargar las areas' });
    }
};

const fetchFloors = async () => {
    try {
        const { data } = await axios.get('/piso', { params: { state: 1 } });
        pisos.value = data.data.map(a => ({ label: a.name, value: a.id }));
    } catch (e) {
        toast.add({ severity: 'warn', summary: 'Advertencia', detail: 'No se pudieron cargar los pisos' });
    }
};

const updateTable = async () => {
    submitted.value = true;
    serverErrors.value = {};

    try {
        const dataToSend = {
            name: table.value.name,
            tablenum: table.value.tablenum,
            capacity: table.value.capacity,
            state: table.value.state === true,
            idArea: table.value.idArea,  // <-- CORREGIDO
            idFloor: table.value.idFloor // <-- CORREGIDO
        };

        await axios.put(`/mesa/${props.tableId}`, dataToSend);

        toast.add({
            severity: 'success',
            summary: 'Actualizado',
            detail: 'Mesa actualizada correctamente',
            life: 3000
        });

        dialogVisible.value = false;
        emit('updated');
    } catch (error) {
        if (error.response?.data?.errors) {
            serverErrors.value = error.response.data.errors;
            toast.add({
                severity: 'error',
                summary: 'Error de validación',
                detail: error.response.data.message || 'Revisa los campos.',
                life: 5000
            });
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'No se pudo actualizar la mesa',
                life: 3000
            });
        }
    }
};

</script>

<template>
    <Dialog v-model:visible="dialogVisible" header="Editar Mesa" modal :closable="true" :closeOnEscape="true"
        :style="{ width: '700px' }">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <!-- Nombre -->
                <div class="col-span-10">
                    <label class="block font-bold mb-2">Nombre <span class="text-red-500">*</span></label>
                    <InputText v-model="table.name" required maxlength="100" fluid
                        :class="{ 'p-invalid': serverErrors.name }" />
                    <small v-if="serverErrors.name" class="p-error">{{ serverErrors.name[0] }}</small>
                </div>

                <!-- Estado -->
                <div class="col-span-2">
                    <label class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="table.state" :binary="true" fluid />
                        <Tag :value="table.state ? 'Activo' : 'Inactivo'"
                            :severity="table.state ? 'success' : 'danger'" />
                    </div>
                </div>

                <!-- Número -->
                <div class="col-span-6">
                    <label class="mb-2 block font-bold">Número <span class="text-red-500">*</span></label>
                    <InputText v-model.trim="table.tablenum" fluid maxlength="50" />
                    <small v-if="submitted && !table.tablenum" class="text-red-500">El número es obligatorio.</small>
                    <small v-else-if="serverErrors.tablenum" class="text-red-500">{{ serverErrors.tablenum[0] }}</small>
                </div>
                <!-- Capacidad -->
                <div class="col-span-6">
                    <label class="mb-2 block font-bold">Capacidad <span class="text-red-500">*</span></label>
                    <InputText v-model.number="table.capacity" type="number" fluid min="1" />
                    <small v-if="submitted && !table.capacity" class="text-red-500">La capacidad es obligatoria.</small>
                    <small v-else-if="table.capacity < 1" class="text-red-500">Debe ser al menos 1 persona.</small>
                    <small v-else-if="serverErrors.capacity" class="text-red-500">{{ serverErrors.capacity[0] }}</small>
                </div>

                <!-- Area -->
                <div class="col-span-6">
                    <label class="mb-2 block font-bold">Área <span class="text-red-500">*</span></label>
                    <Dropdown
                        v-model="table.idArea"
                        fluid
                        :options="areas"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Seleccione un área"
                        filter
                        filterPlaceholder="Buscar área"
                    />
                    <small v-if="submitted && !table.idArea" class="text-red-500">El área es obligatoria.</small>
                    <small v-else-if="serverErrors.idArea" class="text-red-500">{{ serverErrors.idArea[0] }}</small>
                </div>

                <!-- Piso -->
                <div class="col-span-6">
                    <label class="mb-2 block font-bold">Piso <span class="text-red-500">*</span></label>
                    <Dropdown
                        v-model="table.idFloor"
                        :options="pisos"
                        fluid
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Seleccione un piso"
                        filter
                        filterPlaceholder="Buscar piso"
                    />
                    <small v-if="submitted && !table.idFloor" class="text-red-500">El piso es obligatorio.</small>
                    <small v-else-if="serverErrors.idFloor" class="text-red-500">{{ serverErrors.idFloor[0] }}</small>
                </div>
             
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="dialogVisible = false" />
            <Button label="Guardar" icon="pi pi-check" @click="updateTable" :loading="loading" />
        </template>
    </Dialog>
</template>
