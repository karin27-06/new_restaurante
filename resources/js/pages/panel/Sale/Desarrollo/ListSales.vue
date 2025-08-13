<script setup>
import axios from 'axios';
import { debounce } from 'lodash';
import Button from 'primevue/button';
import Calendar from 'primevue/calendar';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog'; // Importar Dialog para el modal
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import { onMounted, ref, watch } from 'vue';

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
const selectedSale = ref(null); // Variable para almacenar la venta seleccionada
const detailsDialog = ref(false); // Controlar la visibilidad del modal
const pagination = ref({
    currentPage: 1,
    perPage: 15,
    total: 0,
});
const refreshCount = ref(0); // Variable que se incrementa cuando se agrega un insumo

// Método para ver los detalles de una venta
const verDetalle = async (data) => {
    try {
        // Solicitar los detalles de la venta usando idSale
        const response = await axios.get(`/venta?idSale=${data.idSale}`);
        selectedSale.value = response.data.data[0]; // Almacenar los detalles de la venta
        detailsDialog.value = true; // Abrir el modal
    } catch (error) {
        console.error('Error al obtener detalles de la venta:', error);
    }
};
const estadoInputOptions = ref([
    { name: 'TODOS', value: '' },
    { name: 'ACTIVOS', value: 1 },
    { name: 'INACTIVOS', value: 0 },
]);

const isColumnSelected = (fieldName) => {
    return selectedColumns.value.some((col) => col.field === fieldName);
};

const optionalColumns = ref([
    { field: 'tablenum', header: 'Numero' },
    { field: 'capacity', header: 'Capacidad' },
]);

const dateRange = ref(null); // Variable para almacenar el rango de fechas
// Variables para almacenar los montos
const tarjetaMonto = ref(0);
const transferenciaMonto = ref(0);
const efectivoMonto = ref(0);
const yapeMonto = ref(0);
const plinMonto = ref(0);
const MontoTotal = ref(0);
const loadInputs = async () => {
    loading.value = true;
    try {
        const params = {
            page: pagination.value.currentPage,
            per_page: pagination.value.perPage,
            start_date: dateRange.value ? dateRange.value[0].toISOString().split('T')[0] : null,
            end_date: dateRange.value ? dateRange.value[1].toISOString().split('T')[0] : null,
            search: globalFilterValue.value,
        };
        const response = await axios.get('/venta', { params });
        inputs.value = response.data.data;
        pagination.value.currentPage = response.data.meta.current_page;
        pagination.value.total = response.data.meta.total;
        // Asignar los montos con 2 decimales
        tarjetaMonto.value = parseFloat(response.data.ingresos_por_pago.Tarjeta || 0.0).toFixed(2);
        transferenciaMonto.value = parseFloat(response.data.ingresos_por_pago.Transferencia || 0.0).toFixed(2);
        efectivoMonto.value = parseFloat(response.data.ingresos_por_pago.Efectivo || 0.0).toFixed(2);
        yapeMonto.value = parseFloat(response.data.ingresos_por_pago.Yape || 0.0).toFixed(2);
        plinMonto.value = parseFloat(response.data.ingresos_por_pago.Plin || 0.0).toFixed(2);
        MontoTotal.value = parseFloat(response.data.total_ingresos || 0.0).toFixed(2);
    } catch (error) {
        console.error('Error al cargar insumos:', error);
    } finally {
        loading.value = false;
    }
};

const verRecibo = async () => {
    if (selectedSale.value && selectedSale.value.order) {
        const idOrder = selectedSale.value.order.id; // Usar selectedSale para obtener el idOrder
        window.location.href = `/venta/pdf/${idOrder}`; // Redirigir al PDF
    } else {
        console.error('No se ha seleccionado ninguna venta.');
    }
};

// Método que se ejecuta cuando se selecciona un rango de fechas
const onDateRangeChange = () => {
    pagination.value.currentPage = 1; // Reiniciar la página al primer conjunto de resultados
    loadInputs();
};

// Watcher para que la tabla se actualice cada vez que el rango de fechas cambie
watch(dateRange, loadInputs, { immediate: true });

const props = defineProps({
    refresh: {
        type: Number,
        required: true,
    },
});
// Recarga la tabla cuando `refreshCount` cambia
watch(refreshCount, loadInputs);
watch(() => props.refresh, loadInputs);
watch(
    () => selectedEstadoInput.value,
    () => {
        currentPage.value = 1;
        loadInputs();
    },
);
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

