<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nueva presentación" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
        </template>
        <template #end>
            <!-- ToolsPresentation para los botones de exportar e importar -->
            <ToolsPresentation @import-success="loadPresentacion"/>       
        </template>
    </Toolbar>

    <Dialog v-model:visible="presentacionDialog" :style="{ width: '600px' }" header="Registro de presentación" :modal="true">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-10">
                    <label class="block font-bold mb-3">Nombre <span class="text-red-500">*</span></label>
                    <InputText
                        v-model.trim="presentacion.name"
                        required
                        maxlength="150"
                        fluid
                    />
                    <small v-if="submitted && !presentacion.name" class="text-red-500">El nombre es obligatorio.</small>
                    <small v-if="serverErrors.name" class="text-red-500">{{ serverErrors.name[0] }}</small>
                </div>
                <div class="col-span-2">
                    <label class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="presentacion.state" :binary="true" />
                        <Tag :value="presentacion.state ? 'Activo' : 'Inactivo'" :severity="presentacion.state ? 'success' : 'danger'" />
                    </div>
                    <small v-if="serverErrors.state" class="text-red-500">{{ serverErrors.state[0] }}</small>
                </div>
                <div class="col-span-12">
                    <label class="block font-bold mb-3">Descripción</label>
                    <Textarea fluid v-model="presentacion.description" maxlength="255" rows="4" autoResize
                        :class="{ 'p-invalid': serverErrors.description }" />
                    <small v-if="serverErrors.description" class="text-red-500">{{ serverErrors.description[0]
                        }}</small>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
            <Button label="Guardar" icon="pi pi-check" @click="guardarPresentacion" />
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
import Textarea from 'primevue/textarea';
import Checkbox from 'primevue/checkbox';
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';
import ToolsPresentation from './toolsPresentation.vue';

const toast = useToast();
const submitted = ref(false);
const presentacionDialog = ref(false);
const serverErrors = ref({});
const emit = defineEmits(['presentacion-agregada']);

const presentacion = ref({
    name: '',
    description: '',
    state: true
});
// Método para recargar la lista de presentaciones
const loadPresentacion = async () => {
    try {
        const response = await axios.get('/presentacion');  // Aquí haces una solicitud GET para obtener las presentaciones
        console.log(response.data);
        // Realiza lo que necesites con la respuesta, como actualizar el listado en un componente superior
        emit('presentacion-agregada');  // Si quieres que un componente padre reciba la notificación de la actualización
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar las presentaciones', life: 3000 });
        console.error(error);
    }
}
function resetPresentacion() {
    presentacion.value = {
        name: '',
        description: '',
        state: true
    };
    serverErrors.value = {};
    submitted.value = false;
}

function openNew() {
    resetPresentacion();
    presentacionDialog.value = true;
}

function hideDialog() {
    presentacionDialog.value = false;
    resetPresentacion();
}

function guardarPresentacion() {
    submitted.value = true;
    serverErrors.value = {};

    if (!presentacion.value.name) return;

    axios.post('/presentacion', presentacion.value)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Presentación registrada', life: 3000 });
            hideDialog();
            emit('presentacion-agregada');
        })
        .catch(error => {
            if (error.response && error.response.status === 422) {
                serverErrors.value = error.response.data.errors || {};
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'No se pudo registrar la presentación',
                    life: 3000
                });
            }
        });
}
</script>
