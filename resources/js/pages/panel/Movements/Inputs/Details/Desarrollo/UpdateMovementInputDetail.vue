<script setup>
import Select from 'primevue/select';
import SelectButton from 'primevue/selectbutton';
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import Button from 'primevue/button';
import InputDate from 'primevue/datepicker';
import Dialog from 'primevue/dialog';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Toolbar from 'primevue/toolbar';
import { useToast } from 'primevue/usetoast';
const { id } = usePage().props;
const inputDialog = ref(false);
const props = defineProps({
    visible: Boolean,
    movementInputId: Number,
});


const emit = defineEmits(['update:visible', 'updated']);

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
    batch: '',
    idMovementInput: '',
});


const clearSearch = () => {
    searchTerm.value = ''; // Vaciar el campo de búsqueda
};
watch(
    () => props.visible,
    async (val) => {
        if (val && props.movementInputId) {
            await fetchMovementInput();
        }
    },
);
const fetchMovementInput = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(`/insumos/movimientos/detalle/${props.movementInputId}`);
        
        // Iterar sobre los elementos en data
        data.data.forEach((item) => {
            if (item.id === props.movementInputId) {
                movementInput.value = {
                    batch: item.batch,  
                    quantity: item.quantity,
                    expirationDate: item.expirationDate,
                    totalPrice: item.totalPrice,
                    unitPrice: item.priceUnit,
                    idMovementInput: item.idMovementInput,
                };
                                console.log('Movimiento actualizado:', movementInput.value);

                // Preseleccionar insumo basado en idInput
                if (item.input) {
                    selectedInsumo.value = item.input; // Asignar el insumo correspondiente
                    searchTerm.value = item.input.name; // Inicializar la búsqueda con el nombre del insumo
                }
            }
        });

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

const insumos = ref([]); // Aquí almacenamos los resultados de la búsqueda
const searchTerm = ref('');
const showResults = ref(false);
const insumosOptions = ref([]);
const timeoutId = ref(null);
const selectedInsumo = ref(null);

function hideDialog() {
    // Cerrar el diálogo
    dialogVisible.value = false;
    inputDialog.value = false;



    // Limpiar la búsqueda y la selección
    clearSearch();
    selectedInsumo.value = null; 
    showResults.value = false; 
}
const saveMovement = async () => {
    loading.value = true;
    try {
        if (!movementInput.value.batch || !movementInput.value.quantity || !movementInput.value.expirationDate || !movementInput.value.totalPrice || !movementInput.value.unitPrice) {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Todos los campos son obligatorios.',
                life: 3000,
            });
            return; 
        }

        const formData = {
            idMovementInput: movementInput.value.idMovementInput,
            idInput: selectedInsumo.value ? selectedInsumo.value.id : null,
            quantity: movementInput.value.quantity,
            totalPrice: movementInput.value.totalPrice,
            priceUnit: movementInput.value.unitPrice,
            batch: movementInput.value.batch,
            expirationDate: movementInput.value.expirationDate, 
        };

        console.log(formData);

        const response = await axios.put(`/insumos/movimientos/detalle/${props.movementInputId}`, formData);

        if (response.data.state) {
            toast.add({
                severity: 'success',
                summary: 'Éxito',
                detail: 'Detalle de movimiento actualizado correctamente.',
                life: 3000,
            });
            emit('updated');
            await updateKardex();
            hideDialog();
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'No se pudo actualizar el detalle del movimiento.',
                life: 3000,
            });
        }

    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Ocurrió un error al actualizar el detalle del movimiento.',
            life: 3000,
        });
    } finally {
        loading.value = false;
    }
};

const updateKardex = async () => {
    try {
        const idMovementInput = movementInput.value.idMovementInput;
        const idInput = selectedInsumo.value ? selectedInsumo.value.id : null;

        const response = await axios.get(`/insumos/karde?idMovementInput=${idMovementInput}&idInput=${idInput}`);

        const id = response.data.data[0].id;
        const formDataKardex = {
            idInput: idInput, 
            idMovementInput: idMovementInput, 
            totalPrice: movementInput.value.totalPrice, 
        };

        console.log(formDataKardex); 

        const updateResponse = await axios.put(`/insumos/karde/${id}`, formDataKardex);

      
    } catch (error) {
    }
};










</script>

<template>
    <Dialog
        v-model:visible="dialogVisible"
        header="Editar Movimiento de Insumo"
        modal
        :closable="true"
        :closeOnEscape="true"
        :style="{ width: '500px' }"
          @hide="hideDialog" 
    >
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
                        mode="decimal"
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
