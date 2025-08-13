<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Select from 'primevue/select';
import { debounce } from 'lodash';
import DeleteInput from './DeleteInputs.vue';
import UpdateInput from './UpdateInputs.vue';
import MultiSelect from 'primevue/multiselect';

const inputs = ref([]);
const loading = ref(false);
const globalFilterValue = ref('');
const deleteInputDialog = ref(false);
const updateInputDialog = ref(false);
const selectedInputId = ref(null);
const input = ref({});
const currentPage = ref(1);
const selectedColumns = ref([]);
const selectedAlmacen = ref(null);
const selectedEstadoInput = ref(null);

const pagination = ref({
    currentPage: 1,
    perPage: 15,
    total: 0
});
const refreshCount = ref(0);  // Variable que se incrementa cuando se agrega un insumo

const estadoInputOptions = ref([
    { name: 'TODOS', value: '' },
    { name: 'ACTIVOS', value: 1 },
    { name: 'INACTIVOS', value: 0 },
]);

const isColumnSelected = (fieldName) => {
    return selectedColumns.value.some(col => col.field === fieldName);
};

const optionalColumns = ref([
    { field: 'tablenum', header: 'Numero' },
    { field: 'capacity', header: 'Capacidad' }

]);

const formatCurrency = (value) => {
    if (value != null) {
        return 'S/. ' + parseFloat(value).toFixed(2);
    }
    return '';
};
const loadInputs = async () => {
    loading.value = true;
    try {
        const params = {
            page: pagination.value.currentPage,
            per_page: pagination.value.perPage,
            search: globalFilterValue.value,
            almacen: selectedAlmacen?.value,
            state: selectedEstadoInput.value?.value ?? '',
        };
        const response = await axios.get('/insumo', { params });
        inputs.value = response.data.data;
        pagination.value.currentPage = response.data.meta.current_page;
        pagination.value.total = response.data.meta.total;
    } catch (error) {
        console.error('Error al cargar insumos:', error);
    } finally {
        loading.value = false;
    }
};

const props = defineProps({
    refresh: {
        type: Number,
        required: true
    }
});
// Recarga la tabla cuando `refreshCount` cambia
watch(refreshCount, loadInputs);
watch(() => props.refresh, loadInputs);
watch(() => selectedEstadoInput.value, () => {
    currentPage.value = 1;
    loadInputs();
});
watch(deleteInputDialog, (val) => {
    console.log('Dialogo eliminar visible:', val);
});

const onPage = (event) => {
    pagination.value.currentPage = event.page + 1;
    pagination.value.perPage = event.rows;
    loadInputs();
};

const onGlobalSearch = debounce(() => {
    pagination.value.currentPage = 1;
    loadInputs();
}, 500);

const getSeverity = (value) => {
    return value ? 'success' : 'danger';
};

const editarInput = (prod) => {
    selectedInputId.value = prod.id;
    updateInputDialog.value = true;
};

const confirmarDeleteInput = (prod) => {
    input.value = prod;
    deleteInputDialog.value = true;
};



function handleInputUpdated() {
    loadInputs();
}

function handleInputDeleted() {
    loadInputs();
}

onMounted(loadInputs);
</script>

<template>
    <DataTable ref="dt" v-model:selection="selectedInputs" :value="inputs" dataKey="id" :paginator="true"
        :rows="pagination.perPage" :totalRecords="pagination.total" :loading="loading" :lazy="true" @page="onPage"
        :rowsPerPageOptions="[15, 20, 25]" scrollable scrollHeight="574px"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} Insumos">
        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">Insumos</h4>
                <div class="flex flex-wrap gap-2">
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="globalFilterValue" @input="onGlobalSearch" placeholder="Buscar..." />
                    </IconField>
                    
                    <Select v-model="selectedEstadoInput" :options="estadoInputOptions" optionLabel="name" placeholder="Estado" />
                    <Button icon="pi pi-refresh" outlined rounded aria-label="Refresh" @click="loadInputs" />
                </div>
            </div>
        </template>

                <Column selectionMode="multiple" style="width: 1rem" :exportable="false"></Column>

        <Column field="name" header="Nombre" sortable style="min-width: 10rem" />

<Column field="priceSale" header="Precio Venta" sortable style="min-width: 10rem">
  <template #body="{ data }">
    {{ formatCurrency(data.priceSale) }}
  </template>
</Column>  
        <Column field="quantityUnitMeasure" header="Cantidad" sortable style="min-width: 10rem" />


<Column field="unitMeasure" header="Unidad de Medida" sortable style="min-width: 10rem">
  <template #body="{ data }">
    {{ data.unitMeasure}}
  </template>
</Column>  
      <Column field="almacen_name" header="Almacen" sortable style="min-width: 10rem" />
        <Column field="creacion" header="Creación" sortable style="min-width: 13rem" />
        <Column field="actualizacion" header="Actualización" sortable style="min-width: 13rem"/>
        <Column field="state" header="Estado" sortable>
            <template #body="{ data }">
                <Tag :value="data.state ? 'Activo' : 'Inactivo'" :severity="getSeverity(data.state)" />
            </template>
        </Column>
        <Column field="accions" header="Acciones" :exportable="false" style="min-width: 8rem">
            <template #body="{ data }">
                <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editarInput(data)" />
                <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmarDeleteInput(data)" />
            </template>
        </Column>
    </DataTable>

    <DeleteInput
        v-model:visible="deleteInputDialog"
        :input="input"
        @deleted="handleInputDeleted"
    />
    <UpdateInput
        v-model:visible="updateInputDialog"
        :inputId="selectedInputId"
        @updated="handleInputUpdated"
    />
</template>
