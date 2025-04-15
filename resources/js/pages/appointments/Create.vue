<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Input from '@/components/ui/input/Input.vue'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import InputError from '@/components/InputError.vue'
import type { TimeSlot } from '@/types'
import Select from '@/components/ui/select/Select.vue'
import SelectTrigger from '@/components/ui/select/SelectTrigger.vue'
import SelectValue from '@/components/ui/select/SelectValue.vue'
import SelectContent from '@/components/ui/select/SelectContent.vue'
import SelectGroup from '@/components/ui/select/SelectGroup.vue'
import SelectItem from '@/components/ui/select/SelectItem.vue'

// Props
const props = defineProps<{
  slot: TimeSlot
  canExtendTime: boolean
  nextSlotId: number | null
}>()

// Breadcrumbs
const breadcrumbs = [
  { title: 'Locais', href: '/locations' },
  { title: 'Agendar', href: '' },
]

// Usuário atual da página
const currentUser = computed(() => usePage().props.auth.user as {
  name: string
  cpf: string
  rg: string
  contact: string
  email: string
})

// Inertia form
const form = useForm({
  timeSlotIds: [props.slot.id] as number[],  // já vem com o slot selecionado
  participants: [] as Array<{
    name: string
    cpf: string
    rg: string
    contact: string
    email: string
  }>,
})


const participants = computed(() => form.participants)

const participantForm = reactive({
  name: '',
  cpf: '',
  rg: '',
  contact: '',
  email: '',
})

const participantErrors = reactive({
  name: '',
  cpf: '',
  rg: '',
  contact: '',
  email: '',
})

function isValidCPF(cpf: string): boolean {
  cpf = cpf.replace(/[^\d]/g, '')
  if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) return false

  let sum = 0
  for (let i = 0; i < 9; i++) sum += parseInt(cpf[i]) * (10 - i)
  let rest = (sum * 10) % 11
  if (rest === 10 || rest === 11) rest = 0
  if (rest !== parseInt(cpf[9])) return false

  sum = 0
  for (let i = 0; i < 10; i++) sum += parseInt(cpf[i]) * (11 - i)
  rest = (sum * 10) % 11
  if (rest === 10 || rest === 11) rest = 0
  if (rest !== parseInt(cpf[10])) return false

  return true
}

function validateParticipantForm() {
  let valid = true

  // Nome: pelo menos 3 letras, só letras e espaços
  participantErrors.name =
    /^[A-Za-zÀ-ÿ\s]{3,}$/.test(participantForm.name.trim())
      ? ''
      : 'Nome inválido. Use pelo menos 3 letras e apenas letras/acentos.'

  // CPF: validação matemática
  participantErrors.cpf = isValidCPF(participantForm.cpf.trim())
    ? ''
    : 'CPF inválido'

  // RG: pelo menos 5 caracteres
  participantErrors.rg =
    participantForm.rg.trim().length >= 5
      ? ''
      : 'RG deve ter pelo menos 5 caracteres'

  // Contato: (00) 00000-0000 ou (00) 0000-0000
  participantErrors.contact = /^\(?\d{2}\)?\s?\d{4,5}-?\d{4}$/.test(
    participantForm.contact.trim()
  )
    ? ''
    : 'Contato deve estar no formato (99) 99999-9999 ou (99) 9999-9999'

  // Email
  participantErrors.email = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(
    participantForm.email.trim()
  )
    ? ''
    : 'Email inválido'

  Object.values(participantErrors).forEach((error) => {
    if (error) valid = false
  })

  return valid
}

const isParticipantFormValid = computed(() => {
  return validateParticipantForm()
})

function saveParticipant() {
  if (!validateParticipantForm()) return

  // Normaliza CPF removendo tudo que não for dígito
  const cpfNormalized = participantForm.cpf.replace(/[^\d]/g, '')
  // Verifica se já existe participante com esse CPF
  const exists = participants.value.some(p =>
    p.cpf.replace(/[^\d]/g, '') === cpfNormalized
  )

  if (exists) {
    participantErrors.cpf = 'Este CPF já foi adicionado como participante.'
    return
  }

  // Se não existe, adiciona
  participants.value.push({ ...participantForm })
  resetParticipantForm()
}

function resetParticipantForm() {
  participantForm.name = ''
  participantForm.cpf = ''
  participantForm.rg = ''
  participantForm.contact = ''
  participantForm.email = ''
  // limpa erros também
  participantErrors.name = ''
  participantErrors.cpf = ''
  participantErrors.rg = ''
  participantErrors.contact = ''
  participantErrors.email = ''
}

function isSelf(participant: any) {
  const user = currentUser.value
  return (
    participant.name === user.name &&
    participant.cpf === user.cpf &&
    participant.email === user.email
  )
}

function removeParticipant(idx: number) {
  const removed = participants.value[idx]

  if (isSelf(removed)) {
    isSelfParticipant.value = false
  }

  participants.value.splice(idx, 1)
}

