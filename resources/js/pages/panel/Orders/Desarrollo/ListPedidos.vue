<script setup>
import axios from 'axios';
import { debounce } from 'lodash';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';
import { onMounted, ref, watch } from 'vue';
import Dropdown from 'primevue/dropdown';
import Dialog  from 'primevue/dialog';

// Initialize toast
const toast = useToast();

const dt = ref();
const ordenes = ref([]);
const selectedOrdenes = ref();
const loading = ref(false);
const globalFilterValue = ref('');
const orden = ref({});
const selectedOrdenId = ref(null);
const selectedEstadoOrden = ref(null);
const currentPage = ref(1);
const selectedState = ref(null); // Filtro para el estado
const stateOptions = ref([
    { label: 'Todos los estados', value: null },
    { label: 'Pendiente', value: 'pendiente' },
    { label: 'En Preparación', value: 'en preparación' },
    { label: 'En Entrega', value: 'en entrega' },
    { label: 'Completado', value: 'completado' },
    { label: 'Cancelado', value: 'cancelado' },
]);


const selectedOrder = ref({});
const showActionsDialog = ref(false);

function showActionMenu(order) {
    selectedOrder.value = order;
    showActionsDialog.value = true;
}

async function fetchUserId() {
    try {
        // Hacemos la solicitud al backend para obtener el user_id
        const { data } = await axios.get('/user-id');

        // Verificamos si la solicitud fue exitosa
        if (data.success) {
            return data.user_id; // Retornamos el user_id
        } else {
            console.error("Error al obtener el user_id");
            return null;
        }
    } catch (e) {
        console.error("Error en la solicitud:", e);
        return null;
    }
}
async function obtenerInsumosPorPedido(idOrder) {
    const userId = await fetchUserId(); // Esperar a obtener el user_id

    if (!userId) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo obtener el user_id' });
        return; // Si no se obtiene el user_id, no continuar con el registro
    }
    try {
        // Primero, obtenemos los detalles del pedido usando la API /orders
        const orderResponse = await axios.get(`/orders?id=${idOrder}`);
        const orderDishes = orderResponse.data.data[0].orderDishes; // Suponiendo que solo hay una orden

        // Ahora, para cada orderDish, obtenemos los detalles del plato utilizando la API /plato?id=
        for (const dish of orderDishes) {
            const dishId = dish.idDish;
            const dishResponse = await axios.get(`/plato?id=${dishId}`);
            const plato = dishResponse.data.data[0]; // Suponiendo que siempre existe el plato

            // Verificamos si el plato tiene insumos
            if (plato.insumos && plato.insumos.length > 0) {
                for (const insumo of plato.insumos) {
                    // Ahora accedemos al idInput del insumo
                    const kardexInput = {
                        idUser: userId,  // idUser con valor 2
                        idInput: insumo.id,  // Correcto, aquí accedemos al id del insumo
                        idMovementInput: null,  // idMovementInput es null
                        totalPrice: null,  // totalPrice es null
                        movement_type: 1  // movement_type es 1
                    };

                    // Imprimimos los datos antes de enviarlos a la API kardex
                    console.log('Datos a insertar en kardex_inputs:', kardexInput);

                    // Enviamos el objeto a la API /insumos/karde
                    try {
                        const response = await axios.post('/insumos/karde', kardexInput);
                        console.log('Insumo insertado en kardex:', response.data);
                    } catch (error) {
                        console.error('Error al insertar insumo en kardex:', error);
                    }

                    // Ahora, agregamos los detalles del movimiento de insumo
                    const movimientoDetalle = {
                        idMovementInput: null,  // idMovementInput es null
                        idInput: insumo.id,  // Ahora usamos insumo.id
                        quantity: 1,  // Cantidad es 1
                        totalPrice: null,  // totalPrice es null
                        priceUnit: null,  // priceUnit es null
                        batch: null,  // batch es null
                        expirationdate: null  // expirationdate es null
                    };

                    // Imprimimos los datos antes de enviarlos a la API de movimiento de insumos
                    console.log('Datos a insertar en /insumos/movimientos/detalle:', movimientoDetalle);

                    // Enviamos el objeto a la API /insumos/movimientos/detalle/{idDish}
                    try {
                        const detalleResponse = await axios.post(`/insumos/movimientos/detalle`, movimientoDetalle);
                        console.log('Detalle de movimiento de insumo insertado:', detalleResponse.data);
                    } catch (error) {
                        console.error('Error al insertar detalle de movimiento de insumo:', error);
                    }
                }
            } else {
                console.log(`El plato ${plato.name} no tiene insumos.`);
            }
        }
    } catch (error) {
        console.error('Error al obtener los insumos:', error);
    }
}








async function updateOrderState(newState) {



    try {
        const response = await axios.put(`/order-dishes/${selectedOrder.value.id}`, { state: newState });

 // Si el nuevo estado es "completado", obtenemos los insumos
        if (newState === 'completado') {
            await obtenerInsumosPorPedido(selectedOrder.value.id);
        }

        toast.add({ severity: 'success', summary: 'Éxito', detail: `Estado del pedido actualizado a ${newState}`, life: 3000 });
        loadOrdenes(); // Recargar la lista de órdenes
        showActionsDialog.value = false; // Cerrar el diálogo
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo actualizar el estado', life: 3000 });
    }
}
const props = defineProps({
    refresh: {
        type: Number,
        required: true,
    },
});
watch(
    () => props.refresh,
    () => {
        loadOrdenes();
    },
);
watch(
    () => selectedEstadoOrden.value,
    () => {
        currentPage.value = 1;
        loadOrdenes();
    },
);

