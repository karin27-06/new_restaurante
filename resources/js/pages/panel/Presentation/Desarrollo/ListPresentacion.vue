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
import DeletePresentacion from './DeletePresentacion.vue';
import UpdatePresentacion from './UpdatePresentacion.vue';
import Select from 'primevue/select';

const dt = ref();
const presentaciones = ref([]);
const selectedPresentaciones = ref();
const loading = ref(false);
const globalFilterValue = ref('');
const deletePresentacionDialog = ref(false);
const presentacion = ref({});
const selectedPresentacionId = ref(null);
const selectedEstadoPresentacion = ref(null);
const updatePresentacionDialog = ref(false);
const currentPage = ref(1);

const props = defineProps({
    refresh: {
        type: Number,
        required: true
    }
});

watch(() => props.refresh, () => {
    loadPresentacion();
});

watch(() => selectedEstadoPresentacion.value, () => {
    currentPage.value = 1;
    loadPresentacion();
});

function editPresentacion(p) {
    selectedPresentacionId.value = p.id;
    updatePresentacionDialog.value = true;
}

const estadoPresentacionOptions = ref([
    { name: 'TODOS', value: '' },
    { name: 'ACTIVOS', value: 1 },
    { name: 'INACTIVOS', value: 0 },
]);

function handlePresentacionUpdated() {
    loadPresentacion();
}

function confirmDeletePresentacion(selected) {
    presentacion.value = selected;
    deletePresentacionDialog.value = true;
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

function handlePresentacionDeleted() {
    loadPresentacion();
}

const loadPresentacion = async () => {
    loading.value = true;
    try {
        const params = {
            page: pagination.value.currentPage,
            per_page: pagination.value.perPage,
            search: globalFilterValue.value,
            state: filters.value.state,
        };
        if (selectedEstadoPresentacion.value !== null && selectedEstadoPresentacion.value.value !== '') {
            params.state = selectedEstadoPresentacion.value.value;
        }

        const response = await axios.get('/presentacion', { params });

        presentaciones.value = response.data.data;
        pagination.value.currentPage = response.data.meta.current_page;
        pagination.value.total = response.data.meta.total;
    } catch (error) {
        console.error('Error al cargar presentaciones:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar las presentaciones', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const onPage = (event) => {
    pagination.value.currentPage = event.page + 1;
    pagination.value.perPage = event.rows;
    loadPresentacion();
};

const getSeverity = (value) => {
    if (value === true || value === '1') return 'success';
    if (value === false || value === '0') return 'danger';
    return null;
};

const onGlobalSearch = debounce(() => {
    pagination.value.currentPage = 1;
    loadPresentacion();
}, 500);

onMounted(() => {
    loadPresentacion();
});
</script>

<template>
    <DataTable ref="dt" v-model:selection="selectedPresentaciones" :value="presentaciones" dataKey="id" :paginator="true"
        :rows="pagination.perPage" :totalRecords="pagination.total" :loading="loading" :lazy="true" @page="onPage"
        :rowsPerPageOptions="[15, 20, 25]" scrollable scrollHeight="574px"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} presentaciones">

        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">PRESENTACIONES</h4>
                <div class="flex flex-wrap gap-2">
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="globalFilterValue" @input="onGlobalSearch" placeholder="Buscar..." />
                    </IconField>
                    <Select v-model="selectedEstadoPresentacion" :options="estadoPresentacionOptions" optionLabel="name"
                        placeholder="Estado" class="w-full md:w-auto" />
                    <Button icon="pi pi-refresh" outlined rounded aria-label="Refresh" @click="loadPresentacion" />
                </div>
            </div>
        </template>

        <Column selectionMode="multiple" style="width: 1rem" :exportable="false"></Column>
        <Column field="name" header="Nombre" sortable style="min-width: 12rem" />
        <Column field="description" header="Descripción" sortable style="min-width: 15rem" />
        <Column field="creacion" header="Creación" sortable style="min-width: 13rem" />
        <Column field="actualizacion" header="Actualización" sortable style="min-width: 13rem" />
        <Column field="state" header="Estado" sortable style="min-width: 6rem">
            <template #body="{ data }">
                <Tag :value="data.state ? 'Activo' : 'Inactivo'" :severity="getSeverity(data.state)" />
            </template>
        </Column>
        <Column field="actions" header="Acciones" :exportable="false" style="min-width: 8rem">
            <template #body="slotProps">
                <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editPresentacion(slotProps.data)" />
                <Button icon="pi pi-trash" outlined rounded severity="danger"
                    @click="confirmDeletePresentacion(slotProps.data)" />
            </template>
        </Column>
    </DataTable>

    <DeletePresentacion v-model:visible="deletePresentacionDialog" :presentacion="presentacion" @deleted="handlePresentacionDeleted" />
    <UpdatePresentacion v-model:visible="updatePresentacionDialog" :presentacionId="selectedPresentacionId"
        @updated="handlePresentacionUpdated" />
</template>
