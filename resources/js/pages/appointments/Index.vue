<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import { BreadcrumbItem, Location, LocationImage } from '@/types';
import AlertProvider from '@/components/AlertProvider.vue';
import LocationComponent from '../locations/components/LocationComponent.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';

const props = defineProps<{
    locations: Location[]
}>()

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Horários', href: '/appointments' },
]
</script>

<template>

    <Head title="Locais" />
    <AppLayout :breadcrumbs=breadcrumbs>
        <AlertProvider />

        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between mb-4">
                <HeadingSmall title="Selecione um local" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-for="(location, index) in locations" :key="index" class="h-full">
                    <LocationComponent :location="location" :images="location.images"
                        :url="`/locations/${location.id}/timeslots`" class="h-full" :buttonName="`Liberar horários`" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
