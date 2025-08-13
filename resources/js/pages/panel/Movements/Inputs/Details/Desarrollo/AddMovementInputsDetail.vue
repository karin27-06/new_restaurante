<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nuevo Insumo" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
            <Button label="Volver" icon="pi pi-arrow-left" severity="secondary" class="mr-2" @click="goBack" />
        </template>
    </Toolbar>

    <Dialog v-model:visible="inputDialog" :style="{ width: '500px' }" header="NUEVO INSUMO" :modal="true">
        <div class="flex flex-col gap-6">
            <div class="relative w-full">
                <!-- InputText para búsqueda -->
                <InputText v-model="searchTerm" @input="handleSearch" placeholder="Buscar insumo..." class="w-full" />

                <!-- Resultados del autocompletado -->
                <div
                    v-if="showResults && insumosOptions.length > 0"
                    class="absolute z-50 mt-2 max-h-60 w-full overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 p-2 shadow-lg"
                >
                    <div
                        v-for="insumo in insumosOptions"
                        :key="insumo.id"
                        @click="selectInsumo(insumo)"
                        class="cursor-pointer rounded p-2 hover:bg-primary-100 hover:text-primary-800"
                    >
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-800">{{ insumo.id }} - {{ insumo.name }}</span>
                            <span class="text-sm text-gray-600">{{ insumo.quantityUnitMeasure }}{{ insumo.unitMeasure }}</span>
                        </div>
                    </div>
                </div>

                <!-- Mensaje de no encontrados -->
                <div
                    v-if="showResults && searchTerm && insumosOptions.length === 0"
                    class="absolute z-50 mt-2 w-full rounded-lg border border-gray-200 bg-gray-50 p-4 text-center text-gray-600 shadow-lg"
                >
                    No se encontraron resultados
                </div>
            </div>

            <!-- Insumo seleccionado -->
            <div v-if="selectedInsumo" class="mt-4 flex items-center justify-between rounded-lg bg-primary-50 p-3">
                <span class="font-medium text-primary-800"
                    >{{ selectedInsumo.id }} - {{ selectedInsumo.name }} - {{ selectedInsumo.quantityUnitMeasure
                    }}{{ selectedInsumo.unitMeasure }}</span
                >
                <Button icon="pi pi-times" @click="clearSelection" />
            </div>
            <!-- Número de Lote -->
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <label class="mb-2 block font-bold">Número de Lote <span class="text-red-500">*</span></label>
                    <InputText v-model="movementInput.batch" required maxlength="20" fluid :class="{ 'p-invalid': serverErrors.batch }" />
                    <small v-if="serverErrors.batch" class="text-red-500">{{ serverErrors.batch[0] }}</small>
                </div>
            </div>

            <!-- Fecha de Vencimiento -->
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-6">
                    <label class="mb-2 block font-bold">Fecha de Vencimiento <span class="text-red-500">*</span></label>
                    <InputDate v-model="movementInput.expirationDate" required class="w-full" dateFormat="dd/mm/yy" showIcon/>
                    <small v-if="serverErrors.expirationDate" class="text-red-500">{{ serverErrors.expirationDate[0] }}</small>
                </div>
            </div>

            <!-- Cantidad -->
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <label class="mb-2 block font-bold">Cantidad <span class="text-red-500">*</span></label>
                    <InputNumber v-model="movementInput.quantity" required min="1" class="w-full" :class="{ 'p-invalid': serverErrors.quantity }" />
                    <small v-if="serverErrors.quantity" class="text-red-500">{{ serverErrors.quantity[0] }}</small>
                </div>
            </div>

            <!-- Precio Total -->
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <label class="mb-2 block font-bold">Precio Total <span class="text-red-500">*</span></label>
                    <InputNumber
                        v-model="movementInput.totalPrice"
                        required
                        min="0"
                        minFractionDigits="2" 
                        maxFractionDigits="2"
                        class="w-full"
                        :class="{ 'p-invalid': serverErrors.totalPrice }"
                    />
                    <small v-if="serverErrors.totalPrice" class="text-red-500">{{ serverErrors.totalPrice[0] }}</small>
                </div>
            </div>

            <!-- Precio Unitario (Deshabilitado) -->
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <label class="mb-2 block font-bold">Precio Unitario</label>
                    <InputText v-model="movementInput.unitPrice" disabled fluid />
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
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import Button from 'primevue/button';
import InputDate from 'primevue/datepicker';
import Dialog from 'primevue/dialog';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Toolbar from 'primevue/toolbar';
import { useToast } from 'primevue/usetoast';
import { ref, watch } from 'vue';
const { id } = usePage().props;
const toast = useToast();
const submitted = ref(false);
const inputDialog = ref(false);

const emit = defineEmits(['movementsinputAgregado']);

