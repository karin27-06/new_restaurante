<template>
    <div class="flex flex-wrap items-center gap-3 p-2">
        <!-- Exportar PDF -->
        <a href="/panel/reports/export-pdf-inputs" download>
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
        await axios.post('/api/insumos/import-excel', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })

        toast.add({ severity: 'success', summary: 'Importación exitosa', detail: 'Insumos importados correctamente.' })
        emit('import-success')
        target.value = ''
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Hubo un error al importar el archivo.' })
        console.error(error)
    }
}
</script>
