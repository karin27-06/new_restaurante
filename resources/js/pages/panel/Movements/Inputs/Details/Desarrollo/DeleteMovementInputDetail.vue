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
    console.log('ID a eliminar:', props.movementInput.id);  // Verifica que el ID es correcto

    try {
        const response = await axios.delete(`/insumos/movimientos/detalle/${props.movementInput.id}`);
deleteInputKardex();
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

async function deleteInputKardex() {
    const { idMovementInput } = props.movementInput;  
    const idInput = props.movementInput.input.id;   

    try {
        const response = await axios.get(`/insumos/karde?idMovementInput=${idMovementInput}&idInput=${idInput}`);
        
        const id = response.data.data[0].id;

        const deleteResponse = await axios.delete(`/insumos/karde/${id}`);
        console.log('Respuesta del servidor:', deleteResponse);  // Verifica la respuesta

        emit('deleted');
        closeDialog();

    } catch (error) {
    }
}


</script>


<template>
    <Dialog v-model:visible="localVisible" :style="{ width: '450px', 'z-index': 9999 }" header="Confirmar" :modal="true">
        <div class="flex items-center gap-4">
            <i class="pi pi-exclamation-triangle !text-3xl" />
            <span v-if="movementInput">¿Estás seguro de eliminar el insumo <b>{{ movementInput.input.name }}</b> del movimiento?</span>
        </div>
        <template #footer>
            <Button label="No" icon="pi pi-times" text @click="closeDialog" />
            <Button label="Sí" icon="pi pi-check" @click="deleteInput" />
        </template>
    </Dialog>
</template>