const isSelfParticipant = ref(false)

function addCurrentUserAsParticipant() {
  const user = currentUser.value
  const exists = participants.value.some(p => p.email === user.email)
  isSelfParticipant.value = true
  if (!exists) {
    participants.value.push({
      name: user.name,
      cpf: user.cpf,
      rg: user.rg,
      contact: user.contact,
      email: user.email,
    })
  }
}

function submitAppointment() {
  form.participants = participants.value
  form.post(route('appointments.store'))
}

const extendTime = ref(false)

const selectedTimeLabel = computed(() => {
  if (!form.timeSlotIds.length) return ''
  return form.timeSlotIds.length === 2
    ? '2 horas (2 horários)'
    : '1 hora (padrão)'
})

const timeSlotString = ref<string | null>(null)

watch(timeSlotString, (val) => {
  form.timeSlotIds = val ? JSON.parse(val) : []
})
</script>
<template>
  <AppLayout :breadcrumbs="breadcrumbs">

    <Head title="Criar Agendamento" />

    <div class="p-6 bg-white rounded shadow">
      <h1 class="text-2xl font-semibold mb-4">Criar Agendamento</h1>
      <form @submit.prevent="submitAppointment" class="space-y-6">
        <!-- Slot selecionado -->
        <div>
          <Label>Horário Selecionado</Label>
          <div class="mt-2 inline-flex items-center px-3 py-2 bg-gray-100 rounded">
            <span class="font-medium">{{ slot.date }}:</span>
            <span class="ml-2">{{ slot.start_time }} – {{ slot.end_time }}</span>
          </div>
        </div>
        <!-- Botão: Sou um participante -->
        <Button v-if="!isSelfParticipant" type="button" @click="addCurrentUserAsParticipant" class="w-full">
          Sou um participante
        </Button>
        
        <!-- Participantes -->
        <div>
          <h2 class="text-lg font-medium mb-2">Participantes</h2>
          
          <ul v-if="participants.length" class="space-y-2 mb-4">
            <li v-for="(p, idx) in participants" :key="idx"
            class="flex justify-between items-center bg-gray-50 p-3 rounded">
            <div>
              <p class="font-semibold">{{ p.name }}</p>
              <p class="text-sm text-gray-600">
                {{ p.cpf }} | {{ p.rg }} | {{ p.contact }} | {{ p.email }}
              </p>
            </div>
            <button type="button" @click="removeParticipant(idx)" class="text-red-600 hover:underline">
              Remover
            </button>
          </li>
        </ul>
        
        <h2 class="text-lg font-medium mb-2">Adicionar participante</h2>
        <!-- Formulário de participante -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <!-- Nome -->
            <div>
              <Label for="p-name">Nome</Label>
              <Input id="p-name" v-model="participantForm.name" type="text" />
              <InputError :message="participantErrors.name" />
            </div>

            <!-- CPF -->
            <div>
              <Label for="p-cpf">CPF</Label>
              <Input id="p-cpf" v-model="participantForm.cpf" type="text" />
              <InputError :message="participantErrors.cpf" />
            </div>

            <!-- RG -->
            <div>
              <Label for="p-rg">RG</Label>
              <Input id="p-rg" v-model="participantForm.rg" type="text" />
              <InputError :message="participantErrors.rg" />
            </div>

            <!-- Contato -->
            <div>
              <Label for="p-contact">Contato</Label>
              <Input id="p-contact" v-model="participantForm.contact" type="text" />
              <InputError :message="participantErrors.contact" />
            </div>

            <!-- Email -->
            <div class="md:col-span-2">
              <Label for="p-email">Email</Label>
              <Input id="p-email" v-model="participantForm.email" type="email" />
              <InputError :message="participantErrors.email" />
            </div>
            <div class="md:col-span-2 flex justify-end space-x-2">
              <Button type="button" variant="secondary" @click="resetParticipantForm">
                Cancelar
              </Button>
              <Button type="button" :disabled="!isParticipantFormValid" @click="saveParticipant">
                Adicionar Participante
              </Button>

            </div>
          </div>

          <InputError :message="form.errors.participants" />
        </div>

        <!-- Tempo estendido -->
        <div v-if="canExtendTime && form.participants.length > 6" class="mt-4">
          <Label>Tempo de agendamento</Label>
          <Select v-model="timeSlotString">
            <SelectTrigger>
              <SelectValue placeholder="Selecione o tempo de agendamento" :text-value="selectedTimeLabel" />
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectItem :value="JSON.stringify([slot.id])">
                  1 hora (padrão)
                </SelectItem>
                <SelectItem :value="JSON.stringify([slot.id, nextSlotId])">
                  2 horas (2 horários)
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
        </div>

        <!-- Enviar -->
        <Button type="submit" :disabled="form.processing" class="w-full">
          Confirmar Agendamento
        </Button>
      </form>
    </div>
  </AppLayout>
</template>