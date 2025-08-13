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
import DeleteCliente from './DeleteCliente.vue';
import UpdateCliente from './UpdateCliente.vue';
import Select from 'primevue/select';

const dt = ref();
const clientes = ref([]);
const selectedClientes = ref();
const loading = ref(false);
const globalFilterValue = ref('');
const deleteClienteDialog = ref(false);
const cliente = ref({});
const selectedClienteId = ref(null);
const selectedEstadoCliente = ref(null);
const updateClienteDialog = ref(false);

const props = defineProps({
    refresh: {
        type: Number,
        required: true
    }
});

watch(() => props.refresh, () => {
    loadCliente();
});

watch(() => selectedEstadoCliente.value, () => {
    pagination.value.currentPage = 1;
    loadCliente();
});

function editCliente(cliente) {
    selectedClienteId.value = cliente.id;
    updateClienteDialog.value = true;
}

const estadoClienteOptions = ref([
    { name: 'TODOS', value: '' },
    { name: 'ACTIVOS', value: 1 },
    { name: 'INACTIVOS', value: 0 },
]);

function handleClienteUpdated() {
    loadCliente();
}

function confirmDeleteCliente(selected) {
    cliente.value = selected;
    deleteClienteDialog.value = true;
}

const pagination = ref({
    currentPage: 1,
    perPage: 15,
    total: 0
});

const filters = ref({
    state: null
});

function handleClienteDeleted() {
    loadCliente();
}

const loadCliente = async () => {
    loading.value = true;
    try {
        const params = {
            page: pagination.value.currentPage,
            per_page: pagination.value.perPage,
            search: globalFilterValue.value,
            state: filters.value.state,
        };
        if (selectedEstadoCliente.value !== null && selectedEstadoCliente.value.value !== '') {
            params.state = selectedEstadoCliente.value.value;
        }

        const response = await axios.get('/cliente', { params });

        clientes.value = response.data.data;
        pagination.value.currentPage = response.data.meta.current_page;
        pagination.value.total = response.data.meta.total;
    } catch (error) {
        console.error('Error al cargar clientes:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar los clientes', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const onPage = (event) => {
    pagination.value.currentPage = event.page + 1;
    pagination.value.perPage = event.rows;
    loadCliente();
};

const getSeverity = (value) => {
    if (value === true || value === '1') return 'success';
    if (value === false || value === '0') return 'danger';
    return null;
};

const onGlobalSearch = debounce(() => {
    pagination.value.currentPage = 1;
    loadCliente();
}, 500);

onMounted(() => {
    loadCliente();
});
</script>

<template>
    <DataTable ref="dt" v-model:selection="selectedClientes" :value="clientes" dataKey="id" :paginator="true"
        :rows="pagination.perPage" :totalRecords="pagination.total" :loading="loading" :lazy="true" @page="onPage"
        :rowsPerPageOptions="[15, 20, 25]" scrollable scrollHeight="574px"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} clientes">

        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">CLIENTES</h4>
                <div class="flex flex-wrap gap-2">
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="globalFilterValue" @input="onGlobalSearch" placeholder="Buscar..." />
                    </IconField>
                    <Select v-model="selectedEstadoCliente" :options="estadoClienteOptions" optionLabel="name"
                        placeholder="Estado" class="w-full md:w-auto" />
                    <Button icon="pi pi-refresh" outlined rounded aria-label="Refresh" @click="loadCliente" />
                </div>
            </div>
        </template>

        <Column selectionMode="multiple" style="width: 1rem" :exportable="false"></Column>
        <Column field="name" header="Nombre" sortable style="min-width: 12rem" />
        <Column field="codigo" header="Código" sortable style="min-width: 10rem" />
        <Column field="Cliente_Tipo" header="Tipo de Cliente" sortable style="min-width: 12rem" />
        <Column field="creacion" header="Creación" sortable style="min-width: 13rem" />
        <Column field="actualizacion" header="Actualización" sortable style="min-width: 13rem" />
        <Column field="state" header="Estado" sortable style="min-width: 6rem">
            <template #body="{ data }">
                <Tag :value="data.state ? 'Activo' : 'Inactivo'" :severity="getSeverity(data.state)" />
            </template>
        </Column>
        <Column field="accions" header="Acciones" :exportable="false" style="min-width: 8rem">
            <template #body="slotProps">
                <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editCliente(slotProps.data)" />
                <Button icon="pi pi-trash" outlined rounded severity="danger"
                    @click="confirmDeleteCliente(slotProps.data)" />
            </template>
        </Column>
    </DataTable>

    <DeleteCliente v-model:visible="deleteClienteDialog" :cliente="cliente" @deleted="handleClienteDeleted" />
    <UpdateCliente v-model:visible="updateClienteDialog" :clienteId="selectedClienteId"
        @updated="handleClienteUpdated" />
</template>
