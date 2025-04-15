<script setup lang="ts">
import { defineProps, ref, h } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import AlertProvider from '@/components/AlertProvider.vue'

// Componentes de tabela shadcn‑vue
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { ChevronDown, ChevronsUpDown } from 'lucide-vue-next'

// Componentes e utilitários do TanStack Vue Table
import {
  createColumnHelper,
  FlexRender,
  useVueTable,
  getCoreRowModel,
} from '@tanstack/vue-table'

// Tipos de usuário e breadcrumb (de acordo com sua estrutura)
import type { User, BreadcrumbItem } from '@/types'

// Recebendo os dados vindos da controller via props
const props = defineProps<{
  users: User[]
}>()

// Definição dos breadcrumbs para o layout
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Usuários', href: '/users' },
]

// Cria o helper tipado para definir as colunas (ajuda a manter o código mais seguro em relação aos tipos)
const columnHelper = createColumnHelper<User>()

// Definição das colunas da tabela
// Ajuste os campos conforme sua estrutura de dados.
const columns = [
  // Coluna 'name' com botão no header para ordenar
  columnHelper.accessor('name', {
    header: ({ column }) =>
      h(
        Button,
        {
          variant: 'ghost',
          onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
        },
        () => [
          'Name',
          h(ChevronsUpDown, { class: 'ml-2 h-4 w-4' })
        ]
      ),
    cell: ({ row }) =>
      h('div', { class: 'font-medium' }, row.getValue('name')),
  }),
  // Coluna 'email'
  columnHelper.accessor('email', {
    header: () => h('div', { class: 'text-left' }, 'Email'),
    cell: ({ row }) =>
      h('div', { class: 'text-left' }, row.getValue('email')),
  }),
  // Coluna 'status'
  columnHelper.accessor('status', {
    header: () => h('div', { class: 'text-left' }, 'Status'),
    cell: ({ row }) =>
      h('div', { class: 'capitalize' }, row.getValue('status')),
  }),
  // Coluna 'roles' – supomos que roles é um array de objetos com a propriedade name
  columnHelper.accessor('roles', {
    header: () => h('div', { class: 'text-left' }, 'Prerfil'),
    cell: ({ row }) => {
      const roles: any[] = row.getValue('roles') || []
      const roleNames = roles.map(role => role.name).join(', ')
      return h('div', { class: 'text-left' }, roleNames)
    },
  }),
]

// Estado reativo para a ordenação (você pode adicionar outros estados para filtros, seleção, etc.)
const sorting = ref([])

// Instancia o objeto da tabela, passando os dados vindos da controller e as configurações necessárias
const table = useVueTable({
  data: props.users,
  columns,
  getCoreRowModel: getCoreRowModel(),
  state: {
    get sorting() {
      return sorting.value
    }
  },
  onSortingChange: updaterOrValue => {
    sorting.value = typeof updaterOrValue === 'function'
      ? updaterOrValue(sorting.value)
      : updaterOrValue
  }
})
</script>

<template>
  <!-- Define o título da página -->

  <Head title="Usuários" />
  <!-- Utiliza o layout padrão da aplicação -->
  <AppLayout :breadcrumbs="breadcrumbs">
    <AlertProvider />

    <div class="p-6 rounded-lg shadow-md">
      <!-- Exemplo de input para filtro (a lógica de filtragem pode ser implementada conforme necessário) -->
      <div class="mb-4">
        <Input placeholder="Filtrar por nome ou e-mail" />
      </div>

      <!-- Container da Tabela -->
      <div class="rounded-md border">
        <Table>
          <!-- Cabeçalho da Tabela -->
          <TableHeader>
            <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
              <TableHead v-for="header in headerGroup.headers" :key="header.id">
                <!-- Renderiza o conteúdo do header com FlexRender -->
                <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
              </TableHead>
            </TableRow>
          </TableHeader>

          <!-- Corpo da Tabela -->
          <TableBody>
            <!-- Verifica se há linhas para exibir -->
            <template v-if="table.getRowModel().rows?.length">
              <TableRow v-for="row in table.getRowModel().rows" :key="row.id">
                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                  <!-- Renderiza o conteúdo das células -->
                  <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                </TableCell>
              </TableRow>
            </template>

            <!-- Exibe uma mensagem caso não haja registros -->
            <TableRow v-else>
              <TableCell :colspan="columns.length" class="text-center h-24">
                Nenhum registro encontrado.
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </AppLayout>
</template>