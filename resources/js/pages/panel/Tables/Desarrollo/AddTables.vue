<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nueva Mesa" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
        </template>
        <template #end>
            <!-- ToolsTable para los botones de exportar e importar -->
            <ToolsTable @import-success="loadMesa"/>       
        </template>
    </Toolbar>

    <Dialog v-model:visible="tableDialog" :style="{ width: '700px' }" header="Registro de Mesas" :modal="true">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <!-- Nombre -->
                <div class="col-span-10">
                    <label class="mb-2 block font-bold">Nombre <span class="text-red-500">*</span></label>
                    <InputText v-model.trim="table.name" fluid maxlength="100" />
                    <small v-if="submitted && !table.name" class="text-red-500">El nombre es obligatorio.</small>
                    <small v-else-if="submitted && table.name.length < 2" class="text-red-500">El nombre debe tener al menos 2 caracteres.</small>
                    <small v-else-if="serverErrors.name" class="text-red-500">{{ serverErrors.name[0] }}</small>
                </div>

                <!-- Estado -->
                <div class="col-span-2">
                    <label class="mb-2 block font-bold">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="table.state" :binary="true" />
                        <Tag :value="table.state ? 'Activo' : 'Inactivo'" fluid :severity="table.state ? 'success' : 'danger'" />
                        <small v-if="submitted && table.state === null" class="text-red-500">El estado es obligatorio.</small>
                        <small v-else-if="serverErrors.state" class="text-red-500">{{ serverErrors.state[0] }}</small>
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
                        placeholder="Seleccione el área"
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
                        placeholder="Seleccione el piso"
                        filter
                        filterPlaceholder="Buscar piso"
                    />
                    <small v-if="submitted && !table.idFloor" class="text-red-500">El piso es obligatorio.</small>
                    <small v-else-if="serverErrors.idFloor" class="text-red-500">{{ serverErrors.idFloor[0] }}</small>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
            <Button label="Guardar" icon="pi pi-check" @click="guardarTable" />
        </template>
    </Dialog>
</template>

<script setup>
import axios from 'axios';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
//import Select from 'primevue/select';
import Tag from 'primevue/tag';
import Toolbar from 'primevue/toolbar';
import { useToast } from 'primevue/usetoast';
import { ref } from 'vue';
import ToolsTable from './toolsTable.vue';
import Dropdown from 'primevue/dropdown';  // Importamos Dropdown

const toast = useToast();
const submitted = ref(false);
const tableDialog = ref(false);
const serverErrors = ref({});
const emit = defineEmits(['tables-agregado']);

const table = ref({
    name: '',
    tablenum: '',
    capacity: null,
    state: true,
    idArea: null,
    idFloor: null
});
// Método para recargar la lista de mesas
const loadMesa = async () => {
    try {
        const response = await axios.get('/mesa');  // Aquí haces una solicitud GET para obtener las mesas
        console.log(response.data);
        // Realiza lo que necesites con la respuesta, como actualizar el listado en un componente superior
        emit('mesa-agregada');  // Si quieres que un componente padre reciba la notificación de la actualización
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar las mesas', life: 3000 });
        console.error(error);
    }
}
const areas = ref([]);
const pisos = ref([]);

function resetTable() {
    table.value = {
        name: '',
        tablenum: '',
        capacity: null,
        state: true,
        idArea: null,
        idFloor: null
    };
    serverErrors.value = {};
    submitted.value = false;
}


function openNew() {
    resetTable();
    fetchAreas();
    fetchFloors();
    tableDialog.value = true;
}

function hideDialog() {
    tableDialog.value = false;
    resetTable();
}

async function fetchAreas() {
    try {
        const { data } = await axios.get('/area', { params: { state: 1 } });
        areas.value = data.data.map((c) => ({ label: c.name, value: c.id }));
    } catch (e) {
        toast.add({ severity: 'warn', summary: 'Advertencia', detail: 'No se pudieron cargar las Areas' });
    }
}

async function fetchFloors() {
    try {
        const { data } = await axios.get('/piso', { params: { state: 1 } });
        pisos.value = data.data.map((a) => ({ label: a.name, value: a.id }));
    } catch (e) {
        toast.add({ severity: 'warn', summary: 'Advertencia', detail: 'No se pudieron cargar los pisos' });
    }
}

function guardarTable() {
    submitted.value = true;
    serverErrors.value = {};

    axios
        .post('/mesa', table.value)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Mesa registrada', life: 3000 });
            hideDialog();
            emit('tables-agregado');
        })
        .catch((error) => {
            if (error.response?.status === 422) {
                serverErrors.value = error.response.data.errors || {};
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'No se pudo registrar la mesa',
                    life: 3000,
                });
            }
        });
}
</script>
