<template>
    <Head title="Cajas" />
    <AppLayout>
        <div>
            <template v-if="isLoading">
                <Espera/>
            </template>

            <template v-else>
                <div class="card">
                        <AddCajas @caja-agregada="refrescarListado"/>
                        <!--<Button 
                            label="Ir a ventas" 
                            icon="pi pi-lock-open" 
                            severity="success"
                            @click="goToApertura"
                        />
                        <Button 
                            label="Cerrar Caja" 
                            icon="pi pi-lock" 
                            severity="danger" 
                            @click="goToCerrarCaja" 
                        />-->
                    <ListCajas :refresh="refreshKey"/>
                </div>
            </template>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
//import { router } from '@inertiajs/vue3';
import AppLayout from '@/layout/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import Espera from '@/components/Espera.vue';
import ListCajas from './Desarrollo/ListCajas.vue';
import AddCajas from './Desarrollo/AddCajas.vue';
import Button from 'primevue/button';
import axios from 'axios';

const isLoading = ref(true);
const refreshKey = ref(0);

function refrescarListado() {
    refreshKey.value++;
}
/*const goToApertura = async () => {
  try {
    // Verificar si el usuario ya tiene una caja ocupada
    const response = await axios.get('/caja/mi-caja-activa');
    
    if (response.data.caja) {
      // Si ya tiene caja, redirigir a ventas
      window.location.href = '/categorias'; // Cambia esta ruta por VENTAS (PABLO)
    } else {
      // Si no tiene caja, redirigir a apertura
      window.location.href = '/caja/aperturar';
    }
  } catch (error) {
    console.error('Error verificando caja:', error);
    window.location.href = '/caja/aperturar'; // Fallback por si hay error
  }
};*/

// Redirigir a la vista de Cerrar Caja
/*const goToCerrarCaja = () => {
  window.location.href = '/caja/cerrar';
};*/

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false;
    }, 1000);
});
</script>
