<script setup lang="ts">
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import AlertProvider from '@/components/AlertProvider.vue';

const form = useForm({
    documents: [
        { type: 'identity', file: null },
        { type: 'residence_proof', file: null },
    ],
});

const submit = () => {
    form.post(route('documents.store'));
};

const breadcrumbs: BreadcrumbItem[] = [

];

</script>

<template>

    <Head title="Documentos" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <AlertProvider />

        <div class="p-6 bg-white rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Envie seus documentos</h2>

            <form @submit.prevent="submit" class="space-y-4">
                <div v-for="(document, index) in form.documents" :key="index">
                    <label class="block font-medium text-gray-700">
                        {{ document.type === 'identity' ? 'Identidade (RG ou CNH)' : 'Comprovante de Residência' }}
                    </label>
                    <input type="file" @input="document.file = $event.target.files[0]"
                        class="mt-1 block w-full border p-2 rounded" />
                </div>

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Enviar</button>
            </form>
        </div>
    </AppLayout>
</template>
