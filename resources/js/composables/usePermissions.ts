import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function usePermission() {
  const page = usePage()

  const user = computed(() => page.props.auth?.user as {
    roles: { name: string }[]
    permissions: { name: string }[]
  })

  const hasRole = (role: string): boolean => {
    return user.value?.roles?.some(r => r.name === role) ?? false
  }

  const can = (permission: string): boolean => {
    // Se for admin, pode tudo
    if (hasRole('admin')) return true
    return user.value?.permissions?.some(p => p.name === permission) ?? false
  }

  return { can, hasRole }
}
