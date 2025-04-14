<script setup lang="ts">
const { can } = usePermission()
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
import { MapPin, Edit2 } from 'lucide-vue-next';
import { usePermission } from '@/composables/usePermissions';

const props = defineProps<{
  location: Location
  images: LocationImage[]
  url: string
  buttonName?: string
  canEdit?: boolean
}>()
</script>

<template>
  <Card>
    <CardHeader class="p-0">
      <div class="relative aspect-[16/9] w-full overflow-hidden rounded-t-md">
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

        <!-- Ícone de lápis exibido somente se o usuário tiver permissão -->
        <a
          v-if="can('locations.edit')"
          :href="`/locations/${location.id}/edit`"
          class="absolute top-2 right-2 bg-white bg-opacity-75 rounded-full p-1 hover:bg-opacity-100 transition"
        >
          <Edit2 class="w-5 h-5 text-gray-700" />
        </a>
      </div>
    </CardHeader>

    <CardContent class="mt-3">
      <CardTitle class="text-xl font-semibold leading-none tracking-tight">
        {{ location.name }}
      </CardTitle>
      <CardDescription>
        <p>
          <a
            :href="'https://www.google.com/maps?q=' + encodeURIComponent(location.address)"
            target="_blank"
            class="flex items-center hover:text-blue-500 hover:underline"
          >
            <MapPin class="mr-2" />
            {{ location.address }}
          </a>
        </p>
      </CardDescription>
    </CardContent>

    <CardFooter>
      <a :href="`${url}`">
        <Button>
          {{ buttonName ?? 'Agendar' }}
        </Button>
      </a>
    </CardFooter>
  </Card>
</template>
