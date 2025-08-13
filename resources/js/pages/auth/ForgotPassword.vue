<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <!-- Contenedor sin AuthLayout -->
    <div class="max-w-md mx-auto p-6 bg-white rounded-lg shadow-md">
        <Head title="Forgot password" />

        <h1 class="text-2xl font-bold text-center">Forgot password</h1>
        <p class="text-center text-sm text-gray-600 mb-6">Enter your email to receive a password reset link.</p>

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="space-y-6">
                <div class="grid gap-2">
                    <!-- Reemplazo de Label e Input con HTML estándar -->
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="off"
                        v-model="form.email"
                        autofocus
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                        placeholder="email@example.com"
                    />
                    <!-- Error handling for 'email' -->
                    <div v-if="form.errors.email" class="text-sm text-red-600 mt-2">
                        {{ form.errors.email }}
                    </div>
                </div>

                <div class="my-6 flex items-center justify-start">
                    <!-- Botón HTML básico en lugar de Button.vue -->
                    <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Email password reset link
                    </button>
                </div>
            </div>
        </form>

        <div class="space-x-1 text-center text-sm text-muted-foreground">
            <span>Or, return to</span>
            <!-- Reemplazado TextLink con un enlace regular -->
            <a :href="route('login')" class="underline underline-offset-4">log in</a>
        </div>
    </div>
</template>
