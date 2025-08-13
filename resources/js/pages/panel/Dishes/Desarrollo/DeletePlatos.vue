<script setup>
import { ref, watch } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    visible: Boolean,
    plato: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['update:visible', 'deleted']);

const dialogVisible = ref(props.visible);
const loading = ref(false);
const toast = useToast();

// Sync dialog visibility with prop
watch(() => props.visible, (val) => dialogVisible.value = val);
watch(dialogVisible, (val) => emit('update:visible', val));

const deletePlato = async () => {
    loading.value = true;
    
    try {
        await axios.delete(`/plato/${props.plato.id}`);
        
        toast.add({
            severity: 'success',
            summary: 'Eliminado',
            detail: 'Plato eliminado correctamente',
            life: 3000
        });
        
        dialogVisible.value = false;
        emit('deleted');
    } catch (error) {
        console.error('Error al eliminar plato:', error);
        
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'No se pudo eliminar el plato',
            life: 3000
        });
    } finally {
        loading.value = false;
    }
};
</script>

<template>
 

  <Dialog v-model:visible="dialogVisible" :style="{ width: '450px' }" header="Confirmar Eliminacion" :modal="true"
        @update:visible="closeDialog">
        <div class="flex items-center gap-4">
            <i class="pi pi-exclamation-triangle !text-3xl" />
            <span v-if="plato">¿Estás seguro de eliminar este plato <b>{{ plato.name }}</b>?</span>
        </div>
        <template #footer>
            <Button label="No" icon="pi pi-times" text @click="dialogVisible = false" />
            <Button label="Sí" icon="pi pi-check" @click="deletePlato" />
        </template>
    </Dialog>
    
</template>
