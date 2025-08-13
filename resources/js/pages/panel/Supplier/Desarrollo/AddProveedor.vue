<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nuevo proveedor" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
        </template>
        <template #end>
            <!-- ToolsSupplier para los botones de exportar e importar -->
            <ToolsSupplier @import-success="loadProveedor"/>       
        </template>
    </Toolbar>

    <Dialog v-model:visible="proveedorDialog" :style="{ width: '600px' }" header="Registro de proveedor" :modal="true">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-10">
                    <label class="block font-bold mb-2">Nombre <span class="text-red-500">*</span></label>
                    <InputText
                        v-model.trim="proveedor.name"
                        required
                        maxlength="150"
                        fluid
                    />
                    <small v-if="submitted && !proveedor.name" class="text-red-500">El nombre es obligatorio.</small>
                    <small v-if="serverErrors.name" class="text-red-500">{{ serverErrors.name[0] }}</small>
                </div>

                <div class="col-span-2">
                    <label class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="proveedor.state" :binary="true" />
                        <Tag :value="proveedor.state ? 'Activo' : 'Inactivo'" :severity="proveedor.state ? 'success' : 'danger'" />
                    </div>
                    <small v-if="serverErrors.state" class="text-red-500">{{ serverErrors.state[0] }}</small>
                </div>

                <div class="col-span-12">
                    <label class="block font-bold mb-2">RUC <span class="text-red-500">*</span></label>
                    <InputText
                        v-model.trim="proveedor.ruc"
                        required
                        maxlength="11"
                        fluid
                    />
                    <small v-if="submitted && !proveedor.ruc" class="text-red-500">El RUC es obligatorio.</small>
                    <small v-if="serverErrors.ruc" class="text-red-500">{{ serverErrors.ruc[0] }}</small>
                </div>

                <div class="col-span-12">
                    <label class="block font-bold mb-2">Dirección <span class="text-red-500">*</span></label>
                    <InputText
                        v-model.trim="proveedor.address"
                        maxlength="255"
                        fluid
                    />
                    <small v-if="submitted && !proveedor.address" class="text-red-500">La dirección es obligatoria.</small>
                    <small v-if="serverErrors.address" class="text-red-500">{{ serverErrors.address[0] }}</small>
                </div>

                <div class="col-span-12">
                    <label class="block font-bold mb-2">Teléfono <span class="text-red-500">*</span></label>
                    <InputText
                        v-model.trim="proveedor.phone"
                        maxlength="11"
                        fluid
                    />
                    <small v-if="submitted && !proveedor.phone" class="text-red-500">El telefono es obligatorio.</small>
                    <small v-if="serverErrors.phone" class="text-red-500">{{ serverErrors.phone[0] }}</small>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
            <Button label="Guardar" icon="pi pi-check" @click="guardarProveedor" />
        </template>
    </Dialog>
</template>

<script setup>
import {ref} from 'vue';
import axios from 'axios';
import Dialog from 'primevue/dialog';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';
import ToolsSupplier from './toolsSupplier.vue';

const toast = useToast();
const submitted = ref(false);
const proveedorDialog = ref(false);
const serverErrors = ref({});
const emit = defineEmits(['proveedor-agregado']);

const proveedor = ref({
    name: '',
    ruc: '',
    address: '',
    phone: '',
    state: true
});
// Método para recargar la lista de proveedores
const loadProveedor = async () => {
    try {
        const response = await axios.get('/proveedor');  // Aquí haces una solicitud GET para obtener los proveedores
        console.log(response.data);
        // Realiza lo que necesites con la respuesta, como actualizar el listado en un componente superior
        emit('proveedor-agregada');  // Si quieres que un componente padre reciba la notificación de la actualización
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar los proveedores', life: 3000 });
        console.error(error);
    }
}
function resetProveedor() {
    proveedor.value = {
        name: '',
        ruc: '',
        address: '',
        phone: '',
        state: true
    };
    serverErrors.value = {};
    submitted.value = false;
}

function openNew() {
    resetProveedor();
    proveedorDialog.value = true;
}

function hideDialog() {
    proveedorDialog.value = false;
    resetProveedor();
}

function guardarProveedor() {
    submitted.value = true;
    serverErrors.value = {};

    if (!proveedor.value.name || !proveedor.value.ruc) return;

    axios.post('/proveedor', proveedor.value)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Proveedor registrado', life: 3000 });
            hideDialog();
            emit('proveedor-agregado');
        })
        .catch(error => {
            if (error.response && error.response.state === 422) {
                serverErrors.value = error.response.data.errors || {};
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'No se pudo registrar el proveedor',
                    life: 3000
                });
            }
        });
}
</script>
