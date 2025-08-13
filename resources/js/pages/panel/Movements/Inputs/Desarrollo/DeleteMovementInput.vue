<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    visible: Boolean,
    movementInput: Object,
});
const emit = defineEmits(['update:visible', 'deleted']);

const toast = useToast();
const localVisible = ref(props.visible);

// Sincroniza localVisible con el prop visible
watch(() => props.visible, (val) => {
    localVisible.value = val;
});
// Cuando localVisible cambie, emite el cambio para actualizar el v-model del padre
watch(localVisible, (val) => {
    emit('update:visible', val);
});
function closeDialog() {
    emit('update:visible', false);
}

async function deleteInput() {
    try {
        await axios.delete(`/insumos/movimiento/${props.movementInput.id}`);
        emit('deleted');
        closeDialog();
        toast.add({
            severity: 'success',
            summary: 'Éxito',
            detail: 'Movimiento de Insumo eliminado correctamente',
            life: 3000
        });

    } catch (error) {
        console.error(error);
        let errorMessage = 'Error al eliminar el movimiento de insumo';
        if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
        }
        toast.add({ severity: 'error', summary: 'Error', detail: errorMessage, life: 3000 });
    }
}

async function deleteKardex() {
    try {
        // Enviar el id del movimiento a la API para eliminar el registro correspondiente en el kardex
        await axios.delete(`/insumos/karde/props.movementInput.id`);
        
        emit('deleted');
        closeDialog();
        toast.add({
            severity: 'success',
            summary: 'Éxito',
            detail: 'Movimiento de Kardex eliminado correctamente',
            life: 3000
        });
    } catch (error) {
        console.error(error);
        let errorMessage = 'Error al eliminar el movimiento de Kardex';
        if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
        }
        toast.add({ severity: 'error', summary: 'Error', detail: errorMessage, life: 3000 });
    }
}

</script>

<template>
    <Dialog v-model:visible="localVisible" :style="{ width: '450px', 'z-index': 9999 }" header="Confirmar" :modal="true">
        <div class="flex items-center gap-4">
            <i class="pi pi-exclamation-triangle !text-3xl" />
            <span v-if="movementInput">¿Estás seguro de eliminar el movimiento de compra de insumo con codigo: <b>{{ movementInput.code }}</b>?</span>
        </div>
        <template #footer>
            <Button label="No" icon="pi pi-times" text @click="closeDialog" />
            <Button label="Sí" icon="pi pi-check" @click="deleteInput" />
        </template>
    </Dialog>
</template>

