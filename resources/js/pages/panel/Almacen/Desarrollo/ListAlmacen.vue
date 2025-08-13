<script setup>
import { ref, onMounted,watch } from 'vue';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import axios from 'axios';
import { debounce } from 'lodash';
import DeleteAlmacen from './DeleteAlmacen.vue';
import UpdateAlmacen from './UpdateAlmacen.vue';
import Select from 'primevue/select';

const dt = ref();
const almacenes = ref([]);
const selectedalmacenes = ref();
const loading = ref(false);
const globalFilterValue = ref('');
const deleteAlmacenDialog = ref(false);
const almacen = ref({});
const selectedAlmacenId = ref(null);
const selectedEstadoAlmacen = ref(null);
const updateAlmacenDialog = ref(false);
const currentPage = ref(1);

const props = defineProps({
    refresh: {
        type: Number,
        required: true
    }
});
watch(() => props.refresh, () => {
    loadAlmacen();
});
watch(() => selectedEstadoAlmacen.value, () => {
    currentPage.value = 1;
    loadAlmacen();
});
function editalmacen(almacen) {
    selectedAlmacenId.value = almacen.id;
    updateAlmacenDialog.value = true;
}

const estadoAlmacenOptions = ref([
    { name: 'TODOS', value: '' },
    { name: 'ACTIVOS', value: 1 },
    { name: 'INACTIVOS', value: 0 },
]);

function handleAlmacenUpdated() {
    loadAlmacen();
}

function confirmDeletealmacen(selected) {
    almacen.value = selected;
    deleteAlmacenDialog.value = true;
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
function handleUserDeleted() {
    loadAlmacen();
}

const loadAlmacen = async () => {
    loading.value = true;
    try {
        const params = {
            page: pagination.value.currentPage,
                per_page: pagination.value.perPage,
                search: globalFilterValue.value,
                state: filters.value.state,
        };
        if (selectedEstadoAlmacen.value !== null && selectedEstadoAlmacen.value.value !== '') {
            params.state = selectedEstadoAlmacen.value.value;
        }

        const response = await axios.get('/almacen', { params });

        almacenes.value = response.data.data;
        pagination.value.currentPage = response.data.meta.current_page;
        pagination.value.total = response.data.meta.total;
    } catch (error) {
        console.error('Error al cargar almacen:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar los almacen', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const onPage = (event) => {
    pagination.value.currentPage = event.page + 1;
    pagination.value.perPage = event.rows;
    loadAlmacen();
};

const getSeverity = (value) => {
    if (value === true || value === '1') return 'success';
    if (value === false || value === '0') return 'danger';
    return null;
};

const onGlobalSearch = debounce(() => {
    pagination.value.currentPage = 1;
    loadAlmacen();
}, 500);

onMounted(() => {
    loadAlmacen();
});
</script>

<template>
    <DataTable ref="dt" v-model:selection="selectedalmacenes" :value="almacenes" dataKey="id" :paginator="true"
        :rows="pagination.perPage" :totalRecords="pagination.total" :loading="loading" :lazy="true" @page="onPage"
        :rowsPerPageOptions="[15, 20, 25]" scrollable scrollHeight="574px"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} almacen">

        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">ALMACEN</h4>
                <div class="flex flex-wrap gap-2">
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="globalFilterValue" @input="onGlobalSearch" placeholder="Buscar..." />
                    </IconField>
                    <Select v-model="selectedEstadoAlmacen" :options="estadoAlmacenOptions" optionLabel="name"
                        placeholder="Estado" class="w-full md:w-auto" />
                    <Button icon="pi pi-refresh" outlined rounded aria-label="Refresh" @click="loadAlmacen" />
                </div>
            </div>
        </template>

        <Column selectionMode="multiple" style="width: 1rem" :exportable="false"></Column>
        <Column field="name" header="Nombre" sortable style="min-width: 13rem"></Column>
        <Column field="creacion" header="Creacion" sortable style="min-width: 13rem"></Column>
        <Column field="actualizacion" header="Actualizacion" sortable style="min-width: 13rem"></Column>
        <Column field="state" header="Estado" sortable style="min-width: 4rem">
            <template #body="{ data }">
                <Tag :value="data.state ? 'Activo' : 'Inactivo'" :severity="getSeverity(data.state)" />
            </template>
        </Column>
        <Column field="accions" header="Acciones" :exportable="false" style="min-width: 8rem">
            <template #body="slotProps">
                <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editalmacen(slotProps.data)"/>
                <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeletealmacen(slotProps.data)" />
            </template>
        </Column>
    </DataTable>
    <DeleteAlmacen
        v-model:visible="deleteAlmacenDialog"
        :almacen="almacen"
        @deleted="handleUserDeleted"
    />
    <UpdateAlmacen
        v-model:visible="updateAlmacenDialog"
        :AlmacenId="selectedAlmacenId"
        @updated="handleAlmacenUpdated"
    />
</template>
