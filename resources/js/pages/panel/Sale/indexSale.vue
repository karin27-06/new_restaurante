<template>
    <Head title="Ventas" />
    <AppLayout>
        <div>
            <template v-if="isLoading">
                <Espera/>
            </template>

            <template v-else>
 <ListSales :refresh="refreshKey"/>

            </template>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layout/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import Espera from '@/components/Espera.vue';
import ListSales from './Desarrollo/ListSales.vue';

const isLoading = ref(true);
const refreshKey = ref(0);

function refrescarListado() {
    refreshKey.value++;
}
onMounted(() => {
    setTimeout(() => {
        isLoading.value = false;
    }, 1500);
});
</script>
