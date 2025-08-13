<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    visible: Boolean,
    caja: Object,
});
const emit = defineEmits(['update:visible', 'deleted']);

const toast = useToast();
const localVisible = ref(false);

watch(() => props.visible, (newVal) => {
    localVisible.value = newVal;
});

function closeDialog() {
    emit('update:visible', false);
}

async function deleteCaja() {
    try {
        const response = await axios.delete(`/caja/${props.caja.id}`);

        // Si la caja se elimina correctamente, cierra el modal y muestra el mensaje de éxito
        if (response.data.state) {
            emit('deleted');
            closeDialog();
            toast.add({
                severity: 'success',
                summary: 'Éxito',
                detail: 'Caja eliminada correctamente',
                life: 3000
            });
        } else {
            // Si la respuesta del backend es un error (estado false), muestra el mensaje de error
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: response.data.message,
                life: 3000
            });
            // No cerrar el modal aquí, solo mostrar el mensaje de error
        }
    } catch (error) {
        console.error(error);
        let errorMessage = 'Error eliminando la caja';
        if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
        }
        toast.add({ severity: 'error', summary: 'Error', detail: errorMessage, life: 3000 });
    }
}

</script>

<template>
    <Dialog v-model:visible="localVisible" :style="{ width: '380px' }" header="Confirmar" :modal="true"
        @update:visible="closeDialog">
        <div class="flex items-center gap-5">
            <i class="pi pi-exclamation-triangle !text-7xl" />
                <span v-if="caja">¿Estás seguro de eliminar la caja N° <b>{{ caja.numero_cajas }}</b>?</span>
        </div>
        <template #footer>
            <Button label="No" icon="pi pi-times" text @click="closeDialog" />
            <Button label="Sí" icon="pi pi-check" @click="deleteCaja" />
        </template>
    </Dialog>
</template>
