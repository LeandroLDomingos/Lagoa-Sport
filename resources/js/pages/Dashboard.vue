<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import AlertProvider from '@/components/AlertProvider.vue';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Painel de controle',
    href: '/dashboard',
  },
];
const props = defineProps<{
  pendingUsersCount: number
}>()

</script>

<template>

  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <AlertProvider />
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div
          class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-4">
          <h2 class="text-xl font-semibold mb-2">Usuários em Analise</h2>
          <p class="text-3xl font-bold text-primary">
            {{ props.pendingUsersCount }}
          </p>
        </div>

        <div
          class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
          <PlaceholderPattern />
        </div>
        <div
          class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
          <PlaceholderPattern />
        </div>
      </div>
      <div
        class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
        <PlaceholderPattern />
      </div>
    </div>
  </AppLayout>
</template>
