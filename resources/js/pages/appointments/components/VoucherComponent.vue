<script setup lang="ts">
import { Appointment } from '@/types';
import { computed } from 'vue';

// Tipagem para valores nulos
type Nullable<T> = T | null;

// Define as props esperadas pelo componente
const props = defineProps<{ appointment: Appointment; baseUrl: string }>();

// Computa o valor do QR Code, apontando para a rota de conferência do agendamento
const qrValue = computed(() => `${props.baseUrl}/appointments/checking?ids=${props.appointment.id}/validation`);

// Função para formatar a data para o formato legível
const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    const options: Intl.DateTimeFormatOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    };
    return new Intl.DateTimeFormat('pt-BR', options).format(date);
};

// Função para formatar a hora para o formato HH:mm
const formatTime = (timeString: string) => {
    const [hour, minute] = timeString.split(':');
    return `${hour}:${minute}`;
};

// Computa o valor da data e hora no formato necessário
const formattedTimeRange = computed(() => {
    const slot1 = props.appointment.time_slots[0];
    const slot2 = props.appointment.time_slots[1];

    const start = formatTime(slot1?.start_time ?? '--:--');
    const end = formatTime(slot2?.end_time ?? slot1?.end_time ?? '--:--');
    const date = formatDate(slot1?.date ?? '--/--/----');

    return `${date} - ${start} às ${end}`;
});

// Função para formatar a data e hora de forma legível
const formatDateTime = (dateString: string) => {
    const date = new Date(dateString);
    const options: Intl.DateTimeFormatOptions = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false, // 24 horas
    };
    return new Intl.DateTimeFormat('pt-BR', options).format(date);
};

// Computa a data de criação formatada
const formattedCreatedAt = computed(() => {
    return formatDateTime(props.appointment.created_at ?? '--/--/---- --:--');
});
</script>

<template>
    <div class="max-w-xl mx-auto bg-white shadow-lg rounded-2xl p-6">
        <h2 class="text-2xl font-semibold mb-4">Comprovante de Agendamento</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <ul class="space-y-2">
                    <li>
                        <span class="font-medium">Agendamento:</span>
                        {{ appointment.time_slots[0].location.name }}
                    </li>
                    <li>
                        <span class="font-medium">Data e hora:</span>
                        {{ formattedTimeRange }}
                    </li>

                    <li>
                        <span class="font-medium">Responsável:</span>
                        {{ appointment.user?.name }}
                    </li>
                    <li>
                        <span class="font-medium">Criado em:</span>
                        {{ formattedCreatedAt }}
                    </li>
                </ul>
            </div>
            <div class="flex items-center justify-center pt-5 pb-5">
                <qrcode-vue :value="qrValue" :size="220" />
            </div>
        </div>
    </div>
    <div class="max-w-xl mx-auto bg-white shadow-lg rounded-2xl p-6">
        <div class="mt-6">
            <h3 class="font-medium text-lg mb-2">Participantes:</h3>
            <div class="block space-y-4">
                <div v-for="(participant, index) in appointment.participants" :key="participant.id"
                    class="border p-4 rounded-lg">
                    <p><strong>Participante {{ index + 1 }}</strong></p>
                    <p><strong>Nome:</strong> {{ participant.name }}</p>
                    <div class="grid grid-cols-2 gap-4">
                        <p><strong>CPF:</strong> {{ participant.cpf }}</p>
                        <p><strong>RG:</strong> {{ participant.rg }}</p>
                    </div>
                    <p><strong>Email:</strong> {{ participant.email }}</p>
                    <p><strong>Contato:</strong> {{ participant.contact }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
