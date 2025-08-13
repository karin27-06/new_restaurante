<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

interface Props {
    token: string;
    email: string;
}

const props = defineProps<Props>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <!-- Contenedor simple en lugar de AuthLayout -->
    <div class="max-w-md mx-auto p-6 bg-white rounded-lg shadow-md">
        <Head title="Reset password" />

        <h1 class="text-2xl font-bold text-center">Reset password</h1>
        <p class="text-center text-sm text-gray-600 mb-6">Please enter your new password below.</p>

        <form @submit.prevent="submit">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <!-- Reemplazo de Label con HTML estándar -->
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <!-- Reemplazo de Input con un campo <input> HTML estándar -->
                    <input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        v-model="form.email"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                        readonly
                        placeholder="email@example.com"
                    />
                    <!-- Error handling for 'email' -->
                    <div v-if="form.errors.email" class="text-sm text-red-600 mt-2">
                        {{ form.errors.email }}
                    </div>
                </div>

                <div class="grid gap-2">
                    <!-- Reemplazo de Label con HTML estándar -->
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <!-- Reemplazo de Input con un campo <input> HTML estándar -->
                    <input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        v-model="form.password"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                        autofocus
                        placeholder="Password"
                    />
                    <!-- Error handling for 'password' -->
                    <div v-if="form.errors.password" class="text-sm text-red-600 mt-2">
                        {{ form.errors.password }}
                    </div>
                </div>

                <div class="grid gap-2">
                    <!-- Reemplazo de Label con HTML estándar -->
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <!-- Reemplazo de Input con un campo <input> HTML estándar -->
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Confirm password"
                    />
                    <!-- Error handling for 'password_confirmation' -->
                    <div v-if="form.errors.password_confirmation" class="text-sm text-red-600 mt-2">
                        {{ form.errors.password_confirmation }}
                    </div>
                </div>

                <!-- Botón HTML básico en lugar de Button.vue -->
                <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded mt-4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Reset password
                </button>
            </div>
        </form>
    </div>
</template>
