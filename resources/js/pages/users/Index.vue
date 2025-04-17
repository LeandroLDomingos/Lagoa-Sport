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
  getSortedRowModel,
  getPaginationRowModel,
  getFilteredRowModel,
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

// Define um dicionário para traduzir os nomes dos papéis e dos status
const translations: Record<string, string> = {
  admin: 'Administrador',
  guest: 'Visitante',
  manager: 'Gerente',
  active: 'Ativo',
  pending: 'Pendente',
  blocked: 'Bloqueado',
  inactive: 'Inativo',
  is_analising: 'Em Analise'
  // Adicione outras traduções conforme necessário
}

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
          'Nome',
          h(ChevronsUpDown, { class: 'ml-2 h-4 w-4' })
        ]
      ),
    cell: ({ row }) =>
      h('div', { class: 'font-medium' }, row.getValue('name')),
  }),
  // Coluna 'email'
  columnHelper.accessor('email', {
    header: () => h('div', { class: 'text-left' }, 'E-mail'),
    cell: ({ row }) => h('div', { class: 'text-left' }, row.getValue('email')),
  }),
  // Coluna 'status'
  columnHelper.accessor('status', {
    header: () => h('div', { class: 'text-left' }, 'Status'),
    cell: ({ row }) => {
      //obtem o status do usuário
      const status = row.getValue('status')
      // Traduz o status utilizando o dicionário; caso não exista a tradução, usa o valor original
      const statusNames = translations[status.toLowerCase()] || status
      return h('div', { class: 'capitalize' }, statusNames)
    },
  }),

  // Coluna 'roles'
  columnHelper.accessor('roles', {
    header: () => h('div', { class: 'text-left' }, 'Perfil'),
    cell: ({ row }) => {
      // Obtém o array de roles; caso não haja, retorna um array vazio.
      const roles: any[] = row.getValue('roles') || []
      // Mapeia os papéis, traduzindo pelo dicionário. Se não houver tradução, usa o valor original.
      const roleNames = roles
        .map(role => translations[role.name.toLowerCase()] || role.name)
        .join(', ')
      return h('div', { class: 'text-left capitalize' }, roleNames)
    },
  }),
]

// Estado reativo para ordenação
const sorting = ref([])
// Estado reativo para a paginação, armazenando o índice e o tamanho da página
const pagination = ref({
  pageIndex: 0,
  pageSize: 10,  // valor inicial: 10 usuários por página
})
// Estado reativo para filtragem
const columnFilters = ref([])

// Instancia o objeto da tabela, passando os dados vindos da controller e as configurações necessárias
const table = useVueTable({
  data: props.users,
  columns,
  getCoreRowModel: getCoreRowModel(),
  getSortedRowModel: getSortedRowModel(), // habilita ordenação
  getPaginationRowModel: getPaginationRowModel(), // habilita paginação
  getFilteredRowModel: getFilteredRowModel(), // habilita filtragem
  state: {
    get sorting() {
      return sorting.value
    },
    get pagination() {
      return pagination.value
    },
    get columnFilters() {
      return columnFilters.value
    }
  },
  onSortingChange: updaterOrValue => {
    sorting.value = typeof updaterOrValue === 'function'
      ? updaterOrValue(sorting.value)
      : updaterOrValue
  },
  onPaginationChange: updaterOrValue => {
    pagination.value = typeof updaterOrValue === 'function'
      ? updaterOrValue(pagination.value)
      : updaterOrValue
  },
  onColumnFiltersChange: updaterOrValue => {
    columnFilters.value =
      typeof updaterOrValue === 'function'
        ? updaterOrValue(columnFilters.value)
        : updaterOrValue
  },
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
        <Input
          placeholder="Filtrar por nome ou email..."
          :model-value="table.getColumn('name')?.getFilterValue() || ''"
          @update:model-value="value => {
            // Atualiza o filtro da coluna 'name'
            table.getColumn('name')?.setFilterValue(value)
          }"
        />
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

      <!-- Controles de Paginação -->
      <div class="flex flex-col md:flex-row items-center justify-between mt-4">
        <!-- Seletor de Quantos usuários por página -->
        <div class="flex items-center space-x-2">
          <span class="text-sm">Exibir:</span>
          <select class="border rounded p-1 text-sm" :value="table.getState().pagination.pageSize"
            @change="event => table.setPageSize(Number(event.target.value))">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <span class="text-sm">usuários por página</span>
        </div>

        <!-- Botões de navegação entre as páginas -->
        <div class="flex items-center space-x-2 mt-2 md:mt-0">
          <Button variant="outline" size="sm" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">
            Previous
          </Button>
          <span class="text-sm">
            Página {{ table.getState().pagination.pageIndex + 1 }} de {{ table.getPageCount() }}
          </span>
          <Button variant="outline" size="sm" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
            Next
          </Button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>