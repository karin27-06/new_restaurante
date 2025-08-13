<script setup>
import { ref, watch } from 'vue';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Tag from 'primevue/tag';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import Dropdown from 'primevue/dropdown';

const props = defineProps({
    visible: Boolean,
    clienteId: Number
});
const emit = defineEmits(['update:visible', 'updated']);

const toast = useToast();
const dialogVisible = ref(props.visible);
const loading = ref(false);
const submitted = ref(false);
const serverErrors = ref({});

const cliente = ref({
    name: '',
    codigo: '',
    client_type_id: null,
    state: false,
});

const tiposCliente = ref([]);
const codigoMaxLength = ref(8); // 8 dígitos por defecto para persona natural (DNI)
const codigoPattern = ref(""); // Expresión regular para el código
const codigoPlaceholder = ref("Ingrese su codigo"); 

watch(() => props.visible, (val) => {
    dialogVisible.value = val;
    if (val && props.clienteId) {
        fetchCliente();
        fetchTiposCliente();
    }
});

watch(dialogVisible, (val) => emit('update:visible', val));

const fetchCliente = async () => {
    try {
        loading.value = true;
        const res = await axios.get(`/cliente/${props.clienteId}`);
        const data = res.data.customer;
        cliente.value = {
            name: data.name,
            codigo: data.codigo,
            client_type_id: data.client_type_id,
            state: data.state
        };
        // Actualizar la longitud y el patrón del código al cargar el cliente
        onTipoClienteChange(); 
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar el cliente', life: 3000 });
        console.error(error);
    } finally {
        loading.value = false;
    }
};

const fetchTiposCliente = async () => {
    try {
        const res = await axios.get('/tipo_cliente', { params: { state: 1 } });
        tiposCliente.value = res.data.data;
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cargar los tipos de cliente', life: 3000 });
        console.error(error);
    }
};

const updateCliente = async () => {
    submitted.value = true;
    serverErrors.value = {};

    // Validación de longitud de código dependiendo del tipo de cliente
    if (cliente.value.client_type_id === 1 && cliente.value.codigo.length !== 8) { // Persona natural (DNI)
        serverErrors.value.codigo = ['El código debe tener 8 dígitos para persona natural.'];
        codigoPlaceholder.value.codigo = "Ingrese su número de DNI"; 
        toast.add({ severity: 'error', summary: 'Error', detail: 'El código debe tener 8 dígitos para persona natural.', life: 3000 });
        return;
    }

    if (cliente.value.client_type_id === 2 && cliente.value.codigo.length !== 11) { // Persona jurídica (RUC)
        serverErrors.value.codigo = ['El código debe tener 11 dígitos para persona jurídica.'];
        codigoPlaceholder.value.codigo = "Ingrese su número de RUC (10 o 20 dígitos)";
        toast.add({ severity: 'error', summary: 'Error', detail: 'El código debe tener 11 dígitos para persona jurídica.', life: 3000 });
        return;
    }

    try {
        const clienteData = {
            name: cliente.value.name,
            codigo: cliente.value.codigo,
            client_type_id: cliente.value.client_type_id,
            state: cliente.value.state
        };

        await axios.put(`/cliente/${props.clienteId}`, clienteData);

        toast.add({
            severity: 'success',
            summary: 'Actualizado',
            detail: 'Cliente actualizado correctamente',
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
                detail: 'No se pudo actualizar el cliente',
                life: 3000
            });
        }
        console.error(error);
    }
};

const onTipoClienteChange = () => {
    const tipoCliente = tiposCliente.value.find(t => t.id === cliente.value.client_type_id);
    if (tipoCliente) {
        // Ajustar el patrón y longitud de código dependiendo del tipo de cliente
        if (tipoCliente.codigo_pattern) {
            codigoPattern.value = tipoCliente.codigo_pattern;
        }

        if (cliente.value.client_type_id === 1) {
            codigoMaxLength.value = 8; // Persona natural (DNI)
        } else if (cliente.value.client_type_id === 2) {
            codigoMaxLength.value = 11; // Persona jurídica (RUC)
        } else {
            // Para otros tipos de clientes, puedes ajustar la longitud y el patrón
            codigoMaxLength.value = 10;
        }
    }
};
</script>

<template>
    <Dialog v-model:visible="dialogVisible" header="Editar Cliente" modal :closable="true" :style="{ width: '600px' }">
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-10">
                    <label class="block font-bold mb-2">Código <span class="text-red-500">*</span></label>
                    <InputText
                        v-model="cliente.codigo"
                        required
                        fluid
                        :placeholder="codigoPlaceholder"
                        :maxlength="codigoMaxLength"
                        :pattern="codigoPattern"
                        :class="{ 'p-invalid': serverErrors.codigo }"
                    />
                    <small v-if="serverErrors.codigo" class="text-red-500">{{ serverErrors.codigo[0] }}</small>
                </div>
                <div class="col-span-2">
                    <label class="block font-bold mb-2">Estado <span class="text-red-500">*</span></label>
                    <div class="flex items-center gap-3">
                        <Checkbox v-model="cliente.state" :binary="true" />
                        <Tag :value="cliente.state ? 'Activo' : 'Inactivo'" :severity="cliente.state ? 'success' : 'danger'" />
                    </div>
                </div>
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Nombre <span class="text-red-500">*</span></label>
                    <InputText
                        v-model="cliente.name"
                        required
                        placeholder="Ingrese el nombre correspondiente"
                        maxlength="150"
                        fluid
                        :class="{ 'p-invalid': serverErrors.name }"
                    />
                    <small v-if="serverErrors.name" class="text-red-500">{{ serverErrors.name[0] }}</small>
                </div>
                <div class="col-span-12">
                    <label class="block font-bold mb-2">Tipo de Cliente <span class="text-red-500">*</span></label>
                    <Dropdown
                        v-model="cliente.client_type_id"
                        :options="tiposCliente"
                        optionLabel="name"
                        optionValue="id"
                        placeholder="Seleccione tipo de cliente"
                        filter
                        filterBy="name"
                        filterPlaceholder="Buscar tipo de cliente..." 
                        class="w-full"
                        fluid
                        :class="{ 'p-invalid': serverErrors.client_type_id }"
                        @change="onTipoClienteChange"
                    />
                    <small v-if="serverErrors.client_type_id" class="text-red-500">{{ serverErrors.client_type_id[0] }}</small>
                </div>
            </div>
        </div>
        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="dialogVisible = false" />
            <Button label="Guardar" icon="pi pi-check" @click="updateCliente" :loading="loading" />
        </template>
    </Dialog>
</template>
