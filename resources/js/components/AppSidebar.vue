<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue'
import NavMain from '@/components/NavMain.vue'
import NavUser from '@/components/NavUser.vue'
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar'
import { type NavItem } from '@/types'
import { Link } from '@inertiajs/vue3'
import { PersonStanding, LayoutGrid } from 'lucide-vue-next'
import AppLogo from './AppLogo.vue'
import { usePermission } from '@/composables/usePermissions'

const { can } = usePermission()

// Itens do menu principal (sempre visíveis para quem pode)
const mainNavItems: NavItem[] = [
  {
    title: 'Painel de controle',
    href: '/dashboard',
    icon: LayoutGrid,
  },
]

// Itens do menu do rodapé (com subitens)
const footerNavItems: NavItem[] = [
  {
    title: 'Usuários',
    href: '/users',
    icon: PersonStanding,
    permission: 'view_users',
    children: [
      {
        title: 'Aprovar Usuários',
        href: '/users/analising',
        permission: 'approve_users',
      },
    ],
  },
]

// Filtra itens baseado nas permissões
const filteredMainItems = mainNavItems.filter(item => !item.permission || can(item.permission))

const filteredFooterItems = footerNavItems
  .map(item => {
    const children = item.children?.filter(child => !child.permission || can(child.permission)) || []
    const canShow = (!item.permission || can(item.permission)) || children.length > 0

    return canShow ? { ...item, children } : null
  })
  .filter(Boolean) as NavItem[]
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <!-- Logo -->
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="route('dashboard')">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <!-- Menu Principal -->
    <SidebarContent>
      <NavMain :items="filteredMainItems" />
    </SidebarContent>

    <!-- Rodapé com Submenus e Informações do Usuário -->
    <SidebarFooter>
      <NavFooter :items="filteredFooterItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>

  <!-- Slot para conteúdo da página -->
  <slot />
</template>
