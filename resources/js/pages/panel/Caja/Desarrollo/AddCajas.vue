<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Agregar Cajas" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
        </template>
    </Toolbar>

    <Dialog v-model:visible="cajaDialog" :style="{ width: '500px' }" header="Registro de cajas" :modal="true">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <!-- Número de cajas -->
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Ingresa número de cajas a crear: <span class="text-red-500">*</span></label>
                    <InputText v-model.trim="caja.numero_cajas" type="number" fluid maxlength="100" />
                    <small v-if="submitted && !caja.numero_cajas" class="text-red-500">El número de cajas es obligatorio.</small>
                    <small v-else-if="serverErrors.numero_cajas" class="text-red-500">{{ serverErrors.numero_cajas[0] }}</small>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
            <Button label="Guardar" icon="pi pi-check" @click="guardarCaja" />
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
import { useToast } from 'primevue/usetoast';

const toast = useToast();
const submitted = ref(false);
const cajaDialog = ref(false);
const serverErrors = ref({});
const emit = defineEmits(['caja-agregada']);

const caja = ref({
    numero_cajas: '',  // Solo se ingresa el número de cajas
});

function resetCaja() {
    caja.value = {
        numero_cajas: '', // Inicializar en 1
    };
    serverErrors.value = {};
    submitted.value = false;
}

function openNew() {
    resetCaja();
    cajaDialog.value = true;
}

function hideDialog() {
    cajaDialog.value = false;
    resetCaja();
}

function guardarCaja() {
    submitted.value = true;
    serverErrors.value = {};

    axios.post('/caja', caja.value)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Caja registrada', life: 3000 });
            hideDialog();
            emit('caja-agregada');
        })
        .catch(error => {
            if (error.response?.status === 422) {
                serverErrors.value = error.response.data.errors || {};
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'No se pudo registrar la caja',
                    life: 3000
                });
            }
        });
}
</script>