const serverErrors = ref({}); // Para manejar errores de validación
const insumos = ref([]); // Aquí almacenamos los resultados de la búsqueda
const searchTerm = ref('');
const showResults = ref(false);
const insumosOptions = ref([]);
const timeoutId = ref(null);
const selectedInsumo = ref(null);
const movementInput = ref({
    inputName: '', // Aquí se almacenará el nombre del insumo seleccionado
    batch: '',
    quantity: null,
    expirationDate: null,
    totalPrice: null,
    unitPrice: '',
});
// Watcher para calcular el precio unitario
watch([() => movementInput.value.quantity, () => movementInput.value.totalPrice], () => {
    if (movementInput.value.quantity && movementInput.value.totalPrice) {
        movementInput.value.unitPrice = (movementInput.value.totalPrice / movementInput.value.quantity).toFixed(2);
    }
});

// Función de búsqueda para insumos
const handleSearch = async () => {
    if (timeoutId.value) clearTimeout(timeoutId.value);
    showResults.value = true;
    timeoutId.value = setTimeout(async () => {
        try {
            if (searchTerm.value.trim()) {
                const response = await axios.get('/insumo', {
                    params: {
                        search: searchTerm.value, // Se pasa el término de búsqueda
                        state: 1
                    },
                });

                if (response.data && Array.isArray(response.data.data)) {
                    insumosOptions.value = response.data.data; // Guardamos los resultados en insumosOptions
                }
            } else {
                insumosOptions.value = []; // Limpiamos los resultados si no hay texto
            }
        } catch (error) {
            console.error('Error en la búsqueda:', error);
        }
    }, 300);
};

// Función para seleccionar un insumo de la lista
const selectInsumo = (insumo) => {
    selectedInsumo.value = insumo; // Asignamos el insumo seleccionado
    searchTerm.value = insumo.name; // Mostramos el nombre del insumo seleccionado en el input
    insumosOptions.value = []; // Limpiamos las opciones después de la selección
    showResults.value = false; // Ocultamos los resultados
};

// Función para limpiar la selección
const clearSelection = () => {
    selectedInsumo.value = null;
    searchTerm.value = '';
    insumosOptions.value = [];
    showResults.value = false;
};

function openNew() {
    inputDialog.value = true;
}


    import {
        router
    } from '@inertiajs/core';

function goBack() {

 const url = `/insumos/movimientos`;
                router.visit(url);
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



const saveMovement = async () => {
    try {
        const expirationDate = new Date(movementInput.value.expirationDate);

        // Enviar los datos al backend
        const response = await axios.post('/insumos/movimientos/detalle', {
            idMovementInput: id, // ID del movimiento
            idInput: selectedInsumo.value.id, // ID del insumo
            quantity: movementInput.value.quantity, // Cantidad
            totalPrice: movementInput.value.totalPrice, // Precio total
            priceUnit: movementInput.value.unitPrice, // Precio unitario
            batch: movementInput.value.batch, // Lote
            expirationDate: expirationDate, // Fecha de vencimiento
        });

        if (response.data.state) {
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Insumo agregado correctamente al Movimiento',
            life: 3000});
            emit('movementsinput-agregado');
            // Aquí pasas los valores correctos de totalPrice y idInput a la función enviarkardexinputs
            enviarkardexinputs(selectedInsumo.value.id, movementInput.value.totalPrice); 
            hideDialog();
        }
    } catch (error) {
        // Manejo de errores
        toast.add({ severity: 'error', summary: 'Error', detail: 'Hubo un error al guardar los datos',
            life: 3000});
    }
};

const enviarkardexinputs = async (idInput, totalPrice) => {
    const userId = await fetchUserId(); // Esperar a obtener el user_id

    if (!userId) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo obtener el user_id' });
        return; // Si no se obtiene el user_id, no continuar con el registro
    }

    try {
        // Obtener los detalles del movimiento de insumo
        const movementResponse = await axios.get(`/insumos/movimientos/detalle/${id}`);
        
        // Extraer el code y payment_type desde el movimiento
        const movementInput = movementResponse.data.data[0].movementInput; // Suponiendo que solo tienes un movimiento
        const code = movementInput.code;
        const payment_type = movementInput.payment_type;

        // Crear los datos para el kardex
        const movementDataKardex = {
            idUser: userId,
            idInput: idInput, // Recibido como parámetro
            idMovementInput: id, // Asegúrate de que este id esté correctamente definido
            movement_type: "0", // Asegúrate de que movement_type esté presente en el movimiento
            totalPrice: totalPrice, // Recibido como parámetro
        };

        // Enviar los datos para registrar el Kardex
        const response = await axios.post('/insumos/karde', movementDataKardex);
        console.log('Kardex registrado correctamente:', response.data);
    } catch (error) {
        console.error('Error al registrar el kardex:', error);
        if (error.response && error.response.data && error.response.data.errors) {
            console.error('Errores de validación:', error.response.data.errors);
        } else {
            console.error('Error desconocido:', error);
        }
    }
};



function hideDialog() {
    inputDialog.value = false;
    // Restablecer el formulario después de agregar el insumo
    movementInput.value = {
        inputName: '',
        batch: '',
        quantity: null,
        expirationDate: null,
        totalPrice: null,
        unitPrice: '',
    };
    clearSearch();
    selectedInsumo.value = null;
}
const clearSearch = () => {
    searchTerm.value = ''; // Vaciar el campo de búsqueda
};
</script>
