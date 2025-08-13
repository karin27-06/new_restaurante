<script setup>
import { ref, watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';
import InputNumber from 'primevue/inputnumber';

const props = defineProps({
    visible: Boolean,
    reporteId: Number
});
const emit = defineEmits(['update:visible', 'updated']);

const toast = useToast();
const serverErrors = ref({});
const submitted = ref(false);
const loading = ref(false);

const dialogVisible = ref(props.visible);

watch(() => props.visible, (val) => dialogVisible.value = val);
watch(dialogVisible, (val) => emit('update:visible', val));

const reporte = ref({
    monto_efectivo: '',
    monto_tarjeta: '',
    monto_yape: '',
    monto_transferencia: '',
    vendedorNombre: '',
    numero_cajas: ''
});

watch(() => props.visible, async (val) => {
    if (val && props.reporteId) {
        await fetchReporte();
    }
});

const fetchReporte = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(`/reporte_caja/${props.reporteId}`);
        const r = data.reportecaja;
        reporte.value = {
            monto_efectivo: r.monto_efectivo,
            monto_tarjeta: r.monto_tarjeta,
            monto_yape: r.monto_yape,
            monto_transferencia: r.monto_transferencia,
            vendedorNombre: r.vendedorNombre,
            numero_cajas: r.numero_cajas,
        };
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'No se pudo cargar el reporte',
            life: 3000
        });
    } finally {
        loading.value = false;
    }
};

const updateReporte = async () => {
    submitted.value = true;
    serverErrors.value = {};

    try {
        const dataToSend = {
            monto_efectivo: reporte.value.monto_efectivo,
            monto_tarjeta: reporte.value.monto_tarjeta,
            monto_yape: reporte.value.monto_yape,
            monto_transferencia: reporte.value.monto_transferencia
        };

        await axios.put(`/reporte_caja/${props.reporteId}`, dataToSend);

        toast.add({
            severity: 'success',
            summary: 'Actualizado',
            detail: 'Reporte de caja actualizado correctamente',
            life: 3000
        });

        dialogVisible.value = false;
        emit('updated');
    } catch (error) {
        if (error.response?.status === 422 && error.response?.data?.message) {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: error.response.data.message,
                life: 3000
            });
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'No se pudo actualizar el reporte',
                life: 3000
            });
        }
    }
};
</script>

<template>
  <Dialog v-model:visible="dialogVisible" header="Editar Reporte de Caja" modal :closable="true" :closeOnEscape="true" :style="{ width: '500px' }">
    <div class="flex flex-col gap-6">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-12">
                <label class="block font-bold mb-2">Vendedor</label>
                <InputText v-model="reporte.vendedorNombre" readonly fluid />
            </div>
            <div class="col-span-12">
                <label class="block font-bold mb-2">N° Caja</label>
                <InputText v-model="reporte.numero_cajas" readonly fluid />
            </div>
            <div class="col-span-6">
                <label class="block font-bold mb-2">Efectivo</label>
                <InputNumber
                    v-model="reporte.monto_efectivo"
                    :minFractionDigits="2"
                    :maxFractionDigits="2"
                    mode="currency"
                    currency="PEN"
                    locale="es-PE"
                    fluid />
            </div>
            <div class="col-span-6">
                <label class="block font-bold mb-2">Tarjeta</label>
                <InputNumber
                    v-model="reporte.monto_tarjeta"
                    :minFractionDigits="2"
                    :maxFractionDigits="2"
                    mode="currency"
                    currency="PEN"
                    locale="es-PE"
                    fluid />
            </div>
            <div class="col-span-6">
                <label class="block font-bold mb-2">Yape/Plin</label>
                <InputNumber
                    v-model="reporte.monto_yape"
                    :minFractionDigits="2"
                    :maxFractionDigits="2"
                    mode="currency"
                    currency="PEN"
                    locale="es-PE"
                    fluid />
            </div>
            <div class="col-span-6">
                <label class="block font-bold mb-2">Transferencia</label>
                <InputNumber
                    v-model="reporte.monto_transferencia"
                    :minFractionDigits="2"
                    :maxFractionDigits="2"
                    mode="currency"
                    currency="PEN"
                    locale="es-PE"
                    fluid />
            </div>
        </div>
    </div>
    <template #footer>
        <Button label="Cancelar" icon="pi pi-times" text @click="dialogVisible = false" />
        <Button label="Guardar" icon="pi pi-check" @click="updateReporte" :loading="loading" />
    </template>
  </Dialog>
</template>
