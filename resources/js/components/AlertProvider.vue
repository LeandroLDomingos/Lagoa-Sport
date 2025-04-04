<script setup lang="ts">
import { ref, provide } from 'vue';
import { Alert } from '@shadcn-vue/alert';

// Estado para o erro
const errorMessage = ref(null);

// Fornece o estado de erro para os componentes filhos
provide('setError', (message) => {
    errorMessage.value = message;
});

// Função para limpar o erro
const clearError = () => {
    errorMessage.value = null;
};
</script>

<style scoped>
/* Estilo opcional para centralizar o alerta na tela */
</style>

<template>
    <div v-if="errorMessage" class="absolute top-0 left-0 right-0 p-4 z-50">
        <Alert :variant="'destructive'" :title="'Erro'" :description="errorMessage" @close="clearError" />
    </div>
    <slot />
</template>
