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

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Quadras/Locais', href: '/locations' },
  { title: props.location.name, href: '' },
  { title: 'Horários', href: '' },
]

// ----------------------
// Semana select
// ----------------------
const selectedWeek = ref(props.weekStart)
watch(selectedWeek, (val) => {
  $inertia.get(route('timeslots.index', {
    location: props.location.id,
    week_start: val
  }))
})

// ----------------------
// Datas da semana
// ----------------------
const weekDates = computed<string[]>(() => {
  const dates: string[] = []
  let cur = new Date(props.weekStart + 'T00:00:00')
  const end = new Date(props.weekEnd + 'T00:00:00')
  while (cur <= end) {
    dates.push(cur.toISOString().slice(0, 10))
    cur.setDate(cur.getDate() + 1)
  }
  return dates
})

// Agrupa slots por data
const slotsByDate = computed(() => {
  return props.timeSlots.reduce((acc, slot) => {
    ; (acc[slot.date] ||= []).push(slot)
    return acc
  }, {} as Record<string, TimeSlot[]>)
})

// Datas ativas (tem slot 8–21)
const activeWeekDates = computed(() =>
  weekDates.value.filter(date => {
    const arr = slotsByDate.value[date] || []
    return arr.some(s => {
      const h = parseInt(s.start_time.split(':')[0], 10)
      return h >= 8 && h <= 21
    })
  })
)

// Dia selecionado
const selectedDate = ref(activeWeekDates.value[0] || '')
// ao mudar dia, resetar seleção
watch(selectedDate, () => {
  selectAllByDate.value[selectedDate.value] = false
  selectedSlotsByDate.value[selectedDate.value] = []
})

// Slots visíveis para o dia
const slotsForSelectedDate = computed(() =>
  (slotsByDate.value[selectedDate.value] || [])
    .filter(s => {
      const h = parseInt(s.start_time.split(':')[0], 10)
      return h >= 8 && h <= 21
    })
)

// ----------------------
// Seleção de slots
// ----------------------
const selectAllByDate = ref<Record<string, boolean>>({})
const selectedSlotsByDate = ref<Record<string, string[]>>({})

// inicializa por dia
activeWeekDates.value.forEach(date => {
  selectAllByDate.value[date] = false
  selectedSlotsByDate.value[date] = []
})

// marcar/desmarcar todos para o dia
watch(selectAllByDate, (all) => {
  const date = selectedDate.value
  if (all[date]) {
    selectedSlotsByDate.value[date] = slotsForSelectedDate.value
      .filter(s => s.is_available)
      .map(s => `${s.date}|${s.start_time}|${s.end_time}`)
  } else {
    selectedSlotsByDate.value[date] = []
  }
})

// toggle individual
function toggleSlotCheckbox(slot: TimeSlot) {
  const date = selectedDate.value
  const key = `${slot.date}|${slot.start_time}|${slot.end_time}`
  const arr = selectedSlotsByDate.value[date]
  const idx = arr.indexOf(key)
  if (idx > -1) arr.splice(idx, 1)
  else arr.push(key)
  // atualiza selectAll
  selectAllByDate.value[date] =
    arr.length === slotsForSelectedDate.value.filter(s => s.is_available).length
}

// ----------------------
// Envio ao backend
// ----------------------
function liberate() {
  const slots = selectedSlotsByDate.value[selectedDate.value] || []
  const form = useForm({
    slots: slots.map(k => {
      const [date, start_time, end_time] = k.split('|')
      return { date, start_time, end_time }
    })
  })
  form.post(route('timeslots.store', { location: props.location.id }), {
    onSuccess: () => {
      // limpa seleção do dia
      selectedSlotsByDate.value[selectedDate.value] = []
      selectAllByDate.value[selectedDate.value] = false
    }
  })
}

// formata HH:mm:ss → HH:mm
function formatTime(ts: string) {
  const [h, m] = ts.split(':')
  return `${h}:${m}`
}
</script>

<template>

  <Head :title="`Horários - ${props.location.name}`" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <AlertProvider />

    <div class="p-6 rounded-lg shadow space-y-6">
      <HeadingSmall title="Liberar horários" />

      <!-- Select para trocar semana -->
      <div class="mb-4">
        <Label for="week">Semana </Label>
        <select id="week" @change="e => $inertia.get(route('timeslots.index', {
          location: props.location.id,
          week_start: e.target.value
        }))"
        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-primary focus:ring-1 focus:ring-primary"
         >
          <option v-for="option in props.weekOptions" :key="option.value" :value="option.value"
            :selected="option.value === props.weekStart">
            {{ option.label }}
          </option>
        </select>
      </div>

      <!-- Barra de dias -->
      <div class="grid grid-cols-2 md:grid-cols-4  lg:grid-cols-5 xl:grid-cols-7 gap-4 mb-6">
        <button v-for="date in activeWeekDates" :key="date" @click="selectedDate = date" :class="date === selectedDate
          ? 'bg-primary text-primary-foreground'
          : 'bg-primary/10 hover:bg-primary/20 text-primary'"
          class="flex-shrink-0 px-4 py-2 rounded-lg text-center min-w-[4rem] transition">
          <div class="text-sm font-semibold">
            {{ new Date(date + 'T00:00:00').toLocaleDateString('pt-BR', { weekday: 'short' }).toUpperCase() }}
          </div>
          <div class="text-lg">{{ new Date(date + 'T00:00:00').getDate() }}</div>
        </button>
      </div>


      <!-- Marcar todos -->
      <!-- <div class="mb-4">
        <label class="inline-flex items-center space-x-2">
          <input type="checkbox" v-model="selectAllByDate[selectedDate]" class="h-4 w-4" />
          <span class="select-none">Marcar todos</span>
        </label>
      </div> -->

      <!-- Horários do dia -->
      <div class="grid grid-cols-2 md:grid-cols-4  lg:grid-cols-5 xl:grid-cols-7 gap-4 mb-6">
        <label v-for="slot in slotsForSelectedDate" :key="slot.start_time"
          class="border rounded px-3 py-2 flex justify-between items-center cursor-pointer">
          <div class="flex items-center space-x-2">
            <input v-if="!slot.is_available" type="checkbox" :value="`${slot.date}|${slot.start_time}|${slot.end_time}`"
              :checked="selectedSlotsByDate[selectedDate].includes(`${slot.date}|${slot.start_time}|${slot.end_time}`)"
              @change="() => toggleSlotCheckbox(slot)" class="h-4 w-4" />
            <span class="text-sm">
              {{ formatTime(slot.start_time) }} às {{ formatTime(slot.end_time) }}
              <br>
              <small v-if="slot.is_available" class="text-green-600">Liberado</small>
              <small v-else class="text-red-600">Fechado</small>
            </span>
          </div>
          <Clock class="text-gray-500 w-5 h-5" />
        </label>
      </div>

      <!-- Botão Liberar -->
      <div class="text-right">
        <Button @click="liberate" :disabled="!selectedSlotsByDate[selectedDate]?.length">
          Liberar horários selecionados
        </Button>
      </div>
    </div>
  </AppLayout>
</template>
