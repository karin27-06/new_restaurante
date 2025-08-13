<script setup>
import { ref, onMounted, watch } from 'vue';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import axios from 'axios';
import { debounce } from 'lodash';
import DeleteProveedor from './DeleteProveedor.vue';
import UpdateProveedor from './UpdateProveedor.vue';
import Select from 'primevue/select';

const dt = ref();
const proveedores = ref([]);
const selectedProveedores = ref();
const loading = ref(false);
const globalFilterValue = ref('');
const deleteProveedorDialog = ref(false);
const proveedor = ref({});
const selectedProveedorId = ref(null);
const selectedEstadoProveedor = ref(null);
const updateProveedorDialog = ref(false);
const currentPage = ref(1);

const props = defineProps({
    refresh: {
        type: Number,
        required: true
    }
});

watch(() => props.refresh, () => {
    loadProveedor();
});

watch(() => selectedEstadoProveedor.value, () => {
    currentPage.value = 1;
    loadProveedor();
});

function editProveedor(proveedor) {
    selectedProveedorId.value = proveedor.id;
    updateProveedorDialog.value = true;
}

const estadoProveedorOptions = ref([
    { name: 'TODOS', value: '' },
    { name: 'ACTIVOS', value: 1 },
    { name: 'INACTIVOS', value: 0 },
]);

function handleProveedorUpdated() {
    loadProveedor();
}

function confirmDeleteProveedor(selected) {
    proveedor.value = selected;
    deleteProveedorDialog.value = true;
}

const pagination = ref({
    currentPage: 1,
    perPage: 15,
    total: 0
});

const filters = ref({
    state: null,
    online: null
});

function handleProveedorDeleted() {
    loadProveedor();
}

const loadProveedor = async () => {
    loading.value = true;
    try {
        const params = {
            page: pagination.value.currentPage,
            per_page: pagination.value.perPage,
            search: globalFilterValue.value,
            state: filters.value.state,
        };
        if (selectedEstadoProveedor.value !== null && selectedEstadoProveedor.value.value !== '') {
            params.state = selectedEstadoProveedor.value.value;
        }

        const response = await axios.get('/proveedor', { params });

        proveedores.value = response.data.data;
        pagination.value.currentPage = response.data.meta.current_page;
        pagination.value.total = response.data.meta.total;
    } catch (error) {
        console.error('Error al cargar proveedores:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar los proveedores', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const onPage = (event) => {
    pagination.value.currentPage = event.page + 1;
    pagination.value.perPage = event.rows;
    loadProveedor();
};

const getSeverity = (value) => {
    if (value === true || value === '1') return 'success';
    if (value === false || value === '0') return 'danger';
    return null;
};

const onGlobalSearch = debounce(() => {
    pagination.value.currentPage = 1;
    loadProveedor();
}, 500);

onMounted(() => {
    loadProveedor();
});
</script>

<template>
    <DataTable ref="dt" v-model:selection="selectedProveedores" :value="proveedores" dataKey="id" :paginator="true"
        :rows="pagination.perPage" :totalRecords="pagination.total" :loading="loading" :lazy="true" @page="onPage"
        :rowsPerPageOptions="[15, 20, 25]" scrollable scrollHeight="574px"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} proveedores">

        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">PROVEEDORES</h4>
                <div class="flex flex-wrap gap-2">
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="globalFilterValue" @input="onGlobalSearch" placeholder="Buscar..." />
                    </IconField>
                    <Select v-model="selectedEstadoProveedor" :options="estadoProveedorOptions" optionLabel="name"
                        placeholder="Estado" class="w-full md:w-auto" />
                    <Button icon="pi pi-refresh" outlined rounded aria-label="Refresh" @click="loadProveedor" />
                </div>
            </div>
        </template>

        <Column selectionMode="multiple" style="width: 1rem" :exportable="false"></Column>
        <Column field="name" header="Nombre" sortable style="min-width: 12rem" />
        <Column field="ruc" header="RUC" sortable style="min-width: 10rem" />
        <Column field="address" header="Dirección" sortable style="min-width: 15rem" />
        <Column field="phone" header="Teléfono" sortable style="min-width: 12rem" />
        <Column field="creacion" header="Creación" sortable style="min-width: 13rem" />
        <Column field="actualizacion" header="Actualización" sortable style="min-width: 13rem" />
        <Column field="state" header="Estado" sortable style="min-width: 6rem">
            <template #body="{ data }">
                <Tag :value="data.state ? 'Activo' : 'Inactivo'" :severity="getSeverity(data.state)" />
            </template>
        </Column>
        <Column field="actions" header="Acciones" :exportable="false" style="min-width: 8rem">
            <template #body="slotProps">
                <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editProveedor(slotProps.data)" />
                <Button icon="pi pi-trash" outlined rounded severity="danger"
                    @click="confirmDeleteProveedor(slotProps.data)" />
            </template>
        </Column>
    </DataTable>

    <DeleteProveedor v-model:visible="deleteProveedorDialog" :proveedor="proveedor" @deleted="handleProveedorDeleted" />
    <UpdateProveedor v-model:visible="updateProveedorDialog" :proveedorId="selectedProveedorId"
        @updated="handleProveedorUpdated" />
</template>
