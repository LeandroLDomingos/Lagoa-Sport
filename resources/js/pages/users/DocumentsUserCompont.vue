<script setup lang="ts">
import { ref } from 'vue'

interface Document {
  id: number
  file_path: string
  type: string
  status: string
}

interface User {
  id: number
  cpf: string
  rg: string
  name: string
  email: string
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

const approveDocument = (doc: Document) => {
  console.log('Aprovado:', doc)
  // Aqui você faria uma requisição POST/PUT com Inertia ou Axios para aprovar
}

const rejectDocument = (doc: Document) => {
  console.log('Reprovado:', doc)
  // Aqui também uma requisição com status de reprovado
}

function isPdf(path: string): boolean {
  return path.toLowerCase().endsWith('.pdf');
}
</script>

<template>
  <div class="mb-6 p-4 border rounded shadow-sm bg-white">
    <h2 class="text-lg font-semibold mb-2">{{ user.name }}</h2>
    <ul>
      <li v-for="doc in documents" :key="doc.id" class="flex justify-between items-center p-2 border-b">
        <div>
          <p class="font-medium">{{ doc.type }}</p>
          <p class="text-sm text-gray-500">Status: {{ doc.status }}</p>
        </div>
        <div class="flex gap-2">
          <button class="text-blue-600 hover:underline" @click="openModal(doc)">
            Ver detalhes
          </button>
          <button class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600" @click="approveDocument(doc)">
            Aprovar
          </button>
          <button class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600" @click="rejectDocument(doc)">
            Reprovar
          </button>
        </div>
      </li>
    </ul>

    <!-- Modal -->
<!-- Modal -->
<div v-if="showModal && selectedDocument" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
  <div class="bg-white p-6 rounded shadow-lg w-full max-w-6xl max-h-[90vh] overflow-y-auto relative">
    <h3 class="text-xl font-bold mb-4">{{ selectedDocument.type }}</h3>
    <h4 class="text-md font-semibold mb-4">CPF: {{ user.cpf }} | RG: {{ user.rg }}</h4>

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

    <div class="flex justify-end mt-4">
      <button class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300" @click="closeModal">
        Fechar
      </button>
    </div>
  </div>
</div>

  </div>
</template>
