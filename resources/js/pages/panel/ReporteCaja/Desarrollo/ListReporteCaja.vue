<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
//import Tag from 'primevue/tag';
import { debounce } from 'lodash';
import UpdateReporteCaja from './UpdateReporteCaja.vue';

const reportes = ref([]);
const loading = ref(false);
const globalFilterValue = ref('');
const updateReporteDialog = ref(false);
const selectedReporteId = ref(null);
const pagination = ref({
    currentPage: 1,
    perPage: 15,
    total: 0
});

const loadReportes = async () => {
    loading.value = true;
    try {
        const params = {
            page: pagination.value.currentPage,
            per_page: pagination.value.perPage,
            search: globalFilterValue.value,
        };
        const response = await axios.get('/reporte_caja', { params });
        reportes.value = response.data.data;
        pagination.value.currentPage = response.data.meta.current_page;
        pagination.value.total = response.data.meta.total;
    } catch (error) {
        console.error('Error al cargar reportes:', error);
    } finally {
        loading.value = false;
    }
};

const onPage = (event) => {
    pagination.value.currentPage = event.page + 1;
    pagination.value.perPage = event.rows;
    loadReportes();
};

const onGlobalSearch = debounce(() => {
    pagination.value.currentPage = 1;
    loadReportes();
}, 500);

const formatCurrency = (value) => {
    if (value != null) {
        return 'S/. ' + parseFloat(value).toFixed(2);
    }
    return '';
};

const editarReporte = (reporte) => {
    selectedReporteId.value = reporte.id;
    updateReporteDialog.value = true;
};

function handleReporteUpdated() {
    loadReportes();
}

onMounted(loadReportes);
</script>

<template>
    <DataTable
        :value="reportes"
        :paginator="true"
        :rows="pagination.perPage"
        :totalRecords="pagination.total"
        :loading="loading"
        :lazy="true"
        @page="onPage"
        dataKey="id"
        scrollable scrollHeight="574px"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} reportes"
    >
        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h2 class="m-0">REPORTE DE CAJAS</h2>
                <div class="flex flex-wrap gap-2">
                    <InputText v-model="globalFilterValue" @input="onGlobalSearch" placeholder="Buscar por vendedor o N° de caja..." class="w-96"/>
                    <Button icon="pi pi-refresh" outlined rounded aria-label="Refresh" @click="loadReportes" />
                </div>
            </div>
        </template>
        <Column field="numero_cajas" header="N° Caja" sortable />
        <Column field="vendedorNombre" header="Vendedor" sortable />
        <!--Columnas puestas al prefijo de S/.-->
        <Column field="monto_efectivo" header="Monto Efectivo" sortable>
            <template #body="{ data }">
            {{ formatCurrency(data.monto_efectivo) }}
            </template>
        </Column>
        <Column field="monto_tarjeta" header="Monto Tarjeta" sortable>
            <template #body="{ data }">
            {{ formatCurrency(data.monto_tarjeta) }}
            </template>
        </Column>
        <Column field="monto_yape" header="Monto Yape/Plin" sortable>
            <template #body="{ data }">
            {{ formatCurrency(data.monto_yape) }}
            </template>
        </Column>
        <Column field="monto_transferencia" header="Monto Transferencia" sortable>
            <template #body="{ data }">
            {{ formatCurrency(data.monto_transferencia) }}
            </template>
        </Column>
        <Column field="creacion" header="Fecha Apertura" sortable />
        <Column field="actualizacion" header="Fecha Modificación" sortable />
        <Column field="fecha_salida" header="Fecha Cierre" sortable>
          <template #body="{ data }">
            <span>{{ data.fecha_salida || 'Sin cerrar' }}</span>
          </template>
        </Column>
        <Column field="acciones" header="Acción" :exportable="false">
            <template #body="{ data }">
                <Button icon="pi pi-pencil" outlined rounded @click="editarReporte(data)" />
            </template>
        </Column>
    </DataTable>

    <UpdateReporteCaja 
      v-model:visible="updateReporteDialog" 
      :reporteId="selectedReporteId" 
      @updated="handleReporteUpdated" 
    />
</template>
