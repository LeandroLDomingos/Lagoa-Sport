<script setup lang="ts">
import { type BreadcrumbItem } from '@/types'
import { Head, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import AlertProvider from '@/components/AlertProvider.vue'
import HeadingSmall from '@/components/HeadingSmall.vue'
import LocationComponent from './components/LocationComponent.vue'
import { Button } from '@/components/ui/button'
import { ref } from 'vue'
import { usePermission } from '@/composables/usePermissions'

const { can } = usePermission()

const page = usePage()
const locations = ref(page.props.locations)

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Quadras/Locais', href: '/locations' },
]
</script>

<template>

  <Head title="Quadras/Locais" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <AlertProvider />

    <div class="p-6 rounded-lg shadow-md">
      <div class="flex items-center justify-between mb-4">
        <HeadingSmall title="Selecione uma quadra/local" />
        <Button v-if="can('locations.create')" as-child>
          <a href="/locations/create">
            Adicionar Local
          </a>
        </Button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div v-for="(location, index) in locations" :key="index" class="h-full">
          <LocationComponent :location="location" :images="location.images" :url="`/locations/${location.id}`" class="h-full" />
        </div>
      </div>

    </div>
  </AppLayout>
</template>
