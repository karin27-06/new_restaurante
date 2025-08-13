<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nuevo Movimiento" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
        </template>
    </Toolbar>

     <Dialog v-model:visible="inputDialog" :style="{ width: '700px' }" header="Movimiento de Insumo" :modal="true">
        <div class="flex flex-col gap-6">
            <!-- Tipo de Documento -->
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <label class="mb-2 block font-bold">Tipo de Documento <span class="text-red-500">*</span></label>
                    <SelectButton v-model="movementInput.documentType" :options="documentTypes" optionLabel="label" optionValue="value" />
               <br>
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
                        :options="customers"
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
           
           <br>
            <small v-if="serverErrors.payment_type" class="text-red-500">{{ serverErrors.payment_type[0] }}</small>

                </div>
            </div>

            <!-- Incluir IGV -->
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <label class="mb-2 block font-bold">Incluir IGV <span class="text-red-500">*</span></label>
                    <SelectButton v-model="movementInput.igvState" :options="igvOptions" optionLabel="label" optionValue="value" />
           
           <br>
            <small v-if="serverErrors.igv_state" class="text-red-500">{{ serverErrors.igv_state[0] }}</small>

                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
            <Button label="Guardar" icon="pi pi-check" @click="saveMovement" />
        </template>
    </Dialog>
</template>

<script setup>

import axios from 'axios';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import RadioButton from 'primevue/radiobutton';
import Select from 'primevue/select';
import Dropdown from 'primevue/dropdown';  // Importamos Dropdown
import Tag from 'primevue/tag';
import Toolbar from 'primevue/toolbar';
import { useToast } from 'primevue/usetoast';
import { ref } from 'vue';
import InputNumber from 'primevue/inputnumber';
import ToolsInput from './toolsInput.vue';
import InputDate from 'primevue/datepicker';
import SelectButton from 'primevue/selectbutton';

const toast = useToast();
const submitted = ref(false);
const inputDialog = ref(false);
const serverErrors = ref({});
const emit = defineEmits(['inputs-agregado','movementsinputAgregado']);
const selectedMovementInputs = ref([]);  // Si es un array vacío, de esta forma lo defines.
    import {
        router
    } from '@inertiajs/core';


const movementInput = ref({
    code: null,                 
    issueDate: null,        
    executionDate: null,       
    supplierId: null,         
    movementType: null,          
    state: true,              
    igvState: null,              
    paymentType: '',         
});

// Variable para guardar todos los datos de acuerdo a las columnas de la tabla
const movementData = ref({
    code: null,
    issue_date: null,
    execution_date: null,
    supplier_id: null,
    user_id: null,
    movement_type: null,
    state: true,
    igv_state: null,
    payment_type: null,
});

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

function openNew() {
    fetchCustomers();
    inputDialog.value = true;
}

function hideDialog() {
    inputDialog.value = false;
}

async function fetchUserId() {
    try {
        // Hacemos la solicitud al backend para obtener el user_id
        const { data } = await axios.get('/user-id');

        // Verificamos si la solicitud fue exitosa
        if (data.success) {
            return data.user_id; // Retornamos el user_id
        } else {
            console.error("Error al obtener el user_id");
            return null;
        }
    } catch (e) {
        console.error("Error en la solicitud:", e);
        return null;
    }
}



async function saveMovement() {
   const userId = await fetchUserId(); // Esperar a obtener el user_id

    if (!userId) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo obtener el user_id' });
        return; // Si no se obtiene el user_id, no continuar con el registro
    }

    movementData.value = {
        code: movementInput.value.code,
        issue_date: formatDate(movementInput.value.issueDate), 
        execution_date: formatDate(movementInput.value.executionDate),
        supplier_id: movementInput.value.supplierId, 
        user_id: userId,  // Aquí se agrega el user_id
        movement_type: getMovementTypeValue(movementInput.value.documentType),
        state: movementInput.value.state, // true o false
        igv_state: getIgvStateValue(movementInput.value.igvState),
        payment_type: movementInput.value.paymentType,
    };

    console.log("Datos enviados:", movementData.value); 

    axios.post('/insumos/movimiento', movementData.value)
        .then(response => {
           
            // Si la solicitud es exitosa
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Movimiento registrado correctamente',
            life: 3000 });

            // Obtener el ID del nuevo movimiento
            const movementId = response.data.movement.id;
            console.log("ID del nuevo movimiento:", movementId);

            // Cerrar el formulario y resetear los datos
            hideDialog();
            resetForm();

            // Redirigir a la URL con el ID del nuevo movimiento
            emit('movementsinput-agregado');
 const url = `/insumos/movimientos/detalles/${movementId}`;
                router.visit(url);


        })
        .catch(error => {
            if (error.response?.status === 422) {
                serverErrors.value = error.response.data.errors || {};
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'No se pudo registrar el movimiento de insumos',
                    life: 3000
                });
            }
        });
}



function formatDate(date) {
    if (!date) return null;
    const d = new Date(date);
    return d.toISOString().split('T')[0];  // Formato "YYYY-MM-DD"
}

function getMovementTypeValue(type) {
    const types = {
        'FACTURA': 1,
        'GUIA': 2,
        'BOLETA': 3
    };
    return types[type] || 0;  // Default to 0 if the type is invalid
}

function getIgvStateValue(igvState) {
    if (igvState === undefined || igvState === null) {
        return null;  // Deja vacío si no tiene datos
    }
    return igvState === 'INCLUIDO' ? 0 : 1;  // Convert 'INCLUIDO' to 0, 'NO INCLUIDO' to 1
}
// Función para resetear los campos del formulario
function resetForm() {
    movementInput.value = {
        code: '',
        issueDate: null,
        executionDate: null,
        supplierId: null,
        movementType: '',
        state: true,
        igvState:null,
        paymentType: ''
    };
}
</script>