const enviarASunat = async (idSale, prefix) => {
    // Validar que el prefijo sea uno de los valores permitidos (boleta o factura)
    if (prefix !== 'Boleta' && prefix !== 'Factura') {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'El tipo de comprobante debe ser "Boleta" o "Factura".',
            life: 3000
        });
        return;
    }

    try {
        // Datos a enviar
        const data = {
            prefix: prefix,  // El tipo de comprobante (Boleta o Factura)
        };

        // Enviar la solicitud POST a la API de Laravel para generar el comprobante
        const response = await axios.post(`/generate-invoice/${idSale}`, data);

        // Verificar si la respuesta es exitosa
        if (response.data.invoice) {


            console.log('Comprobante generado:', response.data.invoice);
 // Ahora enviar el idSale a la API /envio-sunat
            await axios.get(`/envio-sunat?idSale=${idSale}`)
                .then(() => {
                    console.log('idSale enviado a la API de SUNAT');
                       loadInputs();
                })
                .catch(error => {
                    console.error('Error al enviar idSale a SUNAT:', error);
              
                });
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Hubo un problema al generar el comprobante.',
                life: 3000
            });
        }
    } catch (error) {
        console.error('Error al crear el comprobante:', error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Hubo un error al procesar la solicitud.',
            life: 3000
        });
    }
};

</script>

<template>
    <div class="grid grid-cols-12 gap-8">
        <!-- Transferencia Card -->
        <div class="col-span-12 lg:col-span-6 xl:col-span-3">
            <div class="card mb-0">
                <div class="mb-4 flex justify-between">
                    <div>
                        <span class="mb-4 block font-medium text-muted-color">Transferencia</span>
                        <div class="text-xl font-medium text-surface-900 dark:text-surface-0">S/ {{ transferenciaMonto }}</div>
                    </div>
                    <div
                        class="flex items-center justify-center bg-blue-100 rounded-border dark:bg-blue-400/10"
                        style="width: 2.5rem; height: 2.5rem"
                    >
                        <i class="pi pi-send !text-xl text-blue-500"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-6 xl:col-span-3">
            <div class="card mb-0">
                <div class="mb-4 flex justify-between">
                    <div>
                        <span class="mb-4 block font-medium text-muted-color">Tarjeta</span>
                        <div class="text-xl font-medium text-surface-900 dark:text-surface-0">S/ {{ tarjetaMonto }}</div>
                    </div>
                    <div
                        class="flex items-center justify-center bg-blue-100 rounded-border dark:bg-blue-400/10"
                        style="width: 2.5rem; height: 2.5rem"
                    >
                        <i class="pi pi-credit-card !text-xl text-blue-500"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Efectivo Card -->
        <div class="col-span-12 lg:col-span-6 xl:col-span-3">
            <div class="card mb-0">
                <div class="mb-4 flex justify-between">
                    <div>
                        <span class="mb-4 block font-medium text-muted-color">Efectivo</span>
                        <div class="text-xl font-medium text-surface-900 dark:text-surface-0">S/ {{ efectivoMonto }}</div>
                    </div>
                    <div
                        class="flex items-center justify-center bg-blue-100 rounded-border dark:bg-blue-400/10"
                        style="width: 2.5rem; height: 2.5rem"
                    >
                        <i class="pi pi-money-bill !text-xl text-blue-500"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yape Card -->
        <div class="col-span-12 lg:col-span-6 xl:col-span-3">
            <div class="card mb-0">
                <div class="mb-4 flex justify-between">
                    <div>
                        <span class="mb-4 block font-medium text-muted-color">Yape</span>
                        <div class="text-xl font-medium text-surface-900 dark:text-surface-0">S/ {{ yapeMonto }}</div>
                    </div>
                    <div
                        class="flex items-center justify-center bg-blue-100 rounded-border dark:bg-blue-400/10"
                        style="width: 3rem; height: 2.5rem"
                    >
                        <!-- Ícono de billetera digital y la letra 'Y' al lado -->
                        <i class="pi pi-wallet !text-xl text-blue-500"></i>
                        <span class="text-xl font-medium text-blue-500 ml-1">Y</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plin Card -->
        <div class="col-span-12 lg:col-span-6 xl:col-span-3">
            <div class="card mb-0">
                <div class="mb-4 flex justify-between">
                    <div>
                        <span class="mb-4 block font-medium text-muted-color">Plin</span>
                        <div class="text-xl font-medium text-surface-900 dark:text-surface-0">S/ {{ plinMonto }}</div>
                    </div>
                    <div
                        class="flex items-center justify-center bg-blue-100 rounded-border dark:bg-blue-400/10"
                        style="width: 3rem; height: 2.5rem"
                    >
                        <!-- Ícono de billetera digital y la letra 'Y' al lado -->
                        <i class="pi pi-wallet !text-xl text-blue-500"></i>
                        <span class="text-xl font-medium text-blue-500 ml-1">P</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br />
    <span class="text-muted-color">El monto total es: </span> <span class="font-medium text-primary">S/ {{ MontoTotal }}</span>
    <br />
    <br />
    <div class="card">
        <DataTable
            ref="dt"
            v-model:selection="selectedInputs"
            :value="inputs"
            dataKey="id"
            :paginator="true"
            :rows="pagination.perPage"
            :totalRecords="pagination.total"
            :loading="loading"
            :lazy="true"
            @page="onPage"
            :rowsPerPageOptions="[15, 20, 25]"
            scrollable
            scrollHeight="574px"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} Ventas"
        >
            <template #header>
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h4 class="m-0">Ventas</h4>
                    <div class="flex flex-wrap gap-2">
                        <div class="flex gap-2">
                            <Calendar
                                v-model="dateRange"
                                selectionMode="range"
                                placeholder="Rango de fechas"
                                class="w-full"
                                dateFormat="yy-mm-dd"
                                @change="onDateRangeChange"
                            />
                        </div>
                        <IconField>
                            <InputIcon>
                                <i class="pi pi-search" />
                            </InputIcon>
                            <InputText v-model="globalFilterValue" @input="onGlobalSearch" placeholder="Buscar por DNI o RUC" />
                        </IconField>

                        <Button icon="pi pi-refresh" outlined rounded aria-label="Refresh" @click="loadInputs" />
                    </div>
                </div>
            </template>

            <Column selectionMode="multiple" style="width: 1rem" :exportable="false"></Column>

            <Column field="salesInvoice.serie" header="Nº" sortable style="min-width: 7rem" />
            <Column field="sale.customer.name" header="Cliente" sortable style="min-width: 10rem" />
            <Column field="order.numeroMesa" header="Nº Mesa" sortable style="min-width: 10rem" />
            <Column field="sale.documentType" header="Tipo" sortable style="min-width: 7rem" />
            <Column field="sale.paymentType" header="Metodo" sortable style="min-width: 7rem" />
            <Column field="subtotal" header="Total" sortable style="min-width: 7rem" />
