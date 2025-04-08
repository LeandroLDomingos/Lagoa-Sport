<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

interface Document {
  id: number
  file_path: string
  type: string
  status: string
  status_label: string
  type_label: string
}

interface User {
  id: number
  name: string
  email: string
  cpf: string
  rg: string
  zip_code: string
  address: string
  neighborhood: string
  number: string
  complement?: string
  city: string
  state: string
  country: string
}

const props = defineProps<{
  user: User
  documents: Document[]
}>()

const selectedDocument = ref<Document | null>(null)
const showModal = ref(false)

const openModal = (doc: Document) => {
  selectedDocument.value = doc
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedDocument.value = null
}

const approveDocument = (doc: Document | null) => {
  if (!doc) return
  router.post(`/documents/${doc.id}/approve`, {}, {
    onSuccess: () => closeModal()
  })
}

const rejectDocument = (doc: Document | null) => {
  if (!doc) return
  router.post(`/documents/${doc.id}/reject`, {}, {
    onSuccess: () => closeModal()
  })
}

const approveUser = () => {
  router.post(`/users/${props.user.id}/approve`)
}

const allDocumentsApproved = computed(() =>
  props.documents.every(doc => doc.status === 'approved')
)

function isPdf(path: string): boolean {
  return path.toLowerCase().endsWith('.pdf');
}
</script>

<template>
  <div class="mb-6 p-4 border rounded shadow-sm bg-white">
    <h2 class="text-lg font-semibold mb-2">{{ user.name }}</h2>

    <ul>
      <li
        v-for="doc in documents"
        :key="doc.id"
        class="flex justify-between items-center p-2 border-b"
      >
        <div>
          <p class="font-medium">{{ doc.type_label }}</p>
          <p class="text-sm text-gray-500">Status: {{ doc.status_label }}</p>
        </div>
        <div>
          <button class="text-blue-600 hover:underline" @click="openModal(doc)">
            Ver detalhes
          </button>
        </div>
      </li>
    </ul>

    <div class="mt-4 text-right">
      <button
        class="px-4 py-2 rounded text-white"
        :class="allDocumentsApproved ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed'"
        :disabled="!allDocumentsApproved"
        @click="approveUser"
      >
        Aprovar Usuário
      </button>

      <p v-if="!allDocumentsApproved" class="text-sm text-red-500 mt-1">
        Todos os documentos devem ser aprovados para liberar a aprovação do usuário.
      </p>
    </div>

    <!-- Modal -->
    <div
      v-if="showModal && selectedDocument"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
      <div class="bg-white p-6 rounded shadow-lg w-full max-w-6xl max-h-[90vh] overflow-y-auto relative">
        <h3 class="text-xl font-bold mb-4">{{ selectedDocument.type_label }}</h3>

        <!-- Endereço apenas para residence_proof -->
        <h4
          v-if="selectedDocument?.type === 'residence_proof'"
          class="text-md font-semibold mb-4"
        >
          Endereço:
          {{ user.address }}, nº {{ user.number }},
          {{ user.neighborhood }} - {{ user.city }}/{{ user.state }},
          {{ user.country }}
          <template v-if="user.complement">
            ({{ user.complement }})
          </template>
        </h4>

        <h4
          v-if="selectedDocument?.type !== 'residence_proof'"
          class="text-md font-semibold mb-4"
        >
          CPF:
          {{ user.cpf }}
          <br> RG: {{ user.rg }}
        </h4>

        <div class="border rounded p-2 bg-neutral-100 max-h-[65vh] overflow-auto flex justify-center items-center">
          <template v-if="isPdf(selectedDocument.file_path)">
            <iframe
              :src="`/private-file/${selectedDocument.file_path}`"
              width="100%"
              height="600px"
              class="rounded border"
            ></iframe>
          </template>

          <template v-else>
            <img
              :src="`/private-file/${selectedDocument.file_path}`"
              alt="Documento"
              class="rounded max-w-full max-h-[60vh] object-contain cursor-zoom-in transition-transform duration-300 hover:scale-150"
            />
          </template>
        </div>

        <div class="flex justify-end mt-4 gap-2">
          <button
            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
            @click="approveDocument(selectedDocument)"
          >
            Aprovar Documento
          </button>
          <button
            class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
            @click="rejectDocument(selectedDocument)"
          >
            Reprovar Documento
          </button>
          <button
            class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300"
            @click="closeModal"
          >
            Fechar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
