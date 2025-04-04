<script setup lang="ts">
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert';
import { usePage } from '@inertiajs/vue3';
import { AlertCircle } from 'lucide-vue-next'

const { props } = usePage();
</script>


<template>
    <!-- Exibe alerta usando shadcn-vue caso exista flash.error -->
    <Alert v-if="props.flash?.error" variant="destructive" class="mb-4">
        <AlertCircle class="w-4 h-4" />
        <AlertTitle>Erro</AlertTitle>
        <AlertDescription>
            {{ props.flash.error }}
        </AlertDescription>
    </Alert>
</template>