function editOrden(orden) {
    selectedOrdenId.value = orden.id;
    updateOrdenDialog.value = true;
}

const estadoOrdenOptions = ref([
    { name: 'TODOS', value: '' },
    { name: 'ACTIVOS', value: 1 },
    { name: 'INACTIVOS', value: 0 },
]);

function handleOrdenUpdated() {
    loadOrdenes();
}
const onStateChange = async () => {
    await loadOrdenes();
};
function confirmDeleteOrden(selected) {
    orden.value = selected;
    deleteOrdenDialog.value = true;
}

const pagination = ref({
    currentPage: 1,
    perPage: 15,
    total: 0,
});

const filters = ref({
    state: null,
});

function handleOrdenDeleted() {
    loadOrdenes();
}

const loadOrdenes = async () => {
    loading.value = true;
    try {
        const params = {
            page: pagination.value.currentPage,
            per_page: pagination.value.perPage,
            search: globalFilterValue.value,
            state: selectedState.value, 

        };
        if (selectedEstadoOrden.value !== null && selectedEstadoOrden.value !== undefined && selectedEstadoOrden.value.value !== '') {
            params.state = selectedEstadoOrden.value.value;
        }

        const response = await axios.get('/order-dishes', { params });

        // Check if response.data exists and has the expected structure
        if (response.data && response.data.data) {
            ordenes.value = response.data.data;

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
            ordenes.value = [];
            pagination.value.total = 0;
        }
    } catch (error) {
        console.error('Error al cargar ordenes:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar los ordenes', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const onPage = (event) => {
    pagination.value.currentPage = event.page + 1;
    pagination.value.perPage = event.rows;
    loadOrdenes();
};

const getSeverity = (value) => {
    if (value === true || value === '1') return 'success';
    if (value === false || value === '0') return 'danger';
    return null;
};

const onGlobalSearch = debounce(() => {
    pagination.value.currentPage = 1;
    loadOrdenes();
}, 500);

const formatCurrency = (value) => {
    if (value != null) {
        return 'S/. ' + parseFloat(value).toFixed(2);
    }
    return '';
};

onMounted(() => {
    loadOrdenes();
});
</script>

<template>
    <DataTable
        ref="dt"
        v-model:selection="selectedOrdenes"
        :value="ordenes"
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
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} de Pedidos"
    >
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h4 class="m-0">Lista de Pedidos</h4>
                <div class="flex flex-wrap gap-2">
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="globalFilterValue" @input="onGlobalSearch" placeholder="Buscar..." />
                    </IconField>
                          <Dropdown
                    v-model="selectedState"
                    :options="stateOptions"
                    option-label="label"
                    option-value="value"
                    placeholder="Selecciona un Estado"
                    class="w-1/1"
                    @change="onStateChange"
                />
                    <Button icon="pi pi-refresh" outlined rounded aria-label="Refresh" @click="loadOrdenes" />
                </div>
            </div>
        </template>

        <Column selectionMode="multiple" style="width: 1rem" :exportable="false"></Column>
        <Column field="id" header="Nº Orden" sortable style="min-width: 5rem"></Column>
        <Column field="numeroMesa" header="Nº Mesa" sortable style="min-width: 7rem"></Column>
        <Column field="name" header="Platillo" sortable style="min-width: 7rem"></Column>
        <Column field="quantity" header="Cantidad" sortable style="min-width: 7rem"></Column>
 <Column field="state" header="Estado" sortable style="min-width: 8rem" >
            <template #body="{ data }">
                <Tag :value="data.state" :severity="getSeverity(data.state)" />
                
            </template>
        </Column>
        <Column field="creacion" header="Creación" sortable style="min-width: 10rem"></Column>
       <Column header="Acciones" style="min-width: 8rem">
            <template #body="{ data }">
                <Button 
                    icon="pi pi-ellipsis-v" 
                    class="p-button-rounded p-button-text" 
                    @click="showActionMenu(data)"
                />
            </template>
        </Column>
    </DataTable>

      <!-- Menú de acciones -->
    <Dialog v-model:visible="showActionsDialog" header="Acciones" :style="{ width: '300px' }">
        <div>
            <Button label="Preparar Pedido" v-if="selectedOrder.state === 'pendiente'" @click="updateOrderState('en preparación')" style="margin-right: 10px;" />
            <Button label="Cancelar Pedido" v-if="selectedOrder.state === 'pendiente'" @click="updateOrderState('cancelado')" />
            <Button label="Marcar como En Entrega" v-if="selectedOrder.state === 'en preparación'" @click="updateOrderState('en entrega')" style="margin-right: 10px;" />
            <Button label="Marcar como Completado" v-if="selectedOrder.state === 'en entrega'" @click="updateOrderState('completado')" />
        </div>
    </Dialog>

</template>
