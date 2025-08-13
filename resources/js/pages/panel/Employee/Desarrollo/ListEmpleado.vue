<script setup>
import { ref, onMounted, watch } from 'vue';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Tag from 'primevue/tag';
import axios from 'axios';
import { debounce } from 'lodash';
import DeleteEmpleado from './DeleteEmpleado.vue';
import UpdateEmpleado from './UpdateEmpleado.vue';
import Select from 'primevue/select';

const dt = ref();
const empleados = ref([]);
const selectedEmpleados = ref();
const loading = ref(false);
const globalFilterValue = ref('');
const deleteEmpleadoDialog = ref(false);
const empleado = ref({});
const selectedEmpleadoId = ref(null);
const selectedEstadoEmpleado = ref(null);
const updateEmpleadoDialog = ref(false);
const currentPage = ref(1);


const props = defineProps({
    refresh: {
        type: Number,
        required: true
    }
});

watch(() => props.refresh, () => {
    loadEmpleado();
});

watch(() => selectedEstadoEmpleado.value, () => {
    currentPage.value = 1;
    loadEmpleado();
});

function editEmpleado(empleado) {
    selectedEmpleadoId.value = empleado.id;
    updateEmpleadoDialog.value = true;
}

const estadoEmpleadoOptions = ref([
    { name: 'TODOS', value: '' },
    { name: 'ACTIVOS', value: 1 },
    { name: 'INACTIVOS', value: 0 },
]);

function handleEmpleadoUpdated() {
    loadEmpleado();
}

function confirmDeleteEmpleado(selected) {
    empleado.value = selected;
    deleteEmpleadoDialog.value = true;
}

const pagination = ref({
    currentPage: 1,
    perPage: 15,
    total: 0
});

const filters = ref({
    state: null,
    online: null
});

function handleEmpleadoDeleted() {
    loadEmpleado();
}

const loadEmpleado = async () => {
    loading.value = true;
    try {
        const params = {
            page: pagination.value.currentPage,
            per_page: pagination.value.perPage,
            search: globalFilterValue.value,
            state: filters.value.state,
        };
        if (selectedEstadoEmpleado.value !== null && selectedEstadoEmpleado.value.value !== '') {
            params.state = selectedEstadoEmpleado.value.value;
        }

        const response = await axios.get('/empleado', { params });

        empleados.value = response.data.data;
        pagination.value.currentPage = response.data.meta.current_page;
        pagination.value.total = response.data.meta.total;
    } catch (error) {
        console.error('Error al cargar empleados:', error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron cargar los empleados', life: 3000 });
    } finally {
        loading.value = false;
    }
};

const onPage = (event) => {
    pagination.value.currentPage = event.page + 1;
    pagination.value.perPage = event.rows;
    loadEmpleado();
};

const getSeverity = (value) => {
    if (value === true || value === '1') return 'success';
    if (value === false || value === '0') return 'danger';
    return null;
};

const onGlobalSearch = debounce(() => {
    pagination.value.currentPage = 1;
    loadEmpleado();
}, 500);

onMounted(() => {
    loadEmpleado();
});
</script>

<template>
    <DataTable ref="dt" v-model:selection="selectedEmpleados" :value="empleados" dataKey="id" :paginator="true"
        :rows="pagination.perPage" :totalRecords="pagination.total" :loading="loading" :lazy="true" @page="onPage"
        :rowsPerPageOptions="[15, 20, 25]" scrollable scrollHeight="574px"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} empleados">

        <template #header>
            <div class="flex flex-wrap gap-2 items-center justify-between">
                <h4 class="m-0">EMPLEADOS</h4>
                <div class="flex flex-wrap gap-2">
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="globalFilterValue" @input="onGlobalSearch" placeholder="Buscar..." />
                    </IconField>
                    <Select v-model="selectedEstadoEmpleado" :options="estadoEmpleadoOptions" optionLabel="name"
                        placeholder="Estado" class="w-full md:w-auto" />
                    <Button icon="pi pi-refresh" outlined rounded aria-label="Refresh" @click="loadEmpleado" />
                </div>
            </div>
        </template>

        <Column selectionMode="multiple" style="width: 1rem" :exportable="false"></Column>
        <Column field="name" header="Nombre" sortable style="min-width: 12rem" />
        <Column field="codigo" header="Código" sortable style="min-width: 10rem" />
        <Column field="Empleado_Tipo" header="Tipo de Empleado" sortable style="min-width: 12rem" />
        <Column field="creacion" header="Creación" sortable style="min-width: 13rem" />
        <Column field="actualizacion" header="Actualización" sortable style="min-width: 13rem" />
        <Column field="state" header="Estado" sortable style="min-width: 6rem">
            <template #body="{ data }">
                <Tag :value="data.state ? 'Activo' : 'Inactivo'" :severity="getSeverity(data.state)" />
            </template>
        </Column>
        <Column field="accions" header="Acciones" :exportable="false" style="min-width: 8rem">
            <template #body="slotProps">
                <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editEmpleado(slotProps.data)" />
                <Button icon="pi pi-trash" outlined rounded severity="danger"
                    @click="confirmDeleteEmpleado(slotProps.data)" />
            </template>
        </Column>
    </DataTable>

    <DeleteEmpleado v-model:visible="deleteEmpleadoDialog" :empleado="empleado" @deleted="handleEmpleadoDeleted" />
    <UpdateEmpleado v-model:visible="updateEmpleadoDialog" :empleadoId="selectedEmpleadoId"
        @updated="handleEmpleadoUpdated" />
</template>
