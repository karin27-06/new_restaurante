<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nuevo Piso" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
        </template>
        <template #end>
            <!-- ToolsFloor para los botones de exportar e importar -->
            <ToolsFloor @import-success="loadPiso"/>       
        </template>
    </Toolbar>

    <Dialog v-model:visible="pisoDialog" :style="{ width: '600px' }" header="Registro de Piso" :modal="true">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-10">
                    <label for="name" class="block font-bold mb-3">Nombre <span class="text-red-500">*</span></label>
                    <InputText id="name" v-model.trim="piso.name" required maxlength="100" fluid
                        :class="{ 'p-invalid': submitted && !piso.name || serverErrors.name }" />
                    <small v-if="submitted && !piso.name" class="text-red-500">El nombre es obligatorio.</small>
                    <small v-else-if="submitted && piso.name && piso.name.length < 2" class="text-red-500">El nombre
                        debe
                        tener al menos 2 caracteres.</small>
                    <small v-else-if="serverErrors.name" class="text-red-500">{{ serverErrors.name[0] }}</small>
                </div>

                <div class="col-span-2">
                    <label for="state" class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="piso.state" :binary="true" inputId="state" />
                        <Tag :value="piso.state ? 'Activo' : 'Inactivo'"
                            :severity="piso.state ? 'success' : 'danger'" />
                    </div>
                </div>

                <div class="col-span-12">
                    <label for="description" class="block font-bold mb-3">Descripción</label>
                    <Textarea id="description" fluid v-model="piso.description" maxlength="255" rows="4" autoResize
                        :class="{ 'p-invalid': serverErrors.description }" />
                    <small v-if="serverErrors.description" class="text-red-500">{{ serverErrors.description[0]
                        }}</small>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
            <Button label="Guardar" icon="pi pi-check" @click="guardarPiso" :loading="loading" />
        </template>
    </Dialog>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import Toolbar from 'primevue/toolbar';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Checkbox from 'primevue/checkbox';
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';
import { defineEmits } from 'vue';
import ToolsFloor from './toolsFloor.vue';

const toast = useToast();
const submitted = ref(false);
const loading = ref(false);
const pisoDialog = ref(false);
const serverErrors = ref({});

const emit = defineEmits(['piso-agregado']);

const piso = ref({
    name: '',
    description: '',
    state: true
});
// Método para recargar la lista de pisos
const loadPiso = async () => {
    try {
        const response = await axios.get('/piso');  // Aquí haces una solicitud GET para obtener los pisos
        console.log(response.data);
        // Realiza lo que necesites con la respuesta, como actualizar el listado en un componente superior
        emit('piso-agregada');  // Si quieres que un componente padre reciba la notificación de la actualización
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar los pisos', life: 3000 });
        console.error(error);
    }
}
function resetPiso() {
    piso.value = {
        name: '',
        description: '',
        state: true
    };
    serverErrors.value = {};
    submitted.value = false;
    loading.value = false;
}

function openNew() {
    resetPiso();
    pisoDialog.value = true;
}

function hideDialog() {
    pisoDialog.value = false;
    resetPiso();
}

async function guardarPiso() {
    submitted.value = true;
    serverErrors.value = {};

    if (!piso.value.name || piso.value.name.length < 2) {
        // no enviar si no cumple validacion basica
        return;
    }

    loading.value = true;

    try {
        await axios.post('/piso', piso.value);

        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Piso registrado', life: 3000 });
        hideDialog();
        emit('piso-agregado');
    } catch (error) {
        if (error.response && error.response.status === 422) {
            serverErrors.value = error.response.data.errors || {};
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'No se pudo registrar el piso',
                life: 3000
            });
        }
    } finally {
        loading.value = false;
    }
}
</script>

<style scoped></style>
