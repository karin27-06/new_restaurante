<script setup>
import { ref, watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Tag from 'primevue/tag';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    visible: Boolean,
    proveedorId: Number
});
const emit = defineEmits(['update:visible', 'updated']);

const toast = useToast();
const dialogVisible = ref(props.visible);
const loading = ref(false);
const submitted = ref(false);
const serverErrors = ref({});

const proveedor = ref({
    name: '',
    ruc: '',
    address: '',
    phone: '',
    state: false,
});

watch(() => props.visible, (val) => {
    dialogVisible.value = val;
    if (val && props.proveedorId) {
        fetchProveedor();
    }
});
watch(dialogVisible, (val) => emit('update:visible', val));

const fetchProveedor = async () => {
    try {
        loading.value = true;
        const res = await axios.get(`/proveedor/${props.proveedorId}`);
        const data = res.data.supplier;
        proveedor.value = {
            name: data.name,
            ruc: data.ruc,
            address: data.address || '',
            phone: data.phone || '',
            state: data.state
        };
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar el proveedor', life: 3000 });
        console.error(error);
    } finally {
        loading.value = false;
    }
};

const updateProveedor = async () => {
    submitted.value = true;
    serverErrors.value = {};

    try {
        const proveedorData = {
            name: proveedor.value.name,
            ruc: proveedor.value.ruc,
            address: proveedor.value.address,
            phone: proveedor.value.phone,
            state: proveedor.value.state
        };

        await axios.put(`/proveedor/${props.proveedorId}`, proveedorData);

        toast.add({
            severity: 'success',
            summary: 'Actualizado',
            detail: 'Proveedor actualizado correctamente',
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
                detail: 'No se pudo actualizar el proveedor',
                life: 3000
            });
        }
        console.error(error);
    }
};
</script>

<template>
    <Dialog v-model:visible="dialogVisible" header="Editar Proveedor" modal :closable="true" :style="{ width: '600px' }">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-10">
                    <label class="block font-bold mb-2">RUC <span class="text-red-500">*</span></label>
                    <InputText
                        v-model="proveedor.ruc"
                        required
                        fluid
                        maxlength="11"
                        :class="{ 'p-invalid': serverErrors.ruc }"
                    />
                    <small v-if="serverErrors.ruc" class="text-red-500">{{ serverErrors.ruc[0] }}</small>
                </div>
                <div class="col-span-2">
                    <label class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="proveedor.state" :binary="true" />
                        <Tag :value="proveedor.state ? 'Activo' : 'Inactivo'" :severity="proveedor.state ? 'success' : 'danger'" />
                    </div>
                </div>
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Nombre <span class="text-red-500">*</span></label>
                    <InputText
                        v-model="proveedor.name"
                        required
                        maxlength="150"
                        fluid
                        :class="{ 'p-invalid': serverErrors.name }"
                    />
                    <small v-if="serverErrors.name" class="text-red-500">{{ serverErrors.name[0] }}</small>
                </div>
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Dirección <span class="text-red-500">*</span></label>
                    <InputText
                        v-model="proveedor.address"
                        maxlength="255"
                        fluid
                        :class="{ 'p-invalid': serverErrors.address }"
                    />
                    <small v-if="serverErrors.address" class="text-red-500">{{ serverErrors.address[0] }}</small>
                </div>
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Teléfono <span class="text-red-500">*</span></label>
                    <InputText
                        v-model="proveedor.phone"
                        maxlength="11"
                        fluid
                        :class="{ 'p-invalid': serverErrors.phone }"
                    />
                    <small v-if="serverErrors.phone" class="text-red-500">{{ serverErrors.phone[0] }}</small>
                </div>
            </div>
        </div>
        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="dialogVisible = false" />
            <Button label="Guardar" icon="pi pi-check" @click="updateProveedor" :loading="loading" />
        </template>
    </Dialog>
</template>
