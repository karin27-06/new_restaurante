<script setup>
import { ref, watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import Tag from 'primevue/tag';
import Checkbox from 'primevue/checkbox';

const props = defineProps({
    visible: Boolean,
    AreaId: [Number, String]
});
const emit = defineEmits(['update:visible', 'updated']);

const serverErrors = ref({});
const submitted = ref(false);
const toast = useToast();
const loading = ref(false);

const dialogVisible = ref(props.visible);
watch(() => props.visible, (val) => dialogVisible.value = val);
watch(dialogVisible, (val) => emit('update:visible', val));

const fetchArea = async () => {
    loading.value = true;
    
    try {
        const response = await axios.get(`/area/${props.AreaId}`);
        
        if (response.data && response.data.areas) {
            const data = response.data.areas;
            area.value = {
                name: data.name || '',
                state: data.state === true
            };
        } else {
            console.error('Unexpected response format:', response.data);
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Formato de respuesta inesperado',
                life: 3000
            });
        }
    } catch (error) {
        console.error('Error fetching area:', error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'No se pudo cargar el área',
            life: 3000
        });
    } finally {
        loading.value = false;
    }
};

watch(() => [props.visible, props.AreaId], ([newVisible, newAreaId]) => {
    if (newVisible && newAreaId) {
        fetchArea();
    }
}, { immediate: true });

const area = ref({
    name: '',
    state: false
});

const updateArea = async () => {
    submitted.value = true;
    serverErrors.value = {};

    if (!area.value.name.trim()) {
        serverErrors.value.name = ['El nombre es requerido'];
        return;
    }

    try {
        const areaData = {
            name: area.value.name,
            state: area.value.state === true,
        };

        await axios.put(`/area/${props.AreaId}`, areaData);

        toast.add({
            severity: 'success',
            summary: 'Actualizado',
            detail: 'Área actualizada correctamente',
            life: 3000
        });

        dialogVisible.value = false;
        emit('updated');
    } catch (error) {
        
        if (error.response && error.response.data?.errors) {
            serverErrors.value = error.response.data.errors;
            toast.add({
                severity: 'error',
                summary: 'Error de validación',
                detail: 'Revisa los campos e intenta nuevamente.',
                life: 5000
            });
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'No se pudo actualizar el área',
                life: 3000
            });
        }
    }
};
</script>

<template>
    <Dialog v-model:visible="dialogVisible" header="Editar Área" modal :closable="true" :closeOnEscape="true"
        :style="{ width: '600px' }">
        <div v-if="loading" class="flex justify-center p-4">
            <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
        </div>
        <div v-else class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-9">
                    <label for="name" class="block font-bold mb-3">Nombre <span class="text-red-500">*</span></label>
                    <InputText
                        id="name"
                        v-model="area.name"
                        required
                        maxlength="100"
                        fluid
                        :class="{ 'p-invalid': serverErrors.name }"
                    />
                    <small v-if="serverErrors.name" class="p-error">{{ serverErrors.name[0] }}</small>
                </div>

                <div class="col-span-3">
                    <label for="state" class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="area.state" :binary="true" inputId="state" />
                        <Tag :value="area.state ? 'Activo' : 'Inactivo'" :severity="area.state ? 'success' : 'danger'" />
                    </div>
                </div>
            </div>
        </div>
        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="dialogVisible = false" />
            <Button label="Guardar" icon="pi pi-check" @click="updateArea" :loading="loading" />
        </template>
    </Dialog>
</template>

<style scoped>
</style>