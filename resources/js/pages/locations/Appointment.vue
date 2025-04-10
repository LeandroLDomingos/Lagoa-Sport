<script setup lang="ts">
import { type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import AlertProvider from '@/components/AlertProvider.vue'
import HeadingSmall from '@/components/HeadingSmall.vue'
import { Card, CardContent } from '@/components/ui/card'
import {
  Carousel,
  CarouselContent,
  CarouselItem,
  CarouselNext,
  CarouselPrevious
} from '@/components/ui/carousel'

// Tipos para a imagem e localização
interface LocationImage {
  id: number
  image: string
}

interface Location {
  id: string
  name: string
  address: string
  images: LocationImage[]
}

// Props vindas do Laravel via Inertia
const props = defineProps<{
  location: Location
}>()

// Breadcrumbs para navegação
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Quadras/Locais', href: '/locations' },
  { title: 'Agendar', href: '' },
]
</script>

<template>
  <Head title="Quadras/Locais" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <AlertProvider />

    <div class="p-6 bg-white rounded-lg shadow-md flex flex-col items-center w-full gap-6">

      <!-- Carrossel de imagens -->
      <Carousel class="relative w-full max-w-md">
        <CarouselContent>
          <CarouselItem v-for="(image, index) in location.images" :key="index">
            <Card>
              <CardContent class="flex aspect-square items-center justify-center p-6">
                <img :src="`/storage/${image.image}`" alt="Imagem">
              </CardContent>
            </Card>
          </CarouselItem>
        </CarouselContent>
        <CarouselPrevious />
        <CarouselNext />
      </Carousel>

      <div class="text-center">
        <h2 class="text-xl font-bold">{{ location.name }}</h2>
        <p class="text-gray-600">{{ location.address }}</p>
      </div>

      <HeadingSmall title="Selecione uma quadra/local" />

    </div>
  </AppLayout>
</template>
