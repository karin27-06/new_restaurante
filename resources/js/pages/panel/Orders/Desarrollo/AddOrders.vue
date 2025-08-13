<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nueva Orden" icon="pi pi-plus" severity="secondary" class="mr-2" @click="verMesas" />
        </template>
        <template #end> </template>
    </Toolbar>
</template>

<script setup>
import { router } from '@inertiajs/core';
import Button from 'primevue/button';
import Toolbar from 'primevue/toolbar';
import { useToast } from 'primevue/usetoast';
import { defineEmits, ref } from 'vue';
const toast = useToast();
const emit = defineEmits(['plato-agregado']);
const submitted = ref(false);
const DialogOrder = ref(false);
const serverErrors = ref({});

const verMesas = () => {
    const url = `/ordenes/mesas/`;
    router.visit(url);
};
</script>
