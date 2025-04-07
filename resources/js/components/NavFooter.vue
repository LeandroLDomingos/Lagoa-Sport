<script setup lang="ts">
import {
  SidebarGroup,
  SidebarGroupContent,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar'

import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
} from '@/components/ui/dropdown-menu'

import { type NavItem } from '@/types'
import { ref } from 'vue'

interface Props {
  items: NavItem[]
  class?: string
}

defineProps<Props>()
</script>

<template>
  <SidebarGroup :class="`group-data-[collapsible=icon]:p-0 ${$props.class || ''}`">
    <SidebarGroupContent>
      <SidebarMenu>
        <SidebarMenuItem v-for="item in items" :key="item.title">
          <!-- Item com children vira dropdown -->
          <template v-if="item.children && item.children.length">
            <DropdownMenu>
              <DropdownMenuTrigger as-child>
                <SidebarMenuButton class="flex items-center justify-between w-full cursor-pointer text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100">
                  <div class="flex items-center">
                    <component :is="item.icon" class="mr-2" />
                    <span>{{ item.title }}</span>
                  </div>
                </SidebarMenuButton>
              </DropdownMenuTrigger>
              <DropdownMenuContent class="ml-6 w-48">
                <DropdownMenuItem
                  v-for="child in item.children"
                  :key="child.title"
                  as-child
                >
                  <a
                    :href="child.href"
                    rel="noopener noreferrer"
                    class="w-full block px-2 py-1.5 text-sm text-left text-neutral-700 hover:bg-neutral-100 dark:text-neutral-200 dark:hover:bg-neutral-800"
                  >
                    {{ child.title }}
                  </a>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          </template>

          <!-- Item sem children é direto -->
          <template v-else>
            <SidebarMenuButton as-child>
              <a
                :href="item.href"
                class="flex items-center text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
              >
                <component :is="item.icon" class="mr-2" />
                <span>{{ item.title }}</span>
              </a>
            </SidebarMenuButton>
          </template>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarGroupContent>
  </SidebarGroup>
</template>
