<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { Appointment, BreadcrumbItem } from '@/types';
import AlertProvider from '@/components/AlertProvider.vue';
import VoucherComponent from './components/VoucherComponent.vue';
import { ref } from 'vue';

const props = defineProps<{
    appointment: Appointment
}>()
const baseUrl = ref(window.location.origin);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Agendamentos', href: '/appointments' },
    { title: 'Comprovante', href: '' },
]
</script>

<template>

    <Head title="Comprovante de Agendamento" />
    <AppLayout :breadcrumbs=breadcrumbs>
        <AlertProvider />
        <div class="p-6 space-y-6 ">
            <VoucherComponent :appointment="props.appointment" :baseUrl="baseUrl" class="justify-center"/>
        </div>
    </AppLayout>
</template>
