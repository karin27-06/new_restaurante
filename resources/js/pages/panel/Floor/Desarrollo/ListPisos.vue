<script setup>
import { ref, onMounted, watch } from 'vue';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import Select from 'primevue/select';
import axios from 'axios';
import { debounce } from 'lodash';
import DeletePiso from './DeletePisos.vue';
import UpdatePiso from './UpdatePisos.vue';

const pisos = ref([]);
const selectedPisos = ref();
const loading = ref(false);
const globalFilterValue = ref('');
const selectedEstadoPiso = ref(null);
const pagination = ref({
    currentPage: 1,
    perPage: 15,
    total: 0
});

const piso = ref({});
const selectedPisoId = ref(null);
const deletePisoDialog = ref(false);
const updatePisoDialog = ref(false);

const props = defineProps({
    refresh: {
        type: [Number, Boolean],
        default: 0
    }
});

watch(() => props.refresh, () => {
    loadPisos();
});


const estadoPisoOptions = ref([
    { name: 'TODOS', value: '' },
    { name: 'ACTIVOS', value: 1 },
    { name: 'INACTIVOS', value: 0 },
]);

const loadPisos = async () => {
    loading.value = true;
    try {
        const params = {
            page: pagination.value.currentPage,
            per_page: pagination.value.perPage,
            search: globalFilterValue.value,
            state: selectedEstadoPiso.value?.value ?? '',
        };

        const response = await axios.get('/piso', { params });
        pisos.value = response.data.data;
        pagination.value.currentPage = response.data.meta.current_page;
        pagination.value.total = response.data.meta.total;
    } catch (error) {
        console.error('Error al cargar pisos:', error);
    } finally {
        loading.value = false;
    }
};

const onPage = (event) => {
    pagination.value.currentPage = event.page + 1;
    pagination.value.perPage = event.rows;
    loadPisos();
};

const getSeverity = (value) => {
    if (value === true || value === '1') return 'success';
    if (value === false || value === '0') return 'danger';
    return null;
};

const onGlobalSearch = debounce(() => {
    pagination.value.currentPage = 1;
    loadPisos();
}, 500);

watch(() => selectedEstadoPiso.value, () => {
    pagination.value.currentPage = 1;
    loadPisos();
});

const editarPiso = (p) => {
    selectedPisoId.value = p.id;
    updatePisoDialog.value = true;
};

const confirmarEliminarPiso = (p) => {
    piso.value = p;
    deletePisoDialog.value = true;
};

const handlePisoEliminado = () => {
    loadPisos();
};

const handlePisoActualizado = () => {
    loadPisos();
};

onMounted(() => {
    loadPisos();
});
</script>

<template>
    <DataTable ref="dt" v-model:selection="selectedPisos" :value="pisos" dataKey="id" :paginator="true"
        :rows="pagination.perPage" :totalRecords="pagination.total" :loading="loading" :lazy="true" @page="onPage"
        :rowsPerPageOptions="[15, 20, 25]" scrollable scrollHeight="574px"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} pisos">

        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">PISOS</h4>
                <div class="flex flex-wrap gap-2">
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="globalFilterValue" @input="onGlobalSearch" placeholder="Buscar..." />
                    </IconField>
                    <Select v-model="selectedEstadoPiso" :options="estadoPisoOptions" optionLabel="name"
                        placeholder="Estado del Piso" class="w-full md:w-auto" />
                    <Button icon="pi pi-refresh" outlined rounded aria-label="Refresh" @click="loadPisos" />
                </div>
            </div>
        </template>

        <Column selectionMode="multiple" style="width: 1rem" :exportable="false"></Column>
        <Column field="name" header="Nombre" sortable style="min-width: 13rem"></Column>
        <Column field="description" header="Descripción" sortable style="min-width: 13rem"></Column>
        <Column field="creacion" header="Creación" sortable style="min-width: 13rem"></Column>
        <Column field="actualizacion" header="Actualización" sortable style="min-width: 13rem"></Column>
        <Column field="state" header="Estado" sortable style="min-width: 4rem">
            <template #body="{ data }">
                <Tag :value="data.state ? 'Activo' : 'Inactivo'" :severity="getSeverity(data.state)" />
            </template>
        </Column>
        <Column field="accions" header="Acciones" :exportable="false" style="min-width: 8rem">
            <template #body="slotProps">
                <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editarPiso(slotProps.data)" />
                <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmarEliminarPiso(slotProps.data)" />
            </template>
        </Column>
    </DataTable>

    <DeletePiso
        v-model:visible="deletePisoDialog"
        :piso="piso"
        @deleted="handlePisoEliminado"
    />
    <UpdatePiso
        v-model:visible="updatePisoDialog"
        :pisoId="selectedPisoId"
        @updated="handlePisoActualizado"
    />
</template>