<Column field="sale.stateSunat" header="Sunat" sortable style="min-width: 7rem">
    <template #body="{ data }">
        <!-- Si está aprobado, muestra en badge verde -->
        <span
            v-if="data.sale.stateSunat === 'aprobado'"
            class="sunat-badge"
        >
            {{ data.sale.stateSunat }}
        </span>
        <!-- Si tiene otro texto distinto a 'no enviado', muéstralo normal -->
        <span
            v-else-if="data.sale.stateSunat && data.sale.stateSunat !== 'no enviado'"
        >
            {{ data.sale.stateSunat }}
        </span>
        <!-- Solo mostrar el botón si está vacío, nulo o 'no enviado' -->
        <Button
            v-if="!data.sale.stateSunat || data.sale.stateSunat === 'no enviado'"
            label="Enviar"
            icon="pi pi-send"
            @click="enviarASunat(data.sale.id,data.sale.documentType)"
            class="p-button-text p-button-sm"
        />
    </template>
</Column>


           <Column field="sale.created_at" header="Creación" sortable style="min-width: 13rem" />
            <Column field="sale.updated_at" header="Actualización" sortable style="min-width: 13rem" />
            <Column field="accions" header="Acciones" :exportable="false" style="min-width: 8rem">
                <template #body="{ data }">
                    <Button icon="pi pi-eye" outlined rounded @click="verDetalle(data)" />
                </template>
            </Column>
        </DataTable>
    </div>
    <Dialog v-model:visible="detailsDialog" header="Detalles de Venta" :closable="false">
        <div v-if="selectedSale">
            <!-- Contenedor para los datos del cliente en fila horizontal -->
            <div class="flex flex-wrap gap-4">
                <p><strong>Cliente:</strong> {{ selectedSale.sale.customer.name }}</p>
                <p><strong>Código:</strong> {{ selectedSale.sale.customer.codigo }}</p>
                <p><strong>Comprobante:</strong> {{ selectedSale.salesInvoice.serie }}</p>
                <p><strong>Documento:</strong> {{ selectedSale.sale.documentType }}</p>
                <p><strong>Nº Order:</strong> {{ selectedSale.order.id }}</p>
                <p><strong>Mesa:</strong> {{ selectedSale.order.numeroMesa }}</p>
                <p><strong>Estado:</strong> {{ selectedSale.order.state }}</p>
            </div>

            <!-- DataTable para mostrar los platos -->
            <DataTable :value="selectedSale.order.orderDishes" :responsive="true">
                <Column field="name" header="Plato" />
                <Column field="quantity" header="Cantidad" />
                <Column field="price" header="Precio" />
                <Column field="subtotal" header="Subtotal" />
                <Column field="state" header="Estado" />
                <Column field="creacion" header="Fecha" />
            </DataTable>

            <!-- Total alineado a la derecha -->
            <br />
            <p style="text-align: right; font-weight: bold"><strong>Total:</strong> {{ selectedSale.subtotal }}</p>

            <!-- Botones para Ver Recibo o Cerrar -->
            <div class="mt-4 flex justify-end gap-3">
                <Button label="Ver Recibo" icon="pi pi-file-pdf" @click="verRecibo" />
                <Button label="Cerrar" icon="pi pi-times" @click="detailsDialog = false" class="p-button-text" />
            </div>
        </div>
    </Dialog>
</template>
<style scoped>
.sunat-badge {
    display: inline-block;
    background: #eaffea;
    color: #138a27;
    font-weight: bold;
    border-radius: 12px;
    padding: 0.2em 0.5em;
    font-size: 0.8em;
    border: 1.5px solid #b7f5c0;
    font-family: inherit;
    margin-left: 2px;
    margin-right: 2px;
}
</style>