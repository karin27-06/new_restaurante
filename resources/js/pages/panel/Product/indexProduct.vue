<template>
    <Head title="Productos" />
    <AppLayout>
        <div>
            <template v-if="isLoading">
                <Espera/>
            </template>

            <template v-else>
                <div class="card">
                    <AddProductos @producto-agregado="refrescarListado"/>
                    <ListProductos :refresh="refreshKey"/>
                </div>
            </template>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/layout/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import Espera from '@/components/Espera.vue';
import ListProductos from './Desarrollo/ListProductos.vue';
import AddProductos from './Desarrollo/AddProductos.vue';

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
