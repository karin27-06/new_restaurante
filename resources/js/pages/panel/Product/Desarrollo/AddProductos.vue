<template>
    <Toolbar class="mb-6">
        <template #start>
            <Button label="Nuevo producto" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
        </template>
        <template #end>
            <!-- ToolsProduct para los botones de exportar e importar -->
            <ToolsProduct @import-success="loadProducto"/>       
        </template>
    </Toolbar>

    <Dialog v-model:visible="productoDialog" :style="{ width: '700px' }" header="Registro de productos" :modal="true">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <!-- Nombre -->
                <div class="col-span-10">
                    <label class="block font-bold mb-2">Nombre <span class="text-red-500">*</span></label>
                    <InputText v-model.trim="producto.name" fluid maxlength="100" />
                    <small v-if="submitted && !producto.name" class="text-red-500">El nombre es obligatorio.</small>
                    <small v-else-if="submitted && producto.name.length < 2" class="text-red-500">El nombre debe tener al menos 2 caracteres.</small>
                    <small v-else-if="serverErrors.name" class="text-red-500">{{ serverErrors.name[0] }}</small>
                </div>

                <!-- Estado -->
                <div class="col-span-2">
                    <label class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="producto.state" :binary="true" />
                        <Tag :value="producto.state ? 'Activo' : 'Inactivo'" fluid :severity="producto.state ? 'success' : 'danger'" />
                        <small v-if="submitted && producto.state === null" class="text-red-500">El estado es obligatorio.</small>
                        <small v-else-if="serverErrors.state" class="text-red-500">{{ serverErrors.state[0] }}</small>
                    </div>
                </div>

                <!-- Detalles -->
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Detalle <span class="text-red-500">*</span></label>
                    <Textarea v-model="producto.details" autoResize rows="3" fluid maxlength="200" />
                    <small v-if="submitted && !producto.details" class="text-red-500">El detalle es obligatorio.</small>
                    <small v-else-if="serverErrors.details" class="text-red-500">{{ serverErrors.details[0] }}</small>
                </div>

                <!-- Categoría -->
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
                    />
                    <small v-if="submitted && !producto.idCategory" class="text-red-500">La categoría es obligatoria.</small>
                    <small v-else-if="serverErrors.idCategory" class="text-red-500">{{ serverErrors.idCategory[0] }}</small>
                </div>

                <!-- Almacén con Dropdown con búsqueda -->
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
                    />
                    <small v-if="submitted && !producto.idAlmacen" class="text-red-500">El almacén es obligatorio.</small>
                    <small v-else-if="serverErrors.idAlmacen" class="text-red-500">{{ serverErrors.idAlmacen[0] }}</small>
                </div>

            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="hideDialog" />
            <Button label="Guardar" icon="pi pi-check" @click="guardarProducto" />
        </template>
    </Dialog>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import Dialog from 'primevue/dialog';
import Toolbar from 'primevue/toolbar';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Checkbox from 'primevue/checkbox';
import Tag from 'primevue/tag';
import Textarea from 'primevue/textarea';
import { useToast } from 'primevue/usetoast';
import Dropdown from 'primevue/dropdown';
import ToolsProduct from './toolsProduct.vue';

const toast = useToast();
const submitted = ref(false);
const productoDialog = ref(false);
const serverErrors = ref({});
const emit = defineEmits(['producto-agregado']);

const producto = ref({
    name: '',
    details: '',
    state: true,
    idCategory: null,
    idAlmacen: null
});
// Método para recargar la lista de productos
const loadProducto = async () => {
    try {
        const response = await axios.get('/producto');  // Aquí haces una solicitud GET para obtener los productos
        console.log(response.data);
        // Realiza lo que necesites con la respuesta, como actualizar el listado en un componente superior
        emit('producto-agregada');  // Si quieres que un componente padre reciba la notificación de la actualización
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar los productos', life: 3000 });
        console.error(error);
    }
}
const categorias = ref([]);
const almacenes = ref([]);

function resetProducto() {
    producto.value = {
        name: '',
        details: '',
        state: true,
        idCategory: null,
        idAlmacen: null
    };
    serverErrors.value = {};
    submitted.value = false;
}

function openNew() {
    resetProducto();
    fetchCategorias();
    fetchAlmacenes();
    productoDialog.value = true;
}

function hideDialog() {
    productoDialog.value = false;
    resetProducto();
}

async function fetchCategorias() {
    try {
        const { data } = await axios.get('/categoria', { params: { state: 1 } });
        categorias.value = data.data.map(c => ({ label: c.name, value: c.id }));
    } catch (e) {
        toast.add({ severity: 'warn', summary: 'Advertencia', detail: 'No se pudieron cargar categorías' });
    }
}

async function fetchAlmacenes() {
    try {
        const { data } = await axios.get('/almacen', { params: { state: 1 } });
        almacenes.value = data.data.map(a => ({ label: a.name, value: a.id }));
    } catch (e) {
        toast.add({ severity: 'warn', summary: 'Advertencia', detail: 'No se pudieron cargar almacenes' });
    }
}

function guardarProducto() {
    submitted.value = true;
    serverErrors.value = {};

    axios.post('/producto', producto.value)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Producto registrado', life: 3000 });
            hideDialog();
            emit('producto-agregado');
        })
        .catch(error => {
            if (error.response?.status === 422) {
                serverErrors.value = error.response.data.errors || {};
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: 'No se pudo registrar el producto',
                    life: 3000
                });
            }
        });
}
</script>
