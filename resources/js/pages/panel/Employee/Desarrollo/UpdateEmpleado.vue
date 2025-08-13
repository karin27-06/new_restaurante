<script setup>
import { ref,watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Tag from 'primevue/tag';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import Dropdown from 'primevue/dropdown';  // Importamos Dropdown

const props = defineProps({
    visible: Boolean,
    empleadoId: Number
});
const emit = defineEmits(['update:visible', 'updated']);

const toast = useToast();
const dialogVisible = ref(props.visible);
const loading = ref(false);
const submitted = ref(false);
const serverErrors = ref({});

const empleado = ref({
    name: '',
    codigo: '',
    employee_type_id: null,
    state: false,
});

const tiposEmpleado = ref([]);

watch(() => props.visible, (val) => {
    dialogVisible.value = val;
    if (val && props.empleadoId) {
        fetchEmpleado();
        fetchTiposEmpleado();
    }
});
watch(dialogVisible, (val) => emit('update:visible', val));

const fetchEmpleado = async () => {
    try {
        loading.value = true;
        const res = await axios.get(`/empleado/${props.empleadoId}`);
        const data = res.data.employee;
        empleado.value = {
            name: data.name,
            codigo: data.codigo,
            employee_type_id: data.employee_type_id,
            state: data.state
        };
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar el empleado', life: 3000 });
        console.error(error);
    } finally {
        loading.value = false;
    }
};

const fetchTiposEmpleado = async () => {
    try {
        const res = await axios.get('/tipo_empleado', { params: { state: 1 } });
        tiposEmpleado.value = res.data.data;
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar los tipos de empleado', life: 3000 });
        console.error(error);
    }
};

const updateEmpleado = async () => {
    submitted.value = true;
    serverErrors.value = {};

    try {
        const empleadoData = {
            name: empleado.value.name,
            codigo: empleado.value.codigo,
            employee_type_id: empleado.value.employee_type_id,
            state: empleado.value.state
        };

        await axios.put(`/empleado/${props.empleadoId}`, empleadoData);

        toast.add({
            severity: 'success',
            summary: 'Actualizado',
            detail: 'Empleado actualizado correctamente',
            life: 3000
        });

        dialogVisible.value = false;
        emit('updated');
    } catch (error) {
        if (error.response && error.response.data?.errors) {
            serverErrors.value = error.response.data.errors;
            toast.add({
                severity: 'error',
                summary: 'Error de validación',
                detail: 'Revisa los campos e intenta nuevamente.',
                life: 5000
            });
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'No se pudo actualizar el empleado',
                life: 3000
            });
        }
        console.error(error);
    }
};
</script>

<template>
    <Dialog v-model:visible="dialogVisible" header="Editar Empleado" modal :closable="true" :style="{ width: '600px' }">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-10">
                    <label class="block font-bold mb-2">Código <span class="text-red-500">*</span></label>
                    <InputText
                        v-model="empleado.codigo"
                        required
                        fluid
                        maxlength="50"
                        :class="{ 'p-invalid': serverErrors.codigo }"
                    />
                    <small v-if="serverErrors.codigo" class="p-error">{{ serverErrors.codigo[0] }}</small>
                </div>
                <div class="col-span-2">
                    <label class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="empleado.state" :binary="true" />
                        <Tag :value="empleado.state ? 'Activo' : 'Inactivo'" :severity="empleado.state ? 'success' : 'danger'" />
                    </div>
                </div>
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Nombre <span class="text-red-500">*</span></label>
                    <InputText
                        v-model="empleado.name"
                        required
                        maxlength="100"
                        fluid
                        :class="{ 'p-invalid': serverErrors.name }"
                    />
                    <small v-if="serverErrors.name" class="p-error">{{ serverErrors.name[0] }}</small>
                </div>
                <!--TIPO DE EMPLEADO-->
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Tipo de Empleado <span class="text-red-500">*</span></label>
                    <Dropdown
                        v-model="empleado.employee_type_id"
                        :options="tiposEmpleado"
                        optionLabel="name"
                        optionValue="id"
                        fluid
                        placeholder="Seleccione"
                        class="w-full"
                        filter
                        filterPlaceholder="Buscar tipo de empleado"
                        :class="{ 'p-invalid': serverErrors.employee_type_id }"
                    />
                    <small v-if="serverErrors.employee_type_id" class="p-error">{{ serverErrors.employee_type_id[0] }}</small>
                </div>
            </div>
        </div>
        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="dialogVisible = false" />
            <Button label="Guardar" icon="pi pi-check" @click="updateEmpleado" :loading="loading" />
        </template>
    </Dialog>
</template>
