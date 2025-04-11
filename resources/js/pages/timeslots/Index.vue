<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import AlertProvider from '@/components/AlertProvider.vue'
import type { BreadcrumbItem, Location, TimeSlot } from '@/types'
import HeadingSmall from '@/components/HeadingSmall.vue'
import { Clock } from 'lucide-vue-next'

// Props vindas do controller
const props = defineProps<{
  location: Location
  timeSlots: TimeSlot[]
  weekStart: string
  weekEnd: string
  weekOptions: { label: string; value: string }[]
}>()


// Controle de seleção de todos
const selectAll = ref(false)

// Formulário para envio dos horários liberados
const selectedSlots = ref<string[]>([])

// Quando marcar/desmarcar todos
watch(selectAll, (val) => {
  if (val) {
    selectedSlots.value = props.timeSlots
      .filter(slot => slot.is_available)
      .map(slot => `${slot.date}|${slot.start_time}|${slot.end_time}`)
  } else {
    selectedSlots.value = []
  }
})

// Agrupar por data
const slotsByDate = computed(() => {
  return props.timeSlots.reduce((acc, slot) => {
    if (!acc[slot.date]) acc[slot.date] = []
    acc[slot.date].push(slot)
    return acc
  }, {} as Record<string, TimeSlot[]>)
})

console.log(props);

// Alternar checkbox individual
function toggleSlotCheckbox(slot: TimeSlot) {
  const key = `${slot.date}|${slot.start_time}|${slot.end_time}`
  if (selectedSlots.value.includes(key)) {
    selectedSlots.value = selectedSlots.value.filter(k => k !== key)
  } else {
    selectedSlots.value.push(key)
  }
}

function liberate() {
  const form = useForm({
    slots: selectedSlots.value.map(key => {
      const [date, start_time, end_time] = key.split('|')
      return { date, start_time, end_time }
    })
  })

  form.post(route('timeslots.store', { location: props.location.id }))
}

// Seleção de semana
function changeWeek(e: Event) {
  const val = (e.target as HTMLSelectElement).value
  $inertia.get(route('timeslots.index', {
    location: props.location.id,
    week_start: val
  }))
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Quadras/Locais', href: '/locations' },
    { title: props.location.name, href: '' },
    { title: 'Horários', href: '' }
  ]
</script>

<template>
  <Head :title="`Horários - ${props.location.name}`" />

  <AppLayout :breadcrumbs= breadcrumbs>
    <AlertProvider /> 
    <div class="p-6 rounded-lg shadow-md space-y-6">
      <HeadingSmall title="Liberar horários"/>

      <!-- Select para trocar semana -->
      <div class="mb-4">
        <Label for="week">Semana </Label>
        <select id="week" class="border rounded px-2 py-1" @change="e => $inertia.get(route('timeslots.index', {
          location: props.location.id,
          week_start: e.target.value
        }))">
          <option v-for="option in props.weekOptions" :key="option.value" :value="option.value"
            :selected="option.value === props.weekStart">
            {{ option.label }}
          </option>
        </select>
      </div>

      <!-- Checkbox Marcar todos -->
      <div class="mb-4">
        <label class="inline-flex items-center space-x-2">
          <input type="checkbox" v-model="selectAll" />
          <span>Marcar todos</span>
        </label>
      </div>

      <!-- Dias e horários com checkboxes -->
      <div v-for="(slots, date) in slotsByDate" :key="date" class="mb-6">
        <h3 class="font-semibold text-lg mb-2">
          📅 {{ new Date(date + 'T00:00:00').toLocaleDateString('pt-BR', {
            weekday: 'long',
            day: '2-digit',
            month: '2-digit'
          }) }}
        </h3>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-4">
          <label
            v-for="slot in slots"
            :key="`${slot.date}-${slot.start_time}`"
            class="border rounded px-3 py-2 flex justify-between items-center gap-2 cursor-pointer"
          >
            <div class="flex items-center gap-2">
              <input
                v-if="slot.is_available"
                type="checkbox"
                :value="`${slot.date}|${slot.start_time}|${slot.end_time}`"
                :checked="selectedSlots.includes(`${slot.date}|${slot.start_time}|${slot.end_time}`)"
                @change="() => toggleSlotCheckbox(slot)"
              />
              <span>
                {{ slot.start_time }} às {{ slot.end_time }}<br>
                <small v-if="!slot.is_available" class="text-green-600">Liberado</small>
                <small v-else class="text-red-600">Fechado</small>
              </span>
            </div>

            <!-- Ícone no final -->
            <Clock class="text-gray-500 w-5 h-5 ml-2" />
          </label>
        </div>
      </div>

      <!-- Botão -->
      <div class="text-right">
        <Button
          type="button"
          @click="liberate"
          :disabled="selectedSlots.length === 0"
        >
          Liberar horários selecionados
        </Button>
      </div>
    </div>
  </AppLayout>
</template>
