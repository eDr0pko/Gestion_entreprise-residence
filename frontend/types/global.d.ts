// Global TypeScript declarations for Vue components
declare module '*.vue' {
  import { DefineComponent } from 'vue'
  const component: DefineComponent<{}, {}, any>
  export default component
}

// Component-specific declarations
declare module './BadgeFormModal.vue' {
  import { DefineComponent } from 'vue'
  const BadgeFormModal: DefineComponent<{}, {}, any>
  export default BadgeFormModal
}

declare module './BadgeDetailModal.vue' {
  import { DefineComponent } from 'vue'
  const BadgeDetailModal: DefineComponent<{}, {}, any>
  export default BadgeDetailModal
}

// Nuxt-specific declarations
declare module '~/composables/useNotifications' {
  export function useNotifications(): {
    showNotification: (message: string, type?: string) => void
    showSuccess: (message: string) => void
    showError: (message: string) => void
    showWarning: (message: string) => void
    showInfo: (message: string) => void
  }
}

// Removed legacy duplicate declaration for useAuthenticatedFetch (handled directly by composable file)
