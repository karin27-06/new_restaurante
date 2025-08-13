<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <!-- Contenedor sin AuthLayout -->
    <div class="max-w-md mx-auto p-6 bg-white rounded-lg shadow-md">
        <Head title="Confirm password" />

        <h1 class="text-2xl font-bold text-center">Confirm your password</h1>
        <p class="text-center text-sm text-gray-600 mb-6">This is a secure area of the application. Please confirm your password before continuing.</p>

        <form @submit.prevent="submit">
            <div class="space-y-6">
                <div class="grid gap-2">
                    <!-- Reemplazo de Label e Input con HTML estándar -->
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input
                        id="password"
                        type="password"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        autofocus
                    />
                    <!-- Manejo de errores sin InputError -->
                    <div v-if="form.errors.password" class="text-sm text-red-600">
                        {{ form.errors.password }}
                    </div>
                </div>

                <div class="flex items-center">
                    <!-- Reemplazo de Button con un botón HTML estándar -->
                    <button 
                        type="submit" 
                        class="w-full bg-blue-500 text-white py-2 px-4 rounded" 
                        :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Confirm Password
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>
