<template>
  <Head title="Aperturar caja" />
  <AppLayout>
    <div>
      <template v-if="isLoading">
        <Espera />
      </template>

      <!-- Si no tiene caja, muestra la opción de aperturar -->
      <template v-else>
        <div v-if="showAperturaCaja" class="card">
          <h1 class="text-2xl font-bold mb-6">Aperturar Caja</h1>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Sección de Ingreso -->
            <div>
              <h2 class="text-lg font-semibold mb-4">Ingreso</h2>
              <div class="mb-4">
                <label class="block font-medium mb-2">Vendedor:</label>
                <InputText 
                  :value="username" 
                  class="w-full bg-gray-100" 
                  readonly 
                />
              </div>
            </div>

            <!-- Sección de Caja -->
            <div>
              <h2 class="text-lg font-semibold mb-4">Caja</h2>
              <div class="mb-4">
              <label class="block font-medium mb-2">Seleccionar caja:</label>
              <!-- Selecciona la caja con filtro habilitado -->
              <Dropdown 
                  v-model="cajaSeleccionada"
                  :options="cajasDisponibles"
                  optionLabel="numero_cajas"
                  optionValue="id"
                  placeholder="Seleccione una caja"
                  class="w-full"
                  :disabled="cajasDisponibles.length === 0"
                  filter
                  filterPlaceholder="Buscar caja..."
              />
              <small v-if="cajasDisponibles.length === 0" class="text-red-500">
                  No hay cajas disponibles para aperturar
              </small>
          </div>

              <Button 
                label="Aperturar Caja" 
                icon="pi pi-lock-open" 
                severity="success"
                class="w-full"
                :disabled="!cajaSeleccionada"
                @click="aperturarCaja"
              />
            </div>
          </div>
        </div>

        <!-- Si ya tiene una caja, muestra el listado de mesas -->
        <ListMesas :refresh="refreshKey" v-if="!showAperturaCaja" />
      </template>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { useToast } from 'primevue/usetoast';
import { ref, onMounted } from 'vue';
import AppLayout from '@/layout/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import Espera from '@/components/Espera.vue';
import ListMesas from './Desarrollo/ListMesas.vue';
import axios from 'axios'; // Asegúrate de importar axios si no lo has hecho aún
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';  // Importamos Dropdown
import Button from 'primevue/button';
const toast = useToast();

const isLoading = ref(true);
const refreshKey = ref(0);
const showAperturaCaja = ref(false); // Variable para controlar la visibilidad de la apertura de caja
const username = ref('');
const cajaSeleccionada = ref(null);
const cajasDisponibles = ref([]);

// Función para refrescar el listado de mesas
function refrescarListado() {
    refreshKey.value++;
}

// Función para verificar si el usuario tiene una caja activa y decidir qué mostrar
const goToApertura = async () => { 
  try {
    const response = await axios.get('/caja/mi-caja-activa');
    
    if (response.data.caja) {
      // Si tiene una caja activa, mostrar el listado de mesas
      isLoading.value = false;
    } else {
      // Si no tiene caja, mostrar la opción para aperturar caja
      showAperturaCaja.value = true;
      isLoading.value = false;
    }
  } catch (error) {
    console.error('Error verificando caja:', error);
    showAperturaCaja.value = true;  // Fallback por si hay un error
    isLoading.value = false;
  }
};

// Función para aperturar la caja
const aperturarCaja = async () => {
  if (!cajaSeleccionada.value) {
    toast.add({
      severity: 'warn',
      summary: 'Advertencia',
      detail: 'Por favor seleccione una caja',
      life: 3000
    });
    return;
  }

  const cajaSeleccionadaObj = cajasDisponibles.value.find(caja => caja.id === cajaSeleccionada.value);

  if (cajaSeleccionadaObj.state === false) {
    toast.add({
      severity: 'warn',
      summary: 'Advertencia',
      detail: 'La caja seleccionada está ocupada',
      life: 3000
    });
    return;
  }

  try {
    // Realizar la solicitud para aperturar la caja
    await axios.post('/caja/aperturar-caja', {
      caja_id: cajaSeleccionada.value
    });

    toast.add({
      severity: 'success',
      summary: 'Éxito',
      detail: 'Caja aperturada correctamente',
      life: 3000
    });

    // Después de aperturar, muestra el listado de mesas
    showAperturaCaja.value = false;
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: error.response?.data?.message || 'Error al aperturar la caja',
      life: 3000
    });
  }
};

// Obtener datos al montar el componente
onMounted(async () => {
  try {
    // Obtener el nombre del vendedor
    const userResponse = await axios.get('/user-id');
    if (userResponse.data.success) {
      username.value = `${userResponse.data.name} ${userResponse.data.apellidos}`;
    }

    // Obtener cajas disponibles
    const cajasResponse = await axios.get('/caja/disponibles');
    cajasDisponibles.value = cajasResponse.data.map(caja => ({
      id: caja.id,
      numero_cajas: caja.numero_cajas,
      state: caja.state
    }));

    // Realizar la verificación de la caja activa
    goToApertura();
  } catch (error) {
    console.error('Error cargando datos:', error);
    isLoading.value = false;
  }
});
</script>
