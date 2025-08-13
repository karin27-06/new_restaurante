<template>
  <Head title="Aperturar Caja" />
  <AppLayout>
    <div class="card">
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
            <Select 
              v-model="cajaSeleccionada"
              :options="cajasDisponibles"
              optionLabel="numero_cajas"
              optionValue="id"
              placeholder="Seleccione una caja"
              class="w-full"
              :disabled="cajasDisponibles.length === 0"
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
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import AppLayout from '@/layout/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import Select from 'primevue/dropdown';
import Button from 'primevue/button';
import axios from 'axios';

const toast = useToast();
const username = ref('');  // Variable para almacenar el nombre completo del usuario autenticado
const cajaSeleccionada = ref(null);
const cajasDisponibles = ref([]);

// Obtener el nombre del vendedor y las cajas disponibles cuando se monta el componente
onMounted(async () => {
  try {
    // Obtener el user_id, name y apellidos desde la API
    const userResponse = await axios.get('/user-id');  // Solicitud a la API /user-id

    if (userResponse.data.success) {
      // Guardamos el nombre completo (name + apellidos) en la variable username
      username.value = `${userResponse.data.name} ${userResponse.data.apellidos}`;
      console.log('Nombre del usuario:', username.value);  // Imprimir en la consola el nombre completo
    }

    // Obtener cajas disponibles (sin ocupar)
    const cajasResponse = await axios.get('/caja/disponibles');
    cajasDisponibles.value = cajasResponse.data.map(caja => ({
      id: caja.id,
      numero_cajas: caja.numero_cajas,
      state: caja.state
    }));
  } catch (error) {
    console.error('Error cargando datos:', error);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudieron cargar los datos necesarios',
      life: 3000
    });
  }
});

// Aperturar caja
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

   // Redirigir a la vista de cajas
    window.location.href = '/cajas';
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: error.response?.data?.message || 'Error al aperturar la caja',
      life: 3000
    });
  }
};
</script>
