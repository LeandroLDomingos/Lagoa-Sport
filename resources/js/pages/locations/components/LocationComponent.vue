<script setup lang="ts">
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/components/ui/card'
import Button from '@/components/ui/button/Button.vue'
import { Location, LocationImage } from '@/types';




const props = defineProps<{
  location: Location
  images: LocationImage[]
  url: String
}>()

</script>

<template>
  <Card>
    <CardHeader class="p-0">
      <div class="aspect-[16/9] w-full overflow-hidden rounded-t-md">
        <img
          v-if="images[0]?.image"
          :src="images[0].image"
          alt=""
          class="w-full h-full object-cover"
        />
        <img
          v-else
          src="/default.png"
          alt="Imagem padrão"
          class="w-full h-full object-cover"
        />
      </div>
    </CardHeader>

    <CardContent class="mt-3">
      <CardTitle class="text-xl font-semibold leading-none tracking-tight">{{ location.name }}</CardTitle>
      <CardDescription>{{ location.address }}</CardDescription>
    </CardContent>

    <CardFooter>
      <a :href="`${url}`">
      <Button>
          Agendar
        </Button>
      </a>
    </CardFooter>
  </Card>
</template>
