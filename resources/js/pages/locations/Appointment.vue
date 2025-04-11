<script setup lang="ts">
import { Location, type BreadcrumbItem } from '@/types'
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

    <div class="p-6 rounded-lg shadow-md flex flex-col items-center w-full gap-6">
      <HeadingSmall title="Selecione um horário" />

      <!-- Carrossel de imagens -->
      <Carousel class="relative w-full max-w-4xl">
        <CarouselContent>
          <!-- Caso tenha imagens, renderiza o carousel -->
          <CarouselItem v-for="(image, index) in location.images" :key="index">
            <Card>
              <CardContent class="flex aspect-[16/9] items-center justify-center p-4">
                <img :src="`/storage/${image.image}`" alt="Imagem" class="w-full h-full object-cover rounded-md" />
              </CardContent>
            </Card>
          </CarouselItem>

          <!-- Caso não tenha nenhuma imagem, mostra a imagem padrão -->
          <CardContent v-if="!location.images?.length" class="flex aspect-[16/9] items-center justify-center p-4">
            <img src="/default.png" alt="Imagem padrão" class="w-full h-full object-cover rounded-md" />
          </CardContent>
        </CarouselContent>
        <CarouselPrevious />
        <CarouselNext />
      </Carousel>


      <div class="text-center">
        <h2 class="text-x font-bold">{{ location.name }}</h2>
        <p class="text-gray-600">{{ location.address }}</p>
      </div>


    </div>
  </AppLayout>
</template>
