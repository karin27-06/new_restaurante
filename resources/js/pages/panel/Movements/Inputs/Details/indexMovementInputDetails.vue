<template>
    <Head title="Movimiento de Compra de Insumos" />
    <AppLayout>
        <div>
            <template v-if="isLoading">
                <Espera/>
            </template>

            <template v-else>
                <div class="card">
              <AddMovementInputsDetail @movementsinput-agregado="refrescarListado"/>

                    <Summary/>
                    <ListMovementInputsDetails :refresh="refreshKey"/>
                    <ListInputs :refresh="refreshKey"/>
                </div>
            </template>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layout/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import AddMovementInputsDetail from './Desarrollo/AddMovementInputsDetail.vue';

import Summary from './Desarrollo/SummaryMovementInputDetails.vue';
import ListMovementInputsDetails from './Desarrollo/ListMovementInputsDetails.vue';
import Espera from '@/components/Espera.vue';

const isLoading = ref(true);
const refreshKey = ref(0);

function refrescarListado() {
    refreshKey.value++;
}
onMounted(() => {
    setTimeout(() => {
        isLoading.value = false;
    }, 1000);

    
});
</script>
