<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nuevo empleado" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
        </template>
        <template #end>
            <!-- ToolsEmployee para los botones de exportar e importar -->
            <ToolsEmployee @import-success="loadEmpleado"/>       
        </template>
    </Toolbar>

    <Dialog v-model:visible="empleadoDialog" :style="{ width: '600px' }" header="Registro de empleado" :modal="true">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-10">
                    <label class="block font-bold mb-2">Nombre <span class="text-red-500">*</span></label>
                    <InputText
                        v-model.trim="empleado.name"
                        required
                        placeholder="Ingrese su nombre"
                        maxlength="100"
                        fluid
                    />
                    <small v-if="submitted && !empleado.name" class="text-red-500">El nombre es obligatorio.</small>
                    <small v-if="serverErrors.name" class="text-red-500">{{ serverErrors.name[0] }}</small>
                </div>

                <div class="col-span-2">
                    <label class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="empleado.state" :binary="true" />
                        <Tag :value="empleado.state ? 'Activo' : 'Inactivo'" :severity="empleado.state ? 'success' : 'danger'" />
                    </div>
                    <small v-if="serverErrors.state" class="text-red-500">{{ serverErrors.state[0] }}</small>
                </div>

                <div class="col-span-12">
                    <label class="block font-bold mb-2">Código <span class="text-red-500">*</span></label>
                    <InputText
                        v-model.trim="empleado.codigo"
                        required
                        placeholder="Ingrese su DNI"
                        fluid
                        maxlength="8"
                    />
                    <small v-if="submitted && !empleado.codigo" class="text-red-500">El código es obligatorio.</small>
                    <small v-if="serverErrors.codigo" class="text-red-500">{{ serverErrors.codigo[0] }}</small>
                </div>
                <!-- Tipo de Empleado -->
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Tipo de Empleado <span class="text-red-500">*</span></label>
                    <Dropdown
                        v-model="empleado.employee_type_id"
                        :options="tiposEmpleado"
                        optionLabel="name"
                        optionValue="id"
                        fluid
                        placeholder="Seleccione empleado"
                        class="w-full"
                        filter
                        filterPlaceholder="Buscar tipo de empleado"
                    />
                    <small v-if="submitted && !empleado.employee_type_id" class="text-red-500">Debe seleccionar un tipo.</small>
                    <small v-if="serverErrors.employee_type_id" class="text-red-500">{{ serverErrors.employee_type_id[0] }}</small>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
            <Button label="Guardar" icon="pi pi-check" @click="guardarEmpleado" />
        </template>
    </Dialog>
</template>

<script setup>
import { ref} from 'vue';
import axios from 'axios';
import Dialog from 'primevue/dialog';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';
import Dropdown from 'primevue/dropdown';  // Importamos Dropdown
import ToolsEmployee from './toolsEmployee.vue';

const toast = useToast();
const submitted = ref(false);
const empleadoDialog = ref(false);
const serverErrors = ref({});
const tiposEmpleado = ref([]);

const emit = defineEmits(['empleado-agregado']);

const empleado = ref({
    name: '',
    codigo: '',
    employee_type_id: null,
    state: true
});
// Método para recargar la lista de empleados
const loadEmpleado = async () => {
    try {
        const response = await axios.get('/empleado');  // Aquí haces una solicitud GET para obtener los empleados
        console.log(response.data);
        // Realiza lo que necesites con la respuesta, como actualizar el listado en un componente superior
        emit('empleado-agregada');  // Si quieres que un componente padre reciba la notificación de la actualización
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar los empleados', life: 3000 });
        console.error(error);
    }
}
function resetEmpleado() {
    empleado.value = {
        name: '',
        codigo: '',
        employee_type_id: null,
        state: true
    };
    serverErrors.value = {};
    submitted.value = false;
}

function openNew() {
    resetEmpleado();
    empleadoDialog.value = true;
    fetchTiposEmpleado();
}

function hideDialog() {
    empleadoDialog.value = false;
    resetEmpleado();
}

function fetchTiposEmpleado() {
    axios.get('/tipo_empleado', { params: { state: 1 } })
        .then(res => {
            tiposEmpleado.value = res.data.data;
        })
        .catch(() => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar los tipos de empleado', life: 3000 });
        });
}

function guardarEmpleado() {
    submitted.value = true;
    serverErrors.value = {};

    if (!empleado.value.name || !empleado.value.codigo || !empleado.value.employee_type_id) return;

    axios.post('/empleado', empleado.value)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Empleado registrado', life: 3000 });
            hideDialog();
            emit('empleado-agregado');
        })
        .catch(error => {
            if (error.response && error.response.status === 422) {
                serverErrors.value = error.response.data.errors || {};
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'No se pudo registrar el empleado',
                    life: 3000
                });
            }
        });
}
</script>
