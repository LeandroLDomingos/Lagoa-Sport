<script setup lang="ts">
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import AlertProvider from '@/components/AlertProvider.vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import DocumentsUserComponent from './components/DocumentsUserComponent.vue';

const page = usePage();

const users = ref(page.props.users);
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Usuários', href: '/documents' },
];
</script>



<template>

    <Head title="Usuários" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <AlertProvider />
        <div class="px-4 py-6">
            <HeadingSmall title="Autorize usuários"/>
            <div v-for="(user, index) in users" :key="index">
                <DocumentsUserComponent :user="user" :documents="user.documents" />
            </div>
        </div>
    </AppLayout>
</template>
