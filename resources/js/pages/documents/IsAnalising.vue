<script setup lang="ts">
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import AlertProvider from '@/components/AlertProvider.vue';

import HeadingSmall from '@/components/HeadingSmall.vue';


const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Documentos', href: '/documents' },
];
</script>



<template>

    <Head title="Documentos" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <AlertProvider />

        <div class="p-6 bg-white rounded-lg shadow-md">
            <HeadingSmall title="Documentos em analise" description="Seus documentos estão sendo analisados" />

        </div>
    </AppLayout>
</template>
