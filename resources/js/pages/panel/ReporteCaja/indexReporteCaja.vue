<template>
    <Head title="Reportes de Caja" />
    <AppLayout>
        <div>
            <template v-if="isLoading">
                <Espera/>
            </template>

            <template v-else>
            <div class="card">
                <ToolsReporteCaja />
                <ListReporteCaja :refresh="refreshKey"/>
            </div>
            </template>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layout/AppLayout.vue';
import Espera from '@/components/Espera.vue';
import ListReporteCaja from './Desarrollo/ListReporteCaja.vue';
import ToolsReporteCaja from './Desarrollo/toolsReporteCaja.vue';

const isLoading = ref(true);
const refreshKey = ref(0);

onMounted(() => {
    setTimeout(() => {
        isLoading.value = false;
    }, 1000);
});

</script>
