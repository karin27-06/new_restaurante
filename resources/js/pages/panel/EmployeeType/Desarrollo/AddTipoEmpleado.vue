<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nuevo tipo de empleado" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
        </template>
        <template #end>
            <!-- ToolsEmployeeType para los botones de exportar e importar -->
            <ToolsEmployeeType @import-success="loadTipoEmpleado"/>       
        </template>
    </Toolbar>

    <Dialog v-model:visible="tipoEmpleadoDialog" :style="{ width: '600px' }" header="Registro de tipo de empleado" :modal="true">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-9">
                    <label for="name" class="block font-bold mb-3">Nombre <span class="text-red-500">*</span></label>
                    <InputText id="name" v-model.trim="tipoEmpleado.name" required maxlength="100" fluid />
                    <small v-if="submitted && !tipoEmpleado.name" class="text-red-500">El nombre es obligatorio.</small>
                    <small v-else-if="submitted && tipoEmpleado.name && tipoEmpleado.name.length < 2" class="text-red-500">
                        El nombre debe tener al menos 2 caracteres.
                    </small>
                    <small v-else-if="serverErrors.name" class="text-red-500">{{ serverErrors.name[0] }}</small>
                </div>

                <div class="col-span-3">
                    <label for="state" class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="tipoEmpleado.state" :binary="true" inputId="state" />
                        <Tag :value="tipoEmpleado.state ? 'Activo' : 'Inactivo'"
                             :severity="tipoEmpleado.state ? 'success' : 'danger'" />
                        <small v-if="submitted && tipoEmpleado.state === null" class="text-red-500">El estado es obligatorio.</small>
                        <small v-else-if="serverErrors.state" class="text-red-500">{{ serverErrors.state[0] }}</small>
                    </div>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
            <Button label="Guardar" icon="pi pi-check" @click="guardarTipoEmpleado" />
        </template>
    </Dialog>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import Dialog from 'primevue/dialog';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';
import { defineEmits } from 'vue';
import ToolsEmployeeType from './toolsEmployeeType.vue';

const toast = useToast();
const submitted = ref(false);
const tipoEmpleadoDialog = ref(false);
const serverErrors = ref({});
const emit = defineEmits(['tipo-empleado-agregado']);

const tipoEmpleado = ref({
    name: '',
    state: true
});
// Método para recargar la lista de tipos de empleado
const loadTipoEmpleado = async () => {
    try {
        const response = await axios.get('/tipos_empleados');  // Aquí haces una solicitud GET para obtener los tipos de empleado
        console.log(response.data);
        // Realiza lo que necesites con la respuesta, como actualizar el listado en un componente superior
        emit('tipos_empleados-agregada');  // Si quieres que un componente padre reciba la notificación de la actualización
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar los tipos de empleado', life: 3000 });
        console.error(error);
    }
}
function resetTipoEmpleado() {
    tipoEmpleado.value = {
        name: '',
        state: true
    };
    serverErrors.value = {};
    submitted.value = false;
}

function openNew() {
    resetTipoEmpleado();
    tipoEmpleadoDialog.value = true;
}

function hideDialog() {
    tipoEmpleadoDialog.value = false;
    resetTipoEmpleado();
}

function guardarTipoEmpleado() {
    submitted.value = true;
    serverErrors.value = {};

    axios.post('/tipo_empleado', tipoEmpleado.value)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Tipo de empleado registrado', life: 3000 });
            hideDialog();
            emit('tipo-empleado-agregado');
        })
        .catch(error => {
            if (error.response && error.response.status === 422) {
                serverErrors.value = error.response.data.errors || {};
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'No se pudo registrar el tipo de empleado',
                    life: 3000
                });
            }
        });
}
</script>
