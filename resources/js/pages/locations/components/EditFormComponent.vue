<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Input from '@/components/ui/input/Input.vue'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import InputError from '@/components/InputError.vue'
import { Upload, X } from 'lucide-vue-next'
import { Location, LocationImage } from '@/types'

const props = defineProps<{
  location: Location
  existingImages: LocationImage[]
  canEdit: boolean
}>()

const form = useForm({
  images: [] as File[],
  name: '',
  address: '',
  min_participants: 0,
  removedImageIds: [] as number[],
})

const newPreviews = ref<string[]>([])
const existingPreviews = ref<LocationImage[]>([...props.existingImages])

function onFilesChange(e: Event) {
  const input = e.target as HTMLInputElement
  if (!input.files) return
  form.images = Array.from(input.files)
}

watch(
  () => form.images,
  (files) => {
    newPreviews.value.forEach(url => URL.revokeObjectURL(url))
    newPreviews.value = files.map(f => URL.createObjectURL(f))
  }
)

function removeExistingImage(id: number) {
  form.removedImageIds.push(id)
  existingPreviews.value = existingPreviews.value.filter(img => img.id !== id)
}

function submit() {
  form.post(
    route('locations.update', { location: props.location.id }),
    {
      forceFormData: true,
      onFinish: () => {
        // limpa previews
        newPreviews.value.forEach(url => URL.revokeObjectURL(url))
        newPreviews.value = []
      },
    }
  )
}

onMounted(() => {
  form.name = props.location.name
  form.address = props.location.address
  form.min_participants = props.location.min_participants
})

function destroy() {
  if (!confirm('Tem certeza que deseja excluir este local?')) return

  form.delete(route('locations.destroy', { location: props.location.id }), {
    onSuccess: () => {
    }
  })
}
</script>

<template>

  <form @submit.prevent="submit" class="flex flex-col gap-6">
    <div class="grid gap-2">
      <Label for="name">Nome</Label>
      <Input id="name" v-model="form.name" type="text" required />
      <InputError :message="form.errors.name" />
    </div>

    <div class="grid gap-2">
      <Label for="address">Endereço</Label>
      <Input id="address" v-model="form.address" type="text" required />
      <InputError :message="form.errors.address" />
    </div>

    <!-- Quantidade minima para 2 horas -->
    <div class="grid gap-2">
      <Label for="min_participants">Minimo de participantes para 2 horas</Label>
      <Input id="min_participants" v-model="form.min_participants" type="number" required
        placeholder="Quantidade minima" />
      <InputError :message="form.errors.min_participants" />
    </div>

    <div v-if="existingPreviews.length" class="grid grid-cols-3 gap-4">
      <div v-for="img in existingPreviews" :key="img.id" class="relative h-24 bg-gray-100 rounded overflow-hidden">
        <img :src="`/storage/${img.image}`" class="object-cover w-full h-full" alt="Preview" />
        <button type="button" @click="removeExistingImage(img.id)"
          class="absolute top-1 right-1 bg-white bg-opacity-75 rounded-full p-1 hover:bg-opacity-100 transition">
          <X class="w-4 h-4 text-red-600" />
        </button>
      </div>
    </div>

    <div class="grid gap-2">
      <Label for="newImages">Adicionar Fotos</Label>
      <div class="relative mt-1">
        <Upload class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
        <Input id="newImages" type="file" multiple accept="image/*" @change="onFilesChange" class="pl-10" />
        <InputError :message="form.errors.newImages" />
      </div>
    </div>

    <div v-if="newPreviews.length" class="grid grid-cols-3 gap-4">
      <div v-for="(url, idx) in newPreviews" :key="idx" class="h-24 bg-gray-100 rounded overflow-hidden">
        <img :src="url" class="object-cover w-full h-full" />
      </div>
    </div>

    <div class="flex gap-4 pt-4">
      <Button :disabled="form.processing" type="submit" class="flex-1">
        Atualizar Local
      </Button>
      <Button variant="destructive" type="button" @click="destroy" class="flex-1">
        Excluir Local
      </Button>
    </div>
  </form>
</template>
