<script setup lang="ts">
import {
  SidebarGroup,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarMenuSub,
  SidebarMenuSubItem,
} from '@/components/ui/sidebar'

import {
  Collapsible,
  CollapsibleTrigger,
  CollapsibleContent,
} from '@/components/ui/collapsible'

import { type NavItem, type SharedData } from '@/types'
import { Link, usePage } from '@inertiajs/vue3'

defineProps<{
  items: NavItem[]
}>()

const page = usePage<SharedData>()
</script>

<template>
  <SidebarGroup class="px-2 py-0">
    <SidebarGroupLabel>Serviços</SidebarGroupLabel>
    <SidebarMenu>
      <SidebarMenuItem v-for="item in items" :key="item.title">
        <!-- Se tiver filhos, usar Collapsible -->
        <template v-if="item.children && item.children.length">
          <Collapsible>
            <CollapsibleTrigger as-child>
              <SidebarMenuButton
                class="flex items-center justify-between w-full cursor-pointer text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
              >
                <div class="flex items-center">
                  <component :is="item.icon" class="mr-2" />
                  <span>{{ item.title }}</span>
                </div>
                <svg
                  class="ml-2 h-4 w-4 transition-transform group-data-[state=open]:rotate-90"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M6.293 8.293a1 1 0 011.414 0L10 10.586l2.293-2.293a1 1 0 111.414 1.414L10 13.414l-3.707-3.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </SidebarMenuButton>
            </CollapsibleTrigger>
            <CollapsibleContent>
              <SidebarMenuSub>
                <SidebarMenuSubItem
                  v-for="child in item.children"
                  :key="child.title"
                  as-child
                >
                  <Link
                    :href="child.href"
                    class="w-full block px-2 py-1.5 text-sm text-neutral-700 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-neutral-800"
                  >
                    {{ child.title }}
                  </Link>
                </SidebarMenuSubItem>
              </SidebarMenuSub>
            </CollapsibleContent>
          </Collapsible>
        </template>

        <!-- Se não tiver filhos, é botão simples -->
        <template v-else>
          <SidebarMenuButton
            as-child
            :is-active="item.href === page.url"
            :tooltip="item.title"
          >
            <Link :href="item.href" class="flex items-center">
              <component :is="item.icon" class="mr-2" />
              <span>{{ item.title }}</span>
            </Link>
          </SidebarMenuButton>
        </template>
      </SidebarMenuItem>
    </SidebarMenu>
  </SidebarGroup>
</template>
