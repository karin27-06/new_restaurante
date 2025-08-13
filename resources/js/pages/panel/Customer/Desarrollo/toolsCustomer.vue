<template>
    <div class="flex flex-wrap items-center gap-3 p-2">
        <!-- Exportar Excel -->
        <a href="/panel/reports/export-excel-customers" download>
            <Button variant="outlined" size="small" class="bg-green-600 hover:bg-green-700 text-white" icon="pi pi-file-excel" label="Exportar a Excel" title="Exportar a Excel">
            </Button>
        </a>

        <!-- Importar Excel 
        <div>
            <input type="file" ref="fileRef" accept=".xlsx" class="hidden" @change="handleFileChange"/>
            <Button @click="handleImportClick" variant="outlined" size="small" class="bg-blue-600 hover:bg-blue-700 text-white" icon="pi pi-upload" label="Importar Excel" title="Importar Excel">
            </Button>
        </div> -->

        <!-- Exportar PDF -->
        <a href="/panel/reports/export-pdf-customers" download>
            <Button variant="outlined" size="small" class="bg-red-600 hover:bg-red-700 text-white" icon="pi pi-file-pdf" label="Exportar a PDF" title="Exportar PDF">
            </Button>
        </a>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import  Button  from 'primevue/button'
//import { FileBarChart, FileDown, FileUp } from 'lucide-vue-next'
import axios from 'axios'
import { useToast } from 'primevue/usetoast'

const fileRef = ref<HTMLInputElement | null>(null)
const  toast  = useToast()

const emit = defineEmits<{
    (e: 'import-success'): void
}>()

const handleImportClick = () => {
    fileRef.value?.click()
}

const handleFileChange = async (event: Event) => {
    const target = event.target as HTMLInputElement
    const file = target.files?.[0]
    if (!file) return
    const formData = new FormData()
    formData.append('archivo', file)

    try {
        //REVISAR AQUI LA RUTA POST
        await axios.post('/api/clientes/import-excel', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })

        toast.add({ severity: 'success', summary: 'Importación exitosa', detail: 'Clientes importados correctamente.' })
        emit('import-success')
        target.value = ''
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Hubo un error al importar el archivo.' })
        console.error(error)
    }
}
</script>
