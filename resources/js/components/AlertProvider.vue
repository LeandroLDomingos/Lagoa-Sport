<script setup lang="ts">
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert';
import { usePage } from '@inertiajs/vue3';
import { AlertCircle, ShieldX, ShieldCheck } from 'lucide-vue-next'

const { props } = usePage();
</script>


<template>
    <!-- Exibe alerta usando shadcn-vue caso exista flash.error -->
    <Alert v-if="props.flash?.error" variant="destructive" class="mb-4">
        <ShieldX class="mt-2" />
        <AlertTitle>Alerta</AlertTitle>
        <AlertDescription>
            {{ props.flash.error }}
        </AlertDescription>
    </Alert>
    <Alert v-if="props.flash?.success" class="mb-4">
        <ShieldCheck class="mt-2" />
        <AlertTitle>Sucesso</AlertTitle>
        <AlertDescription>
            {{ props.flash.success }}
        </AlertDescription>
    </Alert>

    
</template>