<template>
  <AppLayout>
    <Head title="Cierre de caja" />
    <div class="card">
      <h1 class="text-2xl font-bold mb-6">Cerrar Caja</h1>

      <!-- Mostrar pantalla de espera mientras se cargan los datos o se realiza una acción -->
      <template v-if="isLoading">
        <Espera />
      </template>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Sección de Salida -->
        <div>
          <h2 class="text-lg font-semibold mb-4">Salida</h2>
          <div class="mb-4">
            <label class="block font-medium mb-2">Vendedor:</label>
            <InputText 
              :value="vendedor" 
              class="w-full bg-gray-100" 
              readonly 
            />
          </div>
        </div>

        <!-- Sección de Caja -->
        <div>
          <h2 class="text-lg font-semibold mb-4">Caja N° {{ cajaNumero }}</h2>

          <!-- Mostrar si no hay caja activa -->
          <div v-if="!cajaActiva">
            <span class="text-red-500">No hay caja activa</span>
          </div>

          <!-- Mostrar los inputs solo si hay una caja activa -->
          <div v-else>
            <div class="mb-4">
              <label class="block font-medium mb-2">Efectivo:</label>
              <InputNumber 
                v-model="montoEfectivo" 
                class="w-full" 
                placeholder="Ingresar monto en efectivo" 
                :minFractionDigits="2"
                :maxFractionDigits="2"
                mode="currency"
                currency="PEN"
                locale="es-PE"
                min="0" 
              />
            </div>
            <div class="mb-4">
              <label class="block font-medium mb-2">Tarjeta:</label>
              <InputNumber
                v-model="montoTarjeta" 
                class="w-full" 
                placeholder="Ingresar monto en tarjeta" 
                :minFractionDigits="2"
                :maxFractionDigits="2"
                mode="currency"
                currency="PEN"
                locale="es-PE"
                min="0" 
              />
            </div>
            <div class="mb-4">
              <label class="block font-medium mb-2">Yape o Plin:</label>
              <InputNumber 
                v-model="montoYape" 
                class="w-full" 
                placeholder="Ingresar monto en Yape o Plin" 
                :minFractionDigits="2"
                :maxFractionDigits="2"
                mode="currency"
                currency="PEN"
                locale="es-PE"
                min="0" 
              />
            </div>
            <div class="mb-4">
              <label class="block font-medium mb-2">Transferencia:</label>
              <InputNumber 
                v-model="montoTransferencia" 
                class="w-full" 
                placeholder="Ingresar monto en Transferencia" 
                :minFractionDigits="2"
                :maxFractionDigits="2"
                mode="currency"
                currency="PEN"
                locale="es-PE"
                min="0" 
              />
            </div>

            <Button 
              label="Cerrar Caja" 
              icon="pi pi-check" 
              severity="danger" 
              class="w-full mt-4" 
              @click="cerrarCaja" 
            />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import AppLayout from '@/layout/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import Espera from '@/components/Espera.vue';
import InputNumber from 'primevue/inputnumber';
import { router } from '@inertiajs/core';

const toast = useToast();

const isLoading = ref(false);

const vendedor = ref('');
const cajaNumero = ref(null);
const cajaId = ref('');
const cajaActiva = ref(false);
const montoEfectivo = ref('');
const montoTarjeta = ref('');
const montoYape = ref('');
const montoTransferencia = ref('');

// Obtener datos del vendedor y caja activa desde la API
onMounted(async () => {
  isLoading.value = true;  // Empieza el estado de carga
  try {
    const response = await axios.get('/caja/mi-caja-activa');
    if (response.data.state) {
      // Asignamos el ID de la caja activa y el nombre del vendedor
      vendedor.value = response.data.caja.vendedorNombre;
      cajaId.value = response.data.caja.id;  // Ahora 'cajaId' contiene el ID de la caja, no el número visual
      cajaNumero.value = response.data.caja.numero_cajas;  // 'cajaNumero' muestra el número visible de la caja
      cajaActiva.value = true;
    } else {
      vendedor.value = response.data.vendedorNombre;  // Si no hay caja activa
      cajaActiva.value = false;
    }
  } catch (error) {
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudo cargar la información de la caja activa.',
      life: 3000
    });
  }finally {
    isLoading.value = false;  // Finaliza el estado de carga
  }
});

const cerrarCaja = async () => {
  // Validación: los montos no pueden ser negativos
  if (
    montoEfectivo.value < 0 ||
    montoTarjeta.value < 0 ||
    montoYape.value < 0 ||
    montoTransferencia.value < 0
  ) {
    toast.add({
      severity: 'warn',
      summary: 'Advertencia',
      detail: 'Los montos no pueden ser negativos.',
      life: 3000
    });
    return;
  }
  
  if (!cajaActiva.value) {
    toast.add({
      severity: 'warn',
      summary: 'Advertencia',
      detail: 'No se puede cerrar una caja que no está activa.',
      life: 3000
    });
    return;
  }

  try {
    // Cambia a PUT y a la ruta correcta
    const response = await axios.put(`/caja/cerrar/${cajaId.value}`, {
      monto_efectivo: montoEfectivo.value,
      monto_tarjeta: montoTarjeta.value,
      monto_yape: montoYape.value,
      monto_transferencia: montoTransferencia.value,
    });

    if (response.data.state) {
      toast.add({
        severity: 'success',
        summary: 'Caja cerrada',
        detail: 'El reporte de la caja ha sido actualizado y la caja ha sido liberada.',
        life: 3000
      });
      // Redirigir o actualizar
      const url = '/ordenes/mesas'
      router.visit(url);
    } else {
      toast.add({
        severity: 'error',
        summary: 'Error',
        detail: response.data.message || 'No se pudo cerrar la caja.',
        life: 3000
      });
    }
  } catch (error) {
    console.error(error.response?.data);
    toast.add({
      severity: 'error',
      summary: 'Error',
      detail: 'No se pudo cerrar la caja y actualizar el reporte.',
      life: 3000
    });
  }
};

</script>

<style scoped>
.card {
  padding: 20px;
}

button {
  margin-top: 20px;
}
</style>
