<template>


 <Panel header="DETALLE DE MOVIMIENTO" class="p-mt-3">
    <div class="p-grid p-fluid">
        <!-- Código y Fecha de Emisión -->
        <div class="p-col-12 p-md-2 p-lg-4">
            <div class="p-field p-d-flex p-ai-center">
                <label class="p-mr-2"><strong>Código:</strong></label>
                {{ movement.code }}
            </div>
            <div class="p-field p-d-flex p-ai-center">
                <label class="p-mr-2"><strong>Fecha de Emisión:</strong></label>
                {{ movement.issue_date }}
            </div>
        </div>

        <!-- Proveedor y Fecha de Ejecución -->
        <div class="p-col-12 p-md-6 p-lg-4">
            <div class="p-field p-d-flex p-ai-center">
                <label class="p-mr-2"><strong>Proveedor:</strong></label>
                {{ movement.supplier_name }}
            </div>
            <div class="p-field p-d-flex p-ai-center">
                <label class="p-mr-2"><strong>Fecha de Ejecución:</strong></label>
                {{ movement.execution_date }}
            </div>
        </div>

        <!-- Tipo de Movimiento y Tipo de Pago -->
        <div class="p-col-12 p-md-6 p-lg-4">
            <div class="p-field p-d-flex p-ai-center">
                <label class="p-mr-2"><strong>Tipo de Movimiento:</strong></label>
                {{ getMovementType(movement.movement_type) }}
            </div>
            <div class="p-field p-d-flex p-ai-center">
                <label class="p-mr-2"><strong>Tipo de Pago:</strong></label>
                {{ movement.payment_type }}
            </div>
        </div>
    </div>
</Panel>
    <Card class="p-4  mr-4">
        <template #content>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg">DETALLE DE {{ data.tipoMovimiento }} N°: {{ data.codigo }}</h3>
                <div class="flex space-x-3">
                    <Button label="Nuevo" icon="pi pi-plus" severity="contrast" @click="openNewModal"
                        class="px-4 py-2" />
                    <Button label="Volver" severity="secondary" @click="goBack" class="px-4 py-2" />
                </div>
            </div>
        </template>
        <template #footer>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <span class="font-bold">CODIGO:</span>
                            <pre class="ml-2">{{ data.codigo }}</pre>
                        </div>
                        <div class="flex items-center">
                            <span class="font-bold">FECHA DE EMISION:</span>
                            <pre class="ml-2">{{ data.fechaEmision }}</pre>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <span class="font-bold">PROVEEDOR:</span>
                            <pre class="ml-2">{{ data.name }}</pre>
                        </div>
                        <div class="flex items-center">
                            <span class="font-bold">FECHA DE EJECUCION:</span>
                            <pre class="ml-2">{{ data.fechaEjecucion }}</pre>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div v-if="data.fechaCredito !== '00-00-0000'" class="flex items-center">
                            <span class="font-bold">FECHA DE CREDITO:</span>
                            <pre class="ml-2">{{ data.fechaCredito }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </template>

    </Card>

</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3'; 
const { id } = usePage().props;
import Panel from 'primevue/panel';
import Button from 'primevue/button';
import Toolbar from 'primevue/toolbar';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
const toast = useToast();


const movement = ref({
    code: '',
    issue_date: '',
    execution_date: '',
    supplier_name: '',
    movement_type: 0,
    payment_type: ''
});

// Función para obtener el tipo de movimiento
function getMovementType(type: number) {
    switch(type) {
        case 1:
            return 'Factura';
        case 2:
            return 'Guía';
        case 3:
            return 'Boleta';
        default:
            return 'Desconocido';
    }
}

// Función para cargar los datos del movimiento desde la API
async function loadMovementDetails() {
    try {
        const response = await axios.get(`/insumos/movimiento/${id}`);
        if (response.data.state) {
            movement.value = response.data.movement;
        } else {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Movimiento no encontrado.' });
                        window.location.href = '/insumos/movimientos/';

        }
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error al cargar los datos del movimiento.' });
                    window.location.href = '/insumos/movimientos/';

    }
}

// Funciones para el comportamiento de los botones
function openNew() {
    inputDialog.value = true;
}
function back() {
                    window.location.href = '/insumos/movimientos/';

}

// Cargar los detalles al montar el componente
onMounted(() => {
    loadMovementDetails();
});
</script>

<style scoped>
.p-field {
    margin-bottom: 1.5rem;
}

p {
    font-size: 1rem;
    margin-top: 0.2rem;
    font-weight: 500;
}
.p-grid.p-fluid {
    display: flex
;
    flex-direction: row;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: space-evenly;
}
</style>
