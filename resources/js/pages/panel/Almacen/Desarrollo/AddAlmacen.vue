<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nuevo almacen" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
        </template>
        <template #end>
            <!-- ToolsAlmacen para los botones de exportar e importar -->
            <ToolsAlmacen @import-success="loadAlmacen"/>       
        </template>
    </Toolbar>

    <Dialog v-model:visible="almacenDialog" :style="{ width: '600px' }" header="Registro de almacens" :modal="true">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-9">
                    <label for="name" class="block font-bold mb-3">Nombres <span class="text-red-500">*</span></label>
                    <InputText id="name" v-model.trim="almacen.name" required maxlength="100" fluid />
                    <small v-if="submitted && !almacen.name" class="text-red-500">El nombre es obligatorio.</small>
                    <small v-else-if="submitted && almacen.name && almacen.name.length < 2" class="text-red-500">El
                        nombre debe tener al menos 2 caracteres.</small>
                    <small v-else-if="serverErrors.name" class="text-red-500">{{ serverErrors.name[0] }}</small>
                </div>
                <div class="col-span-3">
                    <label for="state" class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="almacen.state" :binary="true" inputId="state" />
                        <Tag :value="almacen.state ? 'Activo' : 'Inactivo'"
                            :severity="almacen.state ? 'success' : 'danger'" />
                        <small v-if="submitted && almacen.state === null" class="text-red-500">El estado es obligatorio.</small>
                        <small v-else-if="serverErrors.state" class="text-red-500">{{ serverErrors.state[0] }}</small>
                    </div>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
            <Button label="Guardar" icon="pi pi-check" @click="guardaralmacen" />
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
import ToolsAlmacen from './toolsAlmacen.vue';

const toast = useToast();
const submitted = ref(false);
const almacenDialog = ref(false);
const serverErrors = ref({});
const emit = defineEmits(['almacen-agregado']);

const almacen = ref({
    name: '',
    state: true
});
// Método para recargar la lista de almacenes
const loadAlmacen = async () => {
    try {
        const response = await axios.get('/almacen');  // Aquí haces una solicitud GET para obtener los almacenes
        console.log(response.data);
        // Realiza lo que necesites con la respuesta, como actualizar el listado en un componente superior
        emit('almacen-agregada');  // Si quieres que un componente padre reciba la notificación de la actualización
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar los almacenes', life: 3000 });
        console.error(error);
    }
}

function resetAlmacen() {
    almacen.value = {
        name: '',
        state: true
    };
    serverErrors.value = {};
    submitted.value = false;
}

function openNew() {
    resetAlmacen();
    almacenDialog.value = true;
}

function hideDialog() {
    almacenDialog.value = false;
    resetAlmacen();
}

function guardaralmacen() {
    submitted.value = true;
    serverErrors.value = {};

    axios.post('/almacen', almacen.value)
        .then(()=> {
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Almacén registrado', life: 3000 });
            hideDialog();
            emit('almacen-agregado');
        })
        .catch(error => {
            if (error.response && error.response.status === 422) {
                serverErrors.value = error.response.data.errors || {};
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'No se pudo registrar el almacén',
                    life: 3000
                });
            }
        });
}
</script>
