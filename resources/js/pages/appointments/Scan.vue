<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'
import { BrowserQRCodeReader } from '@zxing/browser'
import { router, Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import AlertProvider from '@/components/AlertProvider.vue'
import { BreadcrumbItem } from '@/types'

const videoRef = ref<HTMLVideoElement | null>(null)
const errorMessage = ref('')
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Validar Agendamento', href: '/dashboard' }
]

let codeReader: BrowserQRCodeReader | null = null
let stream: MediaStream | null = null
onMounted(async () => {
  if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
    errorMessage.value = 'Seu navegador não suporta acesso à câmera.'
    return
  }
  try {
    // 1. Pede acesso manual à câmera
    stream = await navigator.mediaDevices.getUserMedia({ video: true })

    if (videoRef.value) {
      videoRef.value.srcObject = stream
      await videoRef.value.play()
    }

    // 2. Inicia o leitor de QR depois que a câmera já estiver rodando
    codeReader = new BrowserQRCodeReader()
    const devices = await BrowserQRCodeReader.listVideoInputDevices()

    if (!devices.length) {
      errorMessage.value = 'Nenhuma câmera encontrada.'
      return
    }

    const selectedDeviceId = devices[0].deviceId

    await codeReader.decodeFromVideoDevice(
      selectedDeviceId,
      videoRef.value!,
      (result, error, controls) => {
        if (result) {
          controls.stop()

          const url = result.getText()
          const match = url.match(/appointments\/checking\/(\d+)/)
          const id = match?.[1]

          if (id) {
            router.visit(`/appointments/checking/${id}`)
          } else {
            errorMessage.value = 'QR Code inválido.'
          }
        }
      }
    )
  } catch (err) {
    errorMessage.value = 'Erro ao acessar a câmera. Verifique as permissões.'
    console.error(err)
  }
})

onUnmounted(() => {
  if (codeReader) {
    codeReader.reset()
  }

  if (stream) {
    stream.getTracks().forEach((track) => track.stop())
  }
})
</script>

<template>

  <Head title="Validar Agendamento" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <AlertProvider />

    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="max-w-md mx-auto p-6 text-center">
          <h1 class="text-2xl font-bold mb-4">Validar Agendamento</h1>
          <video ref="videoRef" class="w-full rounded-xl shadow-md border border-gray-200" autoplay playsinline></video>
          <p v-if="errorMessage" class="text-red-600 mt-4">{{ errorMessage }}</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
