<script setup>
import AppLayout from '@/layout/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import Calendar from 'primevue/calendar';
import Chart from 'primevue/chart';
import Button from 'primevue/button';
import Column from 'primevue/column';
import Message from 'primevue/message';
import DataTable from 'primevue/datatable';
import { onMounted, ref, watch } from 'vue';
import Password from './settings/Password.vue';
const page = usePage();
const mustReset = page.props.mustReset;
// Obtener el usuario autenticado
const user = page.props.auth.user;
//DEJA AHI EL MENSAJE DE SALUDO :)
// Función para determinar el saludo
const getGreeting = () => {
  const hours = new Date().getHours();
  if (hours < 12) {
    return 'Buenos días';
  } else if (hours < 18) {
    return 'Buenas tardes';
  } else {
    return 'Buenas noches';
  }
};
// Mensajes motivacionales
const motivationalMessages = [
  "¡Estás haciendo un gran trabajo, sigue así!",
  "¡Hoy es un buen día para alcanzar tus metas!",
  "¡La perseverancia te lleva lejos, nunca pares!",
  "¡Cada paso te acerca más a tu éxito!"
];

// Escoger un mensaje aleatorio y almacenarlo en una variable reactiva
const motivationalMessage = getMotivationalMessage();

// Función para obtener un mensaje aleatorio
function getMotivationalMessage() {
  const randomIndex = Math.floor(Math.random() * motivationalMessages.length);
  return motivationalMessages[randomIndex];
}
const dashboardData = ref({
    totales: {
        total_customers: 0,
        total_orders: 0,
        total_dishes: 0,
        total_income: '0.00',
    },
    total_in_range: {
        total_customers: 0,
        total_orders: 0,
        total_dishes: 0,
        total_income: '0.00',
    },
    payment_method_stats: {
        Efectivo: 0,
        Transferencia: 0,
        Yape: 0,
        Plin: 0,
        Tarjeta: 0,
    },
});
const frequentTables = ref([]);

const dateRange = ref(null); // Rango de fechas
// Función para cargar los datos del dashboard





// Preparar los datos para el gráfico de barras
const paymentMethodLabels = ['Efectivo', 'Transferencia', 'Yape', 'Plin', 'Tarjeta'];
const paymentMethodData = ref([0, 0, 0, 0, 0]);

const paymentMethodChartData = ref({
    labels: paymentMethodLabels,
    datasets: [
        {
            label: 'Métodos de Pago',
            data: paymentMethodData.value,
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
            borderColor: '#fff',
            borderWidth: 1,
        },
    ],
});

watch(
  () => dashboardData.value.payment_method_stats,
  (newStats) => {
    paymentMethodData.value = [
      newStats.Efectivo,
      newStats.Transferencia,
      newStats.Yape,
      newStats.Plin,
      newStats.Tarjeta
    ];
    paymentMethodChartData.value.datasets[0].data = paymentMethodData.value;
  },
  { immediate: true }
);
const loadDashboardData = async (startDate, endDate) => {
  try {
    console.log("Fechas seleccionadas:", startDate, endDate); // Verificar que las fechas sean correctas
    const params = {
      start_date: startDate ? startDate.toISOString().split('T')[0] : null,
      end_date: endDate ? endDate.toISOString().split('T')[0] : null
    };

    const response = await axios.get('/datos/dashboard', { params });
    dashboardData.value = response.data;

    // Asignar puesto basado en el índice (orden), sin usar frequency
    frequentTables.value = response.data.frequent_tables
      .map((table, index) => ({
        tablenum: table,
        puesto: index + 1,
      }));
  } catch (error) {
    console.error('Error al obtener los datos del dashboard:', error);
  }
};
// Función que se ejecuta al hacer clic en el botón para actualizar los datos
const onUpdateDataClick = () => {
  if (dateRange.value && dateRange.value.length === 2) {
    loadDashboardData(dateRange.value[0], dateRange.value[1]); // Llamamos a la función con las fechas seleccionadas
  }
};
// Función que se ejecuta cuando se selecciona un rango de fechas
const onDateRangeChange = () => {
  console.log("Date range changed:", dateRange.value); // Depurar para ver si se dispara el evento
  if (dateRange.value && dateRange.value.length === 2) {
    loadDashboardData(dateRange.value[0], dateRange.value[1]); // Llamamos a la función con las fechas seleccionadas
  }
};

// Cargar los datos cuando el componente se monta
onMounted(() => {
  console.log("Component mounted, loading dashboard data...");
  loadDashboardData(); // Cargar los datos al montar el componente
});
</script>

