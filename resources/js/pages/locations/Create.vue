<script setup lang="ts">
import { type BreadcrumbItem } from '@/types'
import { Head, } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import AlertProvider from '@/components/AlertProvider.vue'
import HeadingSmall from '@/components/HeadingSmall.vue'
import CreateFormComponent from './components/CreateFormComponent.vue'

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Quadras/Locais', href: '/locations' },
  { title: 'Criar Locais', href: '/locations/create' },
]
</script>

<template>

  <Head title="Quadras/Locais" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <AlertProvider />

    <div class="p-6 rounded-lg shadow-md flex flex-col items-center">
      <HeadingSmall title="Selecione uma quadra/local" />

      <!-- Container do form, centralizado e com largura limitada -->
      <div class="w-full max-w-md mt-4">
        <CreateFormComponent class="w-full" />
      </div>
    </div>

  </AppLayout>
</template>
