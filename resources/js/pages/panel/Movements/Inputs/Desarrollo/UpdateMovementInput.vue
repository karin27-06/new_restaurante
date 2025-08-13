<script setup>
import axios from 'axios';
import Button from 'primevue/button';
import InputDate from 'primevue/datepicker';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';  // Importamos Dropdown
import SelectButton from 'primevue/selectbutton';
import { useToast } from 'primevue/usetoast';
import { ref, watch } from 'vue';
    import {
        router
    } from '@inertiajs/core';

const props = defineProps({
    visible: Boolean,
    movementInputId: Number,
});
const emit = defineEmits(['update:visible', 'updated']);

// Define options for SelectButton components
const documentTypes = [
    { label: 'FACTURA', value: 'FACTURA' },
    { label: 'GUIA', value: 'GUIA' },
    { label: 'BOLETA', value: 'BOLETA' },
];

const paymentTypes = [
    { label: 'CONTADO', value: 'contado' },
    { label: 'CREDITO', value: 'credito' },
];

const igvOptions = [
    { label: 'INCLUIDO', value: 'INCLUIDO' },
    { label: 'NO INCLUIDO', value: 'NO INCLUIDO' },
];

const customers = ref([]);

async function fetchCustomers() {
    try {
        const { data } = await axios.get('/proveedor', { params: { state: 1 } });
        customers.value = data.data.map((c) => ({ label: c.name, value: c.id }));
    } catch (e) {
        toast.add({ severity: 'warn', summary: 'Advertencia', detail: 'No se pudieron cargar los Proveedores' });
    }
}

const toast = useToast();
const serverErrors = ref({});
const submitted = ref(false);
const loading = ref(false);

const dialogVisible = ref(props.visible);
watch(
    () => props.visible,
    (val) => (dialogVisible.value = val),
);
watch(dialogVisible, (val) => emit('update:visible', val));

const movementInput = ref({
    idmovement: '',
    code: '',
    issueDate: null,
    executionDate: null,
    supplierId: null,
    documentType: null,
    igvState: null,
    paymentType: '',
});

const suppliers = ref([]);

watch(
    () => props.visible,
    async (val) => {
        if (val && props.movementInputId) {
            await fetchMovementInput();
            await fetchSuppliers();
        }
    },
);

const fetchMovementInput = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(`/insumos/movimiento/${props.movementInputId}`);
        const i = data.movement;
        movementInput.value = {
            idmovement: i.id,
            code: i.code,
            issueDate: new Date(i.issue_date.split('-').reverse().join('-')), // Convierte a Date
            executionDate: new Date(i.execution_date.split('-').reverse().join('-')), // Convierte a Date
            supplierId: i.supplier_id,
            documentType: getMovementTypeValue(i.movement_type), // Asignamos el valor correspondiente
            igvState: getIgvStateValue(i.igv_state), // Asignamos el valor correspondiente
            paymentType: i.payment_type,
        };
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'No se pudo cargar los insumos',
            life: 3000,
        });
    } finally {
        loading.value = false;
    }
};

// Función para convertir el tipo de movimiento ('FACTURA', 'GUIA', 'BOLETA') a un valor numérico
function getMovementTypeValue(type) {
    const types = {
        1: 'FACTURA', // 1 es FACTURA
        2: 'GUIA', // 2 es GUIA
        3: 'BOLETA', // 3 es BOLETA
    };
    return types[type] || ''; // Devuelve el nombre correspondiente o un string vacío si no coincide
}

// Función para convertir el estado del IGV ('INCLUIDO', 'NO INCLUIDO') a un valor numérico
function getIgvStateValue(igvState) {
    const igvStates = {
        0: 'INCLUIDO', // 0 es INCLUIDO
        1: 'NO INCLUIDO', // 1 es NO INCLUIDO
    };
    return igvStates[igvState] || ''; // Devuelve el nombre correspondiente o un string vacío si no coincide
}

const fetchSuppliers = async () => {
    try {
        const { data } = await axios.get('/proveedor', { params: { state: 1 } });
        suppliers.value = data.data.map((c) => ({ label: c.name, value: c.id }));
    } catch (e) {
        toast.add({ severity: 'warn', summary: 'Advertencia', detail: 'No se pudieron cargar los proveedores' });
    }
};
const updateMovementInput = async () => {
    loading.value = true;
    try {
        // Prepara los datos para enviar
        const dataToSend = {
            code: movementInput.value.code, // Este valor debe ser un string simple
            issue_date: movementInput.value.issueDate.toISOString().split('T')[0], // Formatea solo la fecha
            execution_date: movementInput.value.executionDate.toISOString().split('T')[0], // Formatea solo la fecha
            supplier_id: movementInput.value.supplierId,
            state: true,
            user_id: 2,
            movement_type: getMovementTypeValueNum(movementInput.value.documentType), // Convierte a entero
            igv_state: getIgvStateValueNum(movementInput.value.igvState), // Convierte a entero
            payment_type: movementInput.value.paymentType,
        };



        // Enviar la solicitud PUT
        const response = await axios.put(`/insumos/movimiento/${props.movementInputId}`, dataToSend);

        if (response.status === 200) {
            toast.add({
                severity: 'success',
                summary: 'Éxito',
                detail: 'Movimiento de insumo actualizado correctamente',
                life: 3000,
            });

 const url = `/insumos/movimientos/detalles/${props.movementInputId}`;
                router.visit(url);


            // Emitir evento para cerrar el diálogo
            emit('updated');
            dialogVisible.value = false; // Cerrar el diálogo
        } else {
            throw new Error('No se pudo actualizar el movimiento de insumo');
        }
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'No se pudo actualizar el movimiento de insumo',
            life: 3000,
        });
    } finally {
        loading.value = false;
    }
};

