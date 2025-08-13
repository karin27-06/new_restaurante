<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <!-- Contenedor simple en lugar de AuthBase -->
    <div class="max-w-md mx-auto p-6 bg-white rounded-lg shadow-md">
        <Head title="Register" />

        <h1 class="text-2xl font-bold text-center">Create an account</h1>
        <p class="text-center text-sm text-gray-600 mb-6">Enter your details below to create your account</p>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid gap-6">
                <!-- Reemplazo de Label e Input con HTML estándar -->
                <div class="grid gap-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input
                        id="name"
                        type="text"
                        required
                        autofocus
                        v-model="form.name"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Full name"
                    />
                    <!-- Error handling for 'name' -->
                    <div v-if="form.errors.name" class="text-sm text-red-600 mt-2">
                        {{ form.errors.name }}
                    </div>
                </div>

                <div class="grid gap-2">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <input
                        id="email"
                        type="email"
                        required
                        v-model="form.email"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                        placeholder="email@example.com"
                    />
                    <!-- Error handling for 'email' -->
                    <div v-if="form.errors.email" class="text-sm text-red-600 mt-2">
                        {{ form.errors.email }}
                    </div>
                </div>

                <div class="grid gap-2">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input
                        id="password"
                        type="password"
                        required
                        v-model="form.password"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Password"
                    />
                    <!-- Error handling for 'password' -->
                    <div v-if="form.errors.password" class="text-sm text-red-600 mt-2">
                        {{ form.errors.password }}
                    </div>
                </div>

                <div class="grid gap-2">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm password</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        required
                        v-model="form.password_confirmation"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
                        placeholder="Confirm password"
                    />
                    <!-- Error handling for 'password_confirmation' -->
                    <div v-if="form.errors.password_confirmation" class="text-sm text-red-600 mt-2">
                        {{ form.errors.password_confirmation }}
                    </div>
                </div>

                <!-- Reemplazo de Button con un botón HTML estándar -->
                <button
                    type="submit"
                    class="w-full bg-blue-500 text-white py-2 px-4 rounded"
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Create account
                </button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <!-- Reemplazo de TextLink con un enlace regular -->
                <a :href="route('login')" class="underline underline-offset-4">Log in</a>
            </div>
        </form>
    </div>
</template>
