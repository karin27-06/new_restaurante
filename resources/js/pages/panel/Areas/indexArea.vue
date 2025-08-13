<template>
    <Head title="Area" />
    <AppLayout>
        <div>
            <template v-if="isLoading">
                <Espera/>
            </template>

            <template v-else>
                <div class="card">
                    <AddArea @area-agregada="refrescarListado"/>
                    <ListArea :refresh="refreshKey"/>
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
import AddArea from './Desarrollo/AddArea.vue';
import ListArea from './Desarrollo/ListArea.vue';

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
