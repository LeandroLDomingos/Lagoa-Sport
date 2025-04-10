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

interface Images {
  id: number
  name: string
  image: string

}

interface Location {
  id: string
  name: string
  address: string
}

const props = defineProps<{
  location: Location
  images: Images[]
}>()

</script>

<template>
  <Card>
    <CardHeader>
      <img :src="images[0].image" alt="" class="rounded-md border-2 border-black-700">
    </CardHeader>
    <CardContent>
      <CardTitle>{{ location.name }}</CardTitle>
      <CardDescription>{{ location.address }}</CardDescription>
    </CardContent>
    <CardFooter>
      <Button>
        <a :href="`/locations/${location.id}`">Agendar</a>
      </Button>
    </CardFooter>
  </Card>
</template>