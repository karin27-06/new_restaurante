<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};
</script>

<template>
    <!-- Contenedor simple en lugar de AuthLayout -->
    <div class="max-w-md mx-auto p-6 bg-white rounded-lg shadow-md">
        <Head title="Email verification" />

        <h1 class="text-2xl font-bold text-center">Verify email</h1>
        <p class="text-center text-sm text-gray-600 mb-6">Please verify your email address by clicking on the link we just emailed to you.</p>

        <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-green-600">
            A new verification link has been sent to the email address you provided during registration.
        </div>

        <form @submit.prevent="submit" class="space-y-6 text-center">
            <!-- Botón HTML básico en lugar de Button -->
            <button :disabled="form.processing" class="w-full bg-blue-500 text-white py-2 px-4 rounded" type="submit">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                Resend verification email
            </button>

            <!-- Enlace regular en lugar de TextLink -->
            <a :href="route('logout')" class="mx-auto block text-sm text-blue-600"> Log out </a>
        </form>
    </div>
</template>