<template>
    <Head title="Dashboard" />
    <div v-if="mustReset">
        <div>
            <Password />
        </div>
    </div>
    <AppLayout v-else>
        <div class="card">
      <!-- Saludo y nombre completo del usuario -->
      <h2>{{ getGreeting() }}, {{ user.name }}!</h2>
      <br>
      <!-- Mensaje motivacional -->
      <Message severity="info">{{ motivationalMessage }}</Message>
    </div>
         <div class="grid grid-cols-4 gap-8">
      <!-- Calendar component -->
      <Calendar
        v-model="dateRange"
        selectionMode="range"
        placeholder="Rango de fechas"
        class="w-full"
        dateFormat="yy-mm-dd"
      />
      <!-- Button to trigger the data load -->
      <Button label="Filtrar" @click="onUpdateDataClick" class="p-button p-component" />

    </div>


        <br />
        <div class="grid grid-cols-12 gap-8">
            <!-- Revenue Card -->
            <div class="col-span-12 lg:col-span-6 xl:col-span-3">
                <div class="card mb-0">
                    <div class="mb-4 flex justify-between">
                        <div>
                            <span class="mb-4 block font-medium text-muted-color">Ingresos Totales</span>
                            <div class="text-xl font-medium text-surface-900 dark:text-surface-0">S/{{ dashboardData.totales.total_income }}</div>
                        </div>
                        <div
                            class="flex items-center justify-center bg-orange-100 rounded-border dark:bg-orange-400/10"
                            style="width: 2.5rem; height: 2.5rem"
                        >
                            <i class="pi pi-dollar !text-xl text-orange-500"></i>
                        </div>
                    </div>
                    <span class="font-medium text-primary">S/{{ dashboardData.total_in_range.total_income }} </span
                    ><span class="text-muted-color"> filtrados</span>
                </div>
            </div>

            <!-- Customers Card -->
            <div class="col-span-12 lg:col-span-6 xl:col-span-3">
                <div class="card mb-0">
                    <div class="mb-4 flex justify-between">
                        <div>
                            <span class="mb-4 block font-medium text-muted-color">Nº Clientes</span>
                            <div class="text-xl font-medium text-surface-900 dark:text-surface-0">{{ dashboardData.totales.total_customers }}</div>
                        </div>
                        <div
                            class="flex items-center justify-center bg-cyan-100 rounded-border dark:bg-cyan-400/10"
                            style="width: 2.5rem; height: 2.5rem"
                        >
                            <i class="pi pi-users !text-xl text-cyan-500"></i>
                        </div>
                    </div>
                    <span class="font-medium text-primary">{{ dashboardData.total_in_range.total_customers }} </span
                    ><span class="text-muted-color"> nuevos filtrados</span>
                </div>
            </div>
            <!-- Orders Card -->
            <div class="col-span-12 lg:col-span-6 xl:col-span-3">
                <div class="card mb-0">
                    <div class="mb-4 flex justify-between">
                        <div>
                            <span class="mb-4 block font-medium text-muted-color">Nº Ordenes</span>
                            <div class="text-xl font-medium text-surface-900 dark:text-surface-0">{{ dashboardData.totales.total_orders }}</div>
                        </div>
                        <div
                            class="flex items-center justify-center bg-blue-100 rounded-border dark:bg-blue-400/10"
                            style="width: 2.5rem; height: 2.5rem"
                        >
                            <i class="pi pi-shopping-cart !text-xl text-blue-500"></i>
                        </div>
                    </div>
                    <span class="font-medium text-primary">{{ dashboardData.total_in_range.total_orders }}</span
                    ><span class="text-muted-color"> filtrados</span>
                </div>
            </div>
            <!-- Comments Card -->
            <div class="col-span-12 lg:col-span-6 xl:col-span-3">
                <div class="card mb-0">
                    <div class="mb-4 flex justify-between">
                        <div>
                            <span class="mb-4 block font-medium text-muted-color">Nº Platillos</span>
                            <div class="text-xl font-medium text-surface-900 dark:text-surface-0">{{ dashboardData.totales.total_dishes }}</div>
                        </div>
                        <div
                            class="flex items-center justify-center bg-purple-100 rounded-border dark:bg-purple-400/10"
                            style="width: 2.5rem; height: 2.5rem"
                        >
                            <i class="pi pi-comment !text-xl text-purple-500"></i>
                        </div>
                    </div>
                    <span class="font-medium text-primary">{{ dashboardData.total_in_range.total_dishes }} </span
                    ><span class="text-muted-color"> filtrados</span>
                </div>
            </div>

            <!-- Revenue Stream Chart -->
            <div class="col-span-12 xl:col-span-6">
                <div class="card">
                    <div class="mb-4 text-xl font-semibold">FRECUENCIA DE LOS METODOS DE PAGO</div>
                    <!-- Gráfico de Barras -->
                    <Chart type="bar" :data="paymentMethodChartData" />
                </div>
            </div>

            <!-- Mesas Frecuentes -->
            <div class="col-span-12 xl:col-span-6">
                <div class="card">
                    <div class="mb-4 text-xl font-semibold">Mesas Frecuentes</div>
                    <DataTable :value="frequentTables" :rows="5" class="p-datatable-sm">
                        <Column field="puesto" header="Puesto" />
                        <Column field="tablenum" header="Número de Mesa" />
                    </DataTable>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
body {
    overflow-x: hidden; /* Previene el desbordamiento horizontal */
}
.p-datatable-table {
    table-layout: fixed;
    width: 100%;
}
</style>
