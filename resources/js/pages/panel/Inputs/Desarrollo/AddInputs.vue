<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nuevo Insumo" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
        </template>
        <template #end>
            <!-- ToolsInput para los botones de exportar e importar -->
            <ToolsInput @import-success="loadInsumo" />
        </template>
    </Toolbar>

    <Dialog v-model:visible="inputDialog" :style="{ width: '700px' }" header="Registro de Insumos" :modal="true">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <!-- Nombre -->
                <div class="col-span-10">
                    <label class="mb-2 block font-bold">Nombre <span class="text-red-500">*</span></label>
                    <InputText v-model="input.name" required maxlength="100" fluid :class="{ 'p-invalid': serverErrors.name }" />
                    <small v-if="serverErrors.name" class="p-error">{{ serverErrors.name[0] }}</small>
                </div>

                <!-- Estado -->
                <div class="col-span-2">
                    <label class="mb-2 block font-bold">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="input.state" :binary="true" fluid />
                        <Tag :value="input.state ? 'Activo' : 'Inactivo'" :severity="input.state ? 'success' : 'danger'" />
                    </div>
                </div>
                <!-- precio -->

                <div class="col-span-6">
                    <label for="priceSale" class="mb-2 block font-bold">Precio Venta<span class="text-red-500">*</span></label>
                    <InputNumber
                        id="priceSale"
                        v-model="input.priceSale"
                        :minFractionDigits="2"
                        :maxFractionDigits="2"
                        mode="currency"
                        currency="PEN"
                        locale="es-PE"
                        class="w-full"
                        :class="{ 'p-invalid': serverErrors.priceSale }"
                    />
                    <small v-if="serverErrors.priceSale" class="p-error">{{ serverErrors.priceSale[0] }}</small>
                </div>

                <!-- Almacén (Dropdown con búsqueda) -->
                <div class="col-span-6">
                    <label class="mb-2 block font-bold">Almacen <span class="text-red-500">*</span></label>
                    <Dropdown
                        v-model="input.idAlmacen"
                        fluid
                        :options="almacens"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Seleccione Almacen"
                        filter
                        filterBy="label" 
                        filterPlaceholder="Buscar almacen..." 
                        style="width: 325px;" 
                    />
                    <small v-if="submitted && !input.idAlmacen" class="text-red-500">El Almacen es obligatorio.</small>
                    <small v-else-if="serverErrors.idAlmacen" class="text-red-500">{{ serverErrors.idAlmacen[0] }}</small>
                </div>
                <!-- Cantidad por medida -->

              <div class="col-span-6">
    <label for="quantityUnitMeasure" class="mb-2 block font-bold">Cantidad por medida<span class="text-red-500">*</span></label>
    <InputNumber
        id="quantityUnitMeasure"
        v-model="input.quantityUnitMeasure"
        :minFractionDigits="2"
        :maxFractionDigits="2"
        class="w-full"
        :class="{ 'p-invalid': serverErrors.quantityUnitMeasure }"
    />
    <small v-if="serverErrors.quantityUnitMeasure" class="p-error">{{ serverErrors.quantityUnitMeasure[0] }}</small>
</div>

<!-- Unidad de Medida -->
                <div class="col-span-6">
                    <label class="mb-2 block font-bold">Unidad de Medida <span class="text-red-500">*</span></label>
                    <Select
                        v-model="input.unitMeasure"
                        fluid
                        :options="unitMeasures"
                        optionLabel="label"
                        optionValue="value"
                        placeholder="Seleccione Unidad de Medida"
                    />
                    <small v-if="serverErrors.unitMeasure" class="p-error">{{ serverErrors.unitMeasure[0] }}</small>
                </div>
             

                

                <!-- description -->
                <div class="col-span-12">
                    <label class="mb-2 block font-bold">Descripcion <span class="text-red-500">*</span></label>
                    <InputText v-model="input.description" required maxlength="150" fluid :class="{ 'p-invalid': serverErrors.description }" />
                    <small v-if="serverErrors.description" class="p-error">{{ serverErrors.description[0] }}</small>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
            <Button label="Guardar" icon="pi pi-check" @click="guardarInput" />
        </template>
    </Dialog>
</template>

<script setup>
import axios from 'axios';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Dialog from 'primevue/dialog';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Tag from 'primevue/tag';
import Toolbar from 'primevue/toolbar';
import { useToast } from 'primevue/usetoast';
import { ref } from 'vue';
import ToolsInput from './toolsInput.vue';
import Dropdown from 'primevue/dropdown';

const toast = useToast();
const submitted = ref(false);
const inputDialog = ref(false);
const serverErrors = ref({});
const emit = defineEmits(['inputs-agregado']);

const input = ref({
    name: '',
    priceSale: null,
    priceBuy: null,
    state: true,
    idAlmacen: null,
    description: null,
    unitMeasure: null,
    quantityUnitMeasure: null,
});

// Listado de unidades de medida
const unitMeasures = ref([
    { label: 'Kilogramos', value: 'kg' },
    { label: 'Gramos', value: 'g' },
    { label: 'Litros', value: 'litros' },
    { label: 'Mililitros', value: 'ml' },
    { label: 'Unidad', value: 'unidad' },
]);

// Método para recargar la lista de insumos
const loadInsumo = async () => {
    try {
        const response = await axios.get('/insumo'); // Aquí haces una solicitud GET para obtener los insumos
        console.log(response.data);
        // Realiza lo que necesites con la respuesta, como actualizar el listado en un componente superior
        emit('insumo-agregado'); // Si quieres que un componente padre reciba la notificación de la actualización
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar los insumos', life: 3000 });
        console.error(error);
    }
};
const almacens = ref([]);

function resetInput() {
    input.value = {
        name: '',
        priceSale: null,
        priceBuy: null,
        state: true,
        idAlmacen: null,
        description: null,
        unitMeasure: null,
        quantityUnitMeasure: null,
    };
    serverErrors.value = {};
    submitted.value = false;
}

function openNew() {
    resetInput();
    fetchAlmacens();
    inputDialog.value = true;
}

function hideDialog() {
    inputDialog.value = false;
    resetInput();
}

async function fetchAlmacens() {
    try {
        const { data } = await axios.get('/almacen', { params: { state: 1 } });
        almacens.value = data.data.map((c) => ({ label: c.name, value: c.id }));
    } catch (e) {
        toast.add({ severity: 'warn', summary: 'Advertencia', detail: 'No se pudieron cargar los almacenes' });
    }
}

function guardarInput() {
    submitted.value = true;
    serverErrors.value = {};
    const dataToSend = {
        name: input.value.name,
        priceSale: parseFloat(input.value.priceSale),
        priceBuy: null,
        state: input.value.state === true,
        idAlmacen: input.value.idAlmacen,
        description: input.value.description,
        unitMeasure: input.value.unitMeasure,
        quantityUnitMeasure: input.value.quantityUnitMeasure,
    };
    console.log(dataToSend);
    axios
        .post('/insumo', dataToSend)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Insumo registrado', life: 3000 });
            hideDialog();
            emit('inputs-agregado');
        })
        .catch((error) => {
            if (error.response?.status === 422) {
                serverErrors.value = error.response.data.errors || {};
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'No se pudo registrar el insumo',
                    life: 3000,
                });
            }
        });
}
</script>
