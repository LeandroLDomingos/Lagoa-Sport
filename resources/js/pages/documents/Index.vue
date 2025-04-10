<script setup lang="ts">
import { ref } from 'vue'
import { type BreadcrumbItem } from '@/types'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import AlertProvider from '@/components/AlertProvider.vue'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import HeadingSmall from '@/components/HeadingSmall.vue'
import { Upload } from 'lucide-vue-next'

interface DocumentField {
  type: 'identity-front' | 'identity-back' | 'residence_proof'
  file: File | null
  preview: string | null
}

const documents = ref<DocumentField[]>([
  { type: 'identity-front', file: null, preview: null },
  { type: 'identity-back', file: null, preview: null },
  { type: 'residence_proof', file: null, preview: null },
])

const handleFileChange = (file: File, index: number) => {
  documents.value[index].file = file

  if (file.type.startsWith('image/')) {
    documents.value[index].preview = URL.createObjectURL(file)
  } else if (file.type === 'application/pdf') {
    documents.value[index].preview = file.name
  } else {
    documents.value[index].preview = null
  }
}

const submit = () => {
  const formData = new FormData()

  documents.value.forEach((doc, index) => {
    if (doc.file) {
      formData.append(`documents[${index}][file]`, doc.file)
      formData.append(`documents[${index}][type]`, doc.type)
    }
  })

  router.post(route('documents.store'), formData, {
    forceFormData: true,
    preserveScroll: true,
  })
}

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Documentos', href: '/documents' },
]
</script>

<template>
  <Head title="Documentos" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <AlertProvider />

    <div class="p-6 rounded-lg shadow-md">
      <HeadingSmall
        title="Envie seus documentos"
        description="Envie seus documentos para análise"
      />

      <form @submit.prevent="submit" class="space-y-6">
        <div
          v-for="(document, index) in documents"
          :key="index"
          class="grid grid-cols-1 md:grid-cols-2 gap-6"
        >
          <div class="w-full max-w-sm">
            <Label :for="`file-input-${index}`">
              {{
                document.type === 'identity-front'
                  ? 'Identidade (Frente)'
                  : document.type === 'identity-back'
                  ? 'Identidade (Verso)'
                  : 'Comprovante de Residência'
              }}
            </Label>

            <div class="relative mt-1">
              <Upload
                class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"
              />
              <Input
                :id="`file-input-${index}`"
                type="file"
                accept=".pdf,.jpg,.jpeg,.png"
                class="pl-10"
                @change="handleFileChange($event.target.files[0], index)"
                required
              />
            </div>

            <div class="mt-2">
              <img
                v-if="document.preview && document.file?.type.startsWith('image/')"
                :src="document.preview"
                alt="Pré-visualização"
                class="w-40 h-auto rounded border"
              />
              <div
                v-else-if="document.preview && document.file?.type === 'application/pdf'"
                class="text-sm text-gray-600"
              >
                📄 {{ document.preview }}
              </div>
            </div>
          </div>
        </div>

        <Button type="submit" class="mt-4">
          Enviar
        </Button>
      </form>
    </div>
  </AppLayout>
</template>
