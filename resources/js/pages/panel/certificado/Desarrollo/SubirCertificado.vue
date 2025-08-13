<template>
  <div class="card p-4 shadow-sm">
    <h2 class="text-lg font-semibold mb-4">Subir certificado .pem</h2>

    <form @submit.prevent="subirArchivo" enctype="multipart/form-data">
      <div class="mb-3">
        <input
          type="file"
          accept=".pem"
          @change="handleArchivo"
          class="form-control"
          required
        />
        <div v-if="errores.certificado" class="text-danger mt-1">
          {{ errores.certificado }}
        </div>
      </div>
      <button type="submit" class="btn btn-primary" :disabled="cargando">
        {{ cargando ? 'Subiendo...' : 'Subir Certificado' }}
      </button>

      <div v-if="mensaje" class="alert alert-success mt-3">
        {{ mensaje }}
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const archivo = ref(null)
const errores = ref({})
const mensaje = ref('')
const cargando = ref(false)

function handleArchivo(e) {
  archivo.value = e.target.files[0]
}
async function subirArchivo() {
  errores.value = {};
  mensaje.value = '';
  cargando.value = true;

  if (!archivo.value) {
    errores.value = { certificado: 'Por favor selecciona un archivo .pem.' };
    cargando.value = false;
    return;
  }

  const formData = new FormData();
  formData.append('certificado', archivo.value);

  try {
    const response = await axios.post('/enviar-certificado', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });

    mensaje.value = response.data.message || 'Certificado subido correctamente.';
    archivo.value = null;

    // Resetear input manualmente si es necesario
    const input = document.querySelector('input[type="file"]');
    if (input) input.value = '';
  } catch (error) {
    console.error('Error al subir certificado:', error);

    if (error.response) {
      if (error.response.status === 422) {
        errores.value = error.response.data.errors || {
          certificado: 'Error de validación del archivo.',
        };
      } else {
        errores.value = {
          certificado: error.response.data.message || 'Error inesperado del servidor.',
        };
      }
    } else {
      errores.value = { certificado: 'No se pudo conectar con el servidor.' };
    }
  } finally {
    cargando.value = false;
  }
}

</script>

<style scoped>
.card {
  background: white;
  border-radius: 12px;
  border: 1px solid #ddd;
}
</style>
