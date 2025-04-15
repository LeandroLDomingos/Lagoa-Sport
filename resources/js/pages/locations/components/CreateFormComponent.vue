<script setup lang="ts">
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Input from '@/components/ui/input/Input.vue'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import InputError from '@/components/InputError.vue';
import { Upload } from 'lucide-vue-next'

const form = useForm({
    images: [] as File[],
    name: '',
    address: '',
    min_participants: '',
})

// array reativo de URLs para preview
const previews = ref<string[]>([])

// quando form.images mudar, gera URLs de preview
watch(
    () => form.images,
    (files) => {
        previews.value.forEach(url => URL.revokeObjectURL(url))
        previews.value = files.map(f => URL.createObjectURL(f))
    }
)

// handler para seleção de arquivos
function onFilesChange(e: Event) {
    const input = e.target as HTMLInputElement
    if (!input.files) return
    form.images = Array.from(input.files)
}

// envia o formulário
function submit() {
    form.post(route('locations.store'), {
        forceFormData: true,
        onFinish: () => {
            // limpa previews
            previews.value.forEach(url => URL.revokeObjectURL(url))
            previews.value = []
        },
    })
}
</script>

<template>
    <form @submit.prevent="submit" class="flex flex-col gap-6 max-w-md mx-auto">
        <!-- Nome -->
        <div class="grid gap-2">
            <Label for="name">Nome</Label>
            <Input id="name" v-model="form.name" type="text" required placeholder="Nome da Quadra/Local" />
            <InputError :message="form.errors.name" />
        </div>

        <!-- Endereço -->
        <div class="grid gap-2">
            <Label for="address">Endereço</Label>
            <Input id="address" v-model="form.address" type="text" required placeholder="Endereço da Quadra/Local" />
            <InputError :message="form.errors.address" />
        </div>

        <!-- Quantidade minima para 2 horas -->
        <div class="grid gap-2">
            <Label for="min_participants">Minimo de participantes para 2 horas</Label>
            <Input id="min_participants" v-model="form.min_participants" type="number" required placeholder="Quantidade minima" />
            <InputError :message="form.errors.min_participants" />
        </div>

        <!-- Imagens -->
        <div class="grid gap-2">
            <Label for="images">Fotos</Label>
            <div class="relative mt-1">
                <Upload class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
                <Input id="images" type="file" multiple accept="image/*" @change="onFilesChange"
                    class="pl-10" />
                <InputError :message="form.errors.images" />
            </div>
        </div>

        <!-- Preview das imagens -->
        <div v-if="previews.length" class="grid grid-cols-3 gap-4">
            <div v-for="(url, idx) in previews" :key="idx" class="h-24 bg-gray-100 rounded overflow-hidden">
                <img :src="url" class="object-cover w-full h-full" />
            </div>
        </div>

        <!-- Botão de Enviar -->
        <div class="pt-4">
            <Button :disabled="form.processing" type="submit" class="w-full">
                Criar Local
            </Button>
        </div>
    </form>
</template>
