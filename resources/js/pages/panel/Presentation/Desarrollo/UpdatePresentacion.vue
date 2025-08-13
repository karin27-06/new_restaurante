<script setup>
import { ref, watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Tag from 'primevue/tag';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    visible: Boolean,
    presentacionId: Number
});
const emit = defineEmits(['update:visible', 'updated']);

const toast = useToast();
const dialogVisible = ref(props.visible);
const loading = ref(false);
const submitted = ref(false);
const serverErrors = ref({});

const presentacion = ref({
    name: '',
    description: '',
    state: false,
});

watch(() => props.visible, (val) => {
    dialogVisible.value = val;
    if (val && props.presentacionId) {
        fetchPresentacion();
    }
});
watch(dialogVisible, (val) => emit('update:visible', val));

const fetchPresentacion = async () => {
    try {
        loading.value = true;
        const res = await axios.get(`/presentacion/${props.presentacionId}`);
        const data = res.data.presentation;
        presentacion.value = {
            name: data.name,
            description: data.description || '',
            state: data.state
        };
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar la presentación', life: 3000 });
        console.error(error);
    } finally {
        loading.value = false;
    }
};

const updatePresentacion = async () => {
    submitted.value = true;
    serverErrors.value = {};

    try {
        const presentacionData = {
            name: presentacion.value.name,
            description: presentacion.value.description,
            state: presentacion.value.state
        };

        await axios.put(`/presentacion/${props.presentacionId}`, presentacionData);

        toast.add({
            severity: 'success',
            summary: 'Actualizado',
            detail: 'Presentación actualizada correctamente',
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
                detail: 'No se pudo actualizar la presentación',
                life: 3000
            });
        }
        console.error(error);
    }
};
</script>

<template>
    <Dialog v-model:visible="dialogVisible" header="Editar Presentación" modal :closable="true" :style="{ width: '600px' }">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-10">
                    <label class="block font-bold mb-2">Nombre <span class="text-red-500">*</span></label>
                    <InputText
                        v-model="presentacion.name"
                        required
                        maxlength="150"
                        fluid
                        :class="{ 'p-invalid': serverErrors.name }"
                    />
                    <small v-if="serverErrors.name" class="text-red-500">{{ serverErrors.name[0] }}</small>
                </div>
                 <div class="col-span-2">
                    <label class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="presentacion.state" :binary="true" />
                        <Tag :value="presentacion.state ? 'Activo' : 'Inactivo'" :severity="presentacion.state ? 'success' : 'danger'" />
                    </div>
                </div>
                <div class="col-span-12">
                    <label class="block font-bold mb-3">Descripción</label>
                    <Textarea
                        v-model="presentacion.description"
                        maxlength="255"
                        rows="4" autoResize fluid
                        :class="{ 'p-invalid': serverErrors.description }" 
                    />
                    <small v-if="serverErrors.description" class="text-red-500">{{ serverErrors.description[0] }}</small>
                </div>
            </div>
        </div>
        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="dialogVisible = false" />
            <Button label="Guardar" icon="pi pi-check" @click="updatePresentacion" :loading="loading" />
        </template>
    </Dialog>
</template>
