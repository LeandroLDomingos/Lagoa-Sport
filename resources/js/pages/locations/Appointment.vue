<script setup lang="ts">
import { Location, TimeSlot, type BreadcrumbItem } from '@/types'
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
import { ref, onMounted } from 'vue'
import { Clock, MapPin } from 'lucide-vue-next'
import Button from '@/components/ui/button/Button.vue'

function formatTime(ts: string) {
  const [h, m] = ts.split(':')
  return `${h}:${m}`
}

const selectedDate = ref<string | null>(null)

onMounted(() => {
  const dates = Object.keys(props.slotsByDate)
  if (dates.length > 0) {
    selectedDate.value = dates[0]
  }
})

// Props vindas do Laravel via Inertia
const props = defineProps<{
  location: Location
  slotsByDate: Record<string, TimeSlot[]>
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

      <!-- Carrossel de imagens -->
      <Carousel class="relative w-full max-w-4xl">
        <CarouselContent>
          <!-- Caso tenha imagens, renderiza o carousel -->
          <CarouselItem v-for="(image, index) in props.location.images" :key="index">
            <Card class="flex aspect-[16/9] items-center justify-center p-4">
              <CardContent>
                <img :src="`/storage/${image.image}`" alt="Imagem"
                  class="max-h-full w-auto h-auto object-contain rounded-md" />
              </CardContent>

            </Card>
          </CarouselItem>

          <!-- Caso não tenha nenhuma imagem, mostra a imagem padrão -->
          <CardContent v-if="!props.location.images?.length" class="flex aspect-[16/9] items-center justify-center p-4">
            <img src="/default.png" alt="Imagem padrão" class="w-full h-full object-cover rounded-md" />
          </CardContent>
        </CarouselContent>
        <CarouselPrevious />
        <CarouselNext />
      </Carousel>

      <div class="text-center">
        <h2 class="text-x font-bold">{{ props.location.name }}</h2>
        <p>
          <a :href="'https://www.google.com/maps?q=' + encodeURIComponent(props.location.address)" target="_blank"
            class="flex items-center hover:text-blue-500 hover:underline">
            <!-- Ícone de localização ao lado do texto -->
            <MapPin class="mr-2" />
            {{ props.location.address }}
          </a>
        </p>


      </div>
      <!-- Barra de dias -->
      <div v-if="Object.keys(slotsByDate).length === 0">
        <h3 class="font-semibold text-lg mb-2">Nenhum horário disponível</h3>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-7 gap-4 mb-6">
        <button v-for="(slots, dateKey) in props.slotsByDate" :key="dateKey" @click="selectedDate = dateKey" :class="[
          'flex-shrink-0 px-4 py-2 rounded-lg text-center min-w-[4rem] transition',
          selectedDate === dateKey
            ? 'bg-primary text-primary-foreground'
            : 'bg-primary/10 hover:bg-primary/20 text-primary'
        ]">
          <div class="text-sm font-semibold">
            {{ new Date(dateKey + 'T00:00:00').toLocaleDateString('pt-BR', { weekday: 'short' }).toUpperCase() }}
          </div>
          <div class="text-lg">
            {{ new Date(dateKey + 'T00:00:00').getDate() }}
          </div>
        </button>
      </div>

      <!-- Aqui você pode renderizar os horários da data selecionada, se quiser -->
      <div v-if="selectedDate && props.slotsByDate[selectedDate]">
        <h3 class="font-semibold text-lg mb-2">Horários disponíveis:</h3>
        <ul class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
          <li v-for="(slot, id) in props.slotsByDate[selectedDate]" :key="slot.id">
            <a :href="route('appointments.create', slot.id)">
              <Button class="bg-primary/10 text-primary hover:text-black">
                {{ formatTime(slot.start_time) }} às {{ formatTime(slot.end_time) }}
                <Clock />
              </Button>
            </a>
          </li>
        </ul>
      </div>

    </div>
  </AppLayout>
</template>
