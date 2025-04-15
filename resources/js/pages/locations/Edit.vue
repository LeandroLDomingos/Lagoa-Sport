<script setup lang="ts">
import { Location, type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import AlertProvider from '@/components/AlertProvider.vue'
import HeadingSmall from '@/components/HeadingSmall.vue'
import EditFormComponent from './components/EditFormComponent.vue'

// Props vindas do controller Inertia (location + imagens existentes)
const props = defineProps<{
  location: Location
  existingImages: { id: number; image: string }[]
  canEdit: boolean
}>()

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Quadras/Locais', href: '/locations' },
  { title: 'Editar Local', href: `/locations/${props.location.id}/edit` },
]
</script>

<template>

  <Head title="Editar Local" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <AlertProvider />
    <div class="p-6 rounded-lg shadow-md flex flex-col items-center w-full">
      <HeadingSmall title="Editar Quadra/Local" />

      <!-- Container do form, centralizado e com largura limitada -->
      <div class="w-full max-w-lg mt-4">
        <EditFormComponent :location="props.location" :existing-images="props.existingImages"
          :can-edit="props.canEdit" />
      </div>
    </div>
  </AppLayout>
</template>
