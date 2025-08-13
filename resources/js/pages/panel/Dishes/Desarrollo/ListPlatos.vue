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
import Select from 'primevue/select';
import DeletePlatos from './DeletePlatos.vue';
import UpdatePlatos from './UpdatePlatos.vue';
import { useToast } from 'primevue/usetoast';

// Initialize toast
const toast = useToast();

const dt = ref();
const platos = ref([]);
const selectedPlatos = ref();
const loading = ref(false);
const globalFilterValue = ref('');
const deletePlatoDialog = ref(false);
const plato = ref({});
const selectedPlatoId = ref(null);
const selectedEstadoPlato = ref(null);
const updatePlatoDialog = ref(false);
const currentPage = ref(1);

const props = defineProps({
    refresh: {
        type: Number,
        required: true
    }
});
watch(() => props.refresh, () => {
    loadPlatos();
});
watch(() => selectedEstadoPlato.value, () => {
    currentPage.value = 1;
    loadPlatos();
});

function editPlato(plato) {
    selectedPlatoId.value = plato.id;
    updatePlatoDialog.value = true;
}

const estadoPlatoOptions = ref([
    { name: 'TODOS', value: '' },
    { name: 'ACTIVOS', value: 1 },
    { name: 'INACTIVOS', value: 0 },
]);

function handlePlatoUpdated() {
    loadPlatos();
}

function confirmDeletePlato(selected) {
    plato.value = selected;
    deletePlatoDialog.value = true;
}

const pagination = ref({
    currentPage: 1,
    perPage: 15,
    total: 0
});

const filters = ref({
    state: null
});

function handlePlatoDeleted() {
    loadPlatos();
}

const loadPlatos = async () => {
    loading.value = true;
    try {
        const params = {
            page: pagination.value.currentPage,
            per_page: pagination.value.perPage,
            search: globalFilterValue.value,
            state: filters.value.state,
        };
        if (selectedEstadoPlato.value !== null && selectedEstadoPlato.value !== undefined && selectedEstadoPlato.value.value !== '') {
            params.state = selectedEstadoPlato.value.value;
        }

        const response = await axios.get('/plato', { params });

        // Check if response.data exists and has the expected structure
        if (response.data && response.data.data) {
            platos.value = response.data.data;
            
            // Safely check if meta property exists before accessing its properties
            if (response.data.meta) {
                pagination.value.currentPage = response.data.meta.current_page || 1;
                pagination.value.total = response.data.meta.total || 0;
            } else {
                // Use default values if meta is not available
                pagination.value.currentPage = 1;
                pagination.value.total = response.data.data.length || 0;
            }
        } else {
            platos.value = [];
            pagination.value.total = 0;
        }
    } catch (error) {
        console.error('Error al cargar platos:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar los platos', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const onPage = (event) => {
    pagination.value.currentPage = event.page + 1;
    pagination.value.perPage = event.rows;
    loadPlatos();
};

const getSeverity = (value) => {
    if (value === true || value === '1') return 'success';
    if (value === false || value === '0') return 'danger';
    return null;
};

const onGlobalSearch = debounce(() => {
    pagination.value.currentPage = 1;
    loadPlatos();
}, 500);

const formatCurrency = (value) => {
    if (value != null) {
        return 'S/. ' + parseFloat(value).toFixed(2);
    }
    return '';
};

onMounted(() => {
    loadPlatos();
});
</script>

<template>
    <DataTable ref="dt" v-model:selection="selectedPlatos" :value="platos" dataKey="id" :paginator="true"
        :rows="pagination.perPage" :totalRecords="pagination.total" :loading="loading" :lazy="true" @page="onPage"
        :rowsPerPageOptions="[15, 20, 25]" scrollable scrollHeight="574px"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} platos">

        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">Platos</h4>
                <div class="flex flex-wrap gap-2">
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="globalFilterValue" @input="onGlobalSearch" placeholder="Buscar..." />
                    </IconField>
                    <Select v-model="selectedEstadoPlato" :options="estadoPlatoOptions" optionLabel="name"
                        placeholder="Estado del Plato" class="w-full md:w-auto" />
                    <Button icon="pi pi-refresh" outlined rounded aria-label="Refresh" @click="loadPlatos" />
                </div>
            </div>
        </template>

        <Column selectionMode="multiple" style="width: 1rem" :exportable="false"></Column>
        <Column field="name" header="Nombre" sortable style="min-width: 12rem"></Column>
        <Column field="price" header="Precio" sortable style="min-width: 8rem">
            <template #body="{ data }">
                {{ formatCurrency(data.price) }}
            </template>
        </Column>
        <Column field="quantity" header="Cantidad" sortable style="min-width: 6rem"></Column>
        <Column field="nameCategory" header="Categoría" sortable style="min-width: 12rem"></Column>
        <Column field="creacion" header="Creación" sortable style="min-width: 10rem"></Column>
        <Column field="actualizacion" header="Actualización" sortable style="min-width: 10rem"></Column>
        <Column field="state" header="Estado" sortable style="min-width: 4rem">
            <template #body="{ data }">
                <Tag :value="data.state ? 'Activo' : 'Inactivo'" :severity="getSeverity(data.state)" />
            </template>
        </Column>
        <Column field="accions" header="Acciones" :exportable="false" style="min-width: 8rem">
            <template #body="slotProps">
                <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editPlato(slotProps.data)" />
                <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeletePlato(slotProps.data)" />
            </template>
        </Column>
    </DataTable>

    <DeletePlatos
        v-model:visible="deletePlatoDialog"
        :plato="plato"
        @deleted="handlePlatoDeleted"
    />
    <UpdatePlatos
        v-model:visible="updatePlatoDialog"
        :PlatoId="selectedPlatoId"
        @updated="handlePlatoUpdated"
    />
</template>