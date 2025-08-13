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
import { debounce } from 'lodash'
import DeleteCajas from './DeleteCajas.vue';
import UpdateCajas from './UpdateCajas.vue';

const cajas = ref([]);
const loading = ref(false);
const globalFilterValue = ref('');
const deleteCajaDialog = ref(false);
const updateCajaDialog = ref(false);
const selectedCajaId = ref(null);
const caja = ref({});
const currentPage = ref(1);
const selectedVendedor = ref(null);
const selectedEstadoCaja = ref(null);

const pagination = ref({
    currentPage: 1,
    perPage: 15,
    total: 0
});

const estadoCajaOptions = ref([
    { name: 'TODOS', value: '' },
    { name: 'SIN OCUPAR', value: 1 },
    { name: 'OCUPADA', value: 0 },
]);

const loadCajas = async () => {
    loading.value = true;
    try {
        const params = {
            page: pagination.value.currentPage,
            per_page: pagination.value.perPage,
            search: globalFilterValue.value,
            vendedor: selectedVendedor?.value,
            state: selectedEstadoCaja.value?.value ?? '',
        };
        const response = await axios.get('/caja', { params });
        cajas.value = response.data.data;
        pagination.value.currentPage = response.data.meta.current_page;
        pagination.value.total = response.data.meta.total;
    } catch (error) {
        console.error('Error al cargar cajas:', error);
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

watch(() => props.refresh, loadCajas);
watch(() => selectedEstadoCaja.value, () => {
    currentPage.value = 1;
    loadCajas();
});

const onPage = (event) => {
    pagination.value.currentPage = event.page + 1;
    pagination.value.perPage = event.rows;
    loadCajas();
};

const onGlobalSearch = debounce(() => {
    pagination.value.currentPage = 1;
    loadCajas();
}, 500);

const getSeverity = (value) => {
    return value ? 'success' : 'danger';
};

// Cuando haces clic en editar, asegúrate de actualizar el ID de la caja seleccionada y abrir el modal
const editarCaja = (caja) => {
    selectedCajaId.value = caja.id; // Asignar el ID de la caja seleccionada
    updateCajaDialog.value = true;  // Abrir el modal
};

const confirmarDeleteCaja = (data) => {
    caja.value = data;  // Aquí estás pasando la caja seleccionada
    deleteCajaDialog.value = true;  // Mostrar el modal de confirmación
};

function handleCajaUpdated() {
    loadCajas();
}

function handleCajaDeleted() {
    loadCajas();
}

onMounted(loadCajas);
</script>

<template>
    <DataTable
        :value="cajas"
        :paginator="true"
        :rows="pagination.perPage"
        :totalRecords="pagination.total"
        :loading="loading"
        :lazy="true"
        @page="onPage"
        dataKey="id"
        scrollable scrollHeight="574px"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} cajas"
    >
        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">CAJAS</h4>
                <div class="flex flex-wrap gap-2">
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="globalFilterValue" @input="onGlobalSearch" placeholder="Buscar..." />
                    </IconField>
                    <Select v-model="selectedEstadoCaja" :options="estadoCajaOptions" optionLabel="name" placeholder="Estado" />
                    <Button icon="pi pi-refresh" outlined rounded aria-label="Refresh" @click="loadCajas" />
                </div>
            </div>
        </template>

        <Column selectionMode="multiple" style="width: 1rem" />
        <Column field="numero_cajas" header="N° de caja" sortable style="min-width: 10rem" />
        <Column field="state" header="Estado" sortable>
            <template #body="{ data }">
                <Tag :value="data.state ?  'Sin ocupar':'Ocupada' " :severity="getSeverity(data.state)" />
            </template>
        </Column>
        <Column field="vendedorNombre" header="Vendedor" sortable style="min-width: 20rem" />
        <Column field="accions" header="Acciones" :exportable="false" style="min-width: 8rem">
            <template #body="{ data }">
                <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editarCaja(data)" />
                <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmarDeleteCaja(data)" />
            </template>
        </Column>
    </DataTable>

    <DeleteCajas
        v-model:visible="deleteCajaDialog"
        :caja="caja"
        @deleted="handleCajaDeleted"
    />
    <UpdateCajas 
    v-model:visible="updateCajaDialog" 
    :cajaId="selectedCajaId" 
    @updated="handleCajaUpdated" 
    />
</template>
