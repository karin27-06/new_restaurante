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
import DeleteTable from './DeleteTables.vue';
import UpdateTable from './UpdateTables.vue';
import MultiSelect from 'primevue/multiselect';

const tables = ref([]);
const loading = ref(false);
const globalFilterValue = ref('');
const deleteTableDialog = ref(false);
const updateTableDialog = ref(false);
const selectedTableId = ref(null);
const table = ref({});
const currentPage = ref(1);
const selectedColumns = ref([]);
const selectedAreas = ref(null);
const selectedFloor = ref(null);
const selectedEstadoTable = ref(null);

const pagination = ref({
    currentPage: 1,
    perPage: 15,
    total: 0
});

const estadoTableOptions = ref([
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


const loadTables = async () => {
    loading.value = true;
    try {
        const params = {
            page: pagination.value.currentPage,
            per_page: pagination.value.perPage,
            search: globalFilterValue.value,
            areas: selectedAreas?.value,
            floor: selectedFloor?.value,
            state: selectedEstadoTable.value?.value ?? '',
        };
        const response = await axios.get('/mesa', { params });
        tables.value = response.data.data;
        pagination.value.currentPage = response.data.meta.current_page;
        pagination.value.total = response.data.meta.total;
    } catch (error) {
        console.error('Error al cargar mesas:', error);
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

watch(() => props.refresh, loadTables);
watch(() => selectedEstadoTable.value, () => {
    currentPage.value = 1;
    loadTables();
});
watch(deleteTableDialog, (val) => {
    console.log('Dialogo eliminar visible:', val);
});

const onPage = (event) => {
    pagination.value.currentPage = event.page + 1;
    pagination.value.perPage = event.rows;
    loadTables();
};

const onGlobalSearch = debounce(() => {
    pagination.value.currentPage = 1;
    loadTables();
}, 500);

const getSeverity = (value) => {
    return value ? 'success' : 'danger';
};

const editarTable = (prod) => {
    selectedTableId.value = prod.id;
    updateTableDialog.value = true;
};

const confirmarDeleteTable = (prod) => {
    table.value = prod;
    deleteTableDialog.value = true;
};



function handleTableUpdated() {
    loadTables();
}

function handleTableDeleted() {
    loadTables();
}

onMounted(loadTables);
</script>

<template>
    <DataTable ref="dt" v-model:selection="selectedTables" :value="tables" dataKey="id" :paginator="true"
        :rows="pagination.perPage" :totalRecords="pagination.total" :loading="loading" :lazy="true" @page="onPage"
        :rowsPerPageOptions="[15, 20, 25]" scrollable scrollHeight="574px"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} Mesas">
        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">Mesas</h4>
                <div class="flex flex-wrap gap-2">
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="globalFilterValue" @input="onGlobalSearch" placeholder="Buscar..." />
                    </IconField>
                    
                    <Select v-model="selectedEstadoTable" :options="estadoTableOptions" optionLabel="name" placeholder="Estado" />
                    <Button icon="pi pi-refresh" outlined rounded aria-label="Refresh" @click="loadTables" />
                </div>
            </div>
        </template>

        <Column selectionMode="multiple" style="width: 1rem" />
        <Column field="name" header="Nombre" sortable style="min-width: 20rem" />
        <Column field="tablenum" header="Numero" sortable style="min-width: 20rem" />
        <Column field="capacity" header="Capacidad" sortable style="min-width: 20rem" />
        <Column field="area_name" header="Area" sortable style="min-width: 15rem" />
        <Column field="floor_name" header="Piso" sortable style="min-width: 15rem"/>
        <Column field="creacion" header="Creación" sortable style="min-width: 13rem" />
        <Column field="actualizacion" header="Actualización" sortable style="min-width: 13rem"/>
        <Column field="state" header="Estado" sortable>
            <template #body="{ data }">
                <Tag :value="data.state ? 'Activo' : 'Inactivo'" :severity="getSeverity(data.state)" />
            </template>
        </Column>
        <Column field="accions" header="Acciones" :exportable="false" style="min-width: 8rem">
            <template #body="{ data }">
                <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editarTable(data)" />
                <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmarDeleteTable(data)" />
            </template>
        </Column>
    </DataTable>

    <DeleteTable
        v-model:visible="deleteTableDialog"
        :table="table"
        @deleted="handleTableDeleted"
    />
    <UpdateTable
        v-model:visible="updateTableDialog"
        :tableId="selectedTableId"
        @updated="handleTableUpdated"
    />
</template>
