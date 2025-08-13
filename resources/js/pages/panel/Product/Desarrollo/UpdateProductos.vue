<script setup>
import { ref, watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Dropdown from 'primevue/dropdown';
import Textarea from 'primevue/textarea';
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    productoId: Number
});
const emit = defineEmits(['update:visible', 'updated']);

const toast = useToast();
const serverErrors = ref({});
const submitted = ref(false);
const loading = ref(false);

const dialogVisible = ref(props.visible);
watch(() => props.visible, (val) => dialogVisible.value = val);
watch(dialogVisible, (val) => emit('update:visible', val));

const producto = ref({
    name: '',
    details: '',
    state: false,
    idCategory: null,
    idAlmacen: null
});

const categorias = ref([]);
const almacenes = ref([]);

watch(() => props.visible, async (val) => {
    if (val && props.productoId) {
        await fetchProducto();
        await fetchCategorias();
        await fetchAlmacenes();
    }
});

const fetchProducto = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(`/producto/${props.productoId}`);
        const p = data.product;
        producto.value = {
            name: p.name,
            details: p.details,
            state: p.state,
            idCategory: p.idCategory,
            idAlmacen: p.idAlmacen
        };
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'No se pudo cargar el producto',
            life: 3000
        });
    } finally {
        loading.value = false;
    }
};

const fetchCategorias = async () => {
    try {
        const { data } = await axios.get('/categoria', { params: { state: 1 } });
        categorias.value = data.data.map(c => ({ label: c.name, value: c.id }));
    } catch (e) {
        toast.add({ severity: 'warn', summary: 'Advertencia', detail: 'No se pudieron cargar categorías' });
    }
};

const fetchAlmacenes = async () => {
    try {
        const { data } = await axios.get('/almacen', { params: { state: 1 } });
        almacenes.value = data.data.map(a => ({ label: a.name, value: a.id }));
    } catch (e) {
        toast.add({ severity: 'warn', summary: 'Advertencia', detail: 'No se pudieron cargar almacenes' });
    }
};

const updateProducto = async () => {
    submitted.value = true;
    serverErrors.value = {};

    try {
        const dataToSend = {
            name: producto.value.name,
            details: producto.value.details,
            state: producto.value.state === true,
            idCategory: producto.value.idCategory,
            idAlmacen: producto.value.idAlmacen
        };

        await axios.put(`/producto/${props.productoId}`, dataToSend);

        toast.add({
            severity: 'success',
            summary: 'Actualizado',
            detail: 'Producto actualizado correctamente',
            life: 3000
        });

        dialogVisible.value = false;
        emit('updated');
    } catch (error) {
        if (error.response?.data?.errors) {
            serverErrors.value = error.response.data.errors;
            toast.add({
                severity: 'error',
                summary: 'Error de validación',
                detail: error.response.data.message || 'Revisa los campos.',
                life: 5000
            });
        } else {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'No se pudo actualizar el producto',
                life: 3000
            });
        }
    }
};
</script>

<template>
    <Dialog v-model:visible="dialogVisible" header="Editar Producto" modal :closable="true" :closeOnEscape="true"
        :style="{ width: '700px' }">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <!-- Nombre -->
                <div class="col-span-10">
                    <label class="block font-bold mb-2">Nombre <span class="text-red-500">*</span></label>
                    <InputText v-model="producto.name" required maxlength="100" fluid
                        :class="{ 'p-invalid': serverErrors.name }" />
                    <small v-if="serverErrors.name" class="p-error">{{ serverErrors.name[0] }}</small>
                </div>

                <!-- Estado -->
                <div class="col-span-2">
                    <label class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="producto.state" :binary="true" fluid />
                        <Tag :value="producto.state ? 'Activo' : 'Inactivo'"
                            :severity="producto.state ? 'success' : 'danger'" />
                    </div>
                </div>

                <!-- Detalle -->
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Detalle <span class="text-red-500">*</span></label>
                    <Textarea v-model="producto.details" autoResize maxlength="200" rows="3" fluid
                        :class="{ 'p-invalid': serverErrors.details }" />
                    <small v-if="serverErrors.details" class="p-error">{{ serverErrors.details[0] }}</small>
                </div>

                <!-- Categoría (Dropdown con búsqueda) -->
                <div class="col-span-6">
                    <label class="block font-bold mb-2">Categoría <span class="text-red-500">*</span></label>
                    <Dropdown
                        v-model="producto.idCategory"
                        :options="categorias"
                        optionLabel="label"
                        optionValue="value"
                        fluid
                        placeholder="Seleccione categoría"
                        filter
                        filterBy="label"
                        filterPlaceholder="Buscar categoria..."
                        style="width: 325px;"
                        :class="{ 'p-invalid': serverErrors.idCategory }"
                    />
                    <small v-if="serverErrors.idCategory" class="p-error">{{ serverErrors.idCategory[0] }}</small>
                </div>

                <!-- Almacén (Dropdown con búsqueda) -->
                <div class="col-span-6">
                    <label class="block font-bold mb-2">Almacén <span class="text-red-500">*</span></label>
                    <Dropdown
                        v-model="producto.idAlmacen"
                        :options="almacenes"
                        optionLabel="label"
                        optionValue="value"
                        fluid
                        placeholder="Seleccione almacén"
                        filter
                        filterBy="label"
                        filterPlaceholder="Buscar almacen..."
                        style="width: 325px;"
                        :class="{ 'p-invalid': serverErrors.idAlmacen }"
                    />
                    <small v-if="serverErrors.idAlmacen" class="p-error">{{ serverErrors.idAlmacen[0] }}</small>
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="dialogVisible = false" />
            <Button label="Guardar" icon="pi pi-check" @click="updateProducto" :loading="loading" />
        </template>
    </Dialog>
</template>