// Función para convertir el tipo de movimiento ('FACTURA', 'BOLETA', 'GUIA') a un valor numérico
function getMovementTypeValueNum(type) {
    const types = {
        FACTURA: 1,
        GUIA: 2,
        BOLETA: 3,
    };
    return types[type] || 0; // Default to 0 if the type is invalid
}

function getIgvStateValueNum(igvState) {
    if (igvState === undefined || igvState === null) {
        return null; // Deja vacío si no tiene datos
    }
    return igvState === 'INCLUIDO' ? 0 : 1; // Convert 'INCLUIDO' to 0, 'NO INCLUIDO' to 1
}
</script>

<template>
    <Dialog
        v-model:visible="dialogVisible"
        header="Editar Movimiento de Insumo"
        modal
        :closable="true"
        :closeOnEscape="true"
        :style="{ width: '700px' }"
    >
        <div class="flex flex-col gap-6">
            <!-- Tipo de Documento -->
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <label class="mb-2 block font-bold">Tipo de Documento <span class="text-red-500">*</span></label>
                    <SelectButton v-model="movementInput.documentType" :options="documentTypes" optionLabel="label" optionValue="value" />
                    <br />
                    <small v-if="serverErrors.movement_type" class="text-red-500">{{ serverErrors.movement_type[0] }}</small>
                </div>
            </div>

            <!-- Código -->
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <label class="mb-2 block font-bold">Código <span class="text-red-500">*</span></label>
                    <InputText v-model="movementInput.code" required maxlength="20" fluid :class="{ 'p-invalid': serverErrors.code }" />
                    <small v-if="serverErrors.code" class="text-red-500">{{ serverErrors.code[0] }}</small>
                </div>
            </div>

            <!-- Fechas -->
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-6">
                    <label class="mb-2 block font-bold">Fecha de Emisión <span class="text-red-500">*</span></label>
                    <InputDate v-model="movementInput.issueDate" required class="w-full" showIcon/>
                    <small v-if="serverErrors.issue_date" class="text-red-500">{{ serverErrors.issue_date[0] }}</small>
                </div>
                <div class="col-span-6">
                    <label class="mb-2 block font-bold">Fecha de Ejecución <span class="text-red-500">*</span></label>
                    <InputDate v-model="movementInput.executionDate" required class="w-full" showIcon/>
                    <small v-if="serverErrors.execution_date" class="text-red-500">{{ serverErrors.execution_date[0] }}</small>
                </div>
            </div>

            <!-- Proveedor -->
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <label class="mb-2 block font-bold">Proveedor <span class="text-red-500">*</span></label>
                    <Dropdown
                        v-model="movementInput.supplierId"
                        fluid
                        :options="suppliers"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Seleccione un Proveedor"
                        filter
                        filterPlaceholder="Buscar proveedor"
                    />
                    <small v-if="serverErrors.supplier_id" class="text-red-500">{{ serverErrors.supplier_id[0] }}</small>
                </div>
            </div>

            <!-- Tipo de Pago -->
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <label class="mb-2 block font-bold">Tipo de Pago <span class="text-red-500">*</span></label>
                    <SelectButton v-model="movementInput.paymentType" :options="paymentTypes" optionLabel="label" optionValue="value" />

                    <br />
                    <small v-if="serverErrors.payment_type" class="text-red-500">{{ serverErrors.payment_type[0] }}</small>
                </div>
            </div>

            <!-- Incluir IGV -->
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <label class="mb-2 block font-bold">Incluir IGV <span class="text-red-500">*</span></label>
                    <SelectButton v-model="movementInput.igvState" :options="igvOptions" optionLabel="label" optionValue="value" />
                    <br />
                    <small v-if="serverErrors.igv_state" class="text-red-500">{{ serverErrors.igv_state[0] }}</small>
                </div>
            </div>
        </div>
        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="dialogVisible = false" />
            <Button label="Guardar" icon="pi pi-check" @click="updateMovementInput" :loading="loading" />
        </template>
    </Dialog>
</template>
