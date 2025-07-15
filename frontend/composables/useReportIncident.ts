import { ref } from 'vue'
import { useAuthStore } from '~/stores/auth'

export function useReportIncident() {
  const loading = ref(false)
  const error = ref('')
  const success = ref(false)
  const apiBase = useRuntimeConfig().public.apiBase
  const authStore = useAuthStore()

  async function reportIncident(description: string, statut: string = 'en_cours') {
    loading.value = true
    error.value = ''
    success.value = false
    try {
      await $fetch(`${apiBase}/admin/incidents`, {
        method: 'POST',
        body: {
          datetime: new Date().toISOString(),
          object: description,
          statut
        },
        headers: { Authorization: `Bearer ${authStore.token}` },
      })
      success.value = true
    } catch (e: any) {
      error.value = e?.data?.message || 'Erreur lors du signalement.'
    }
    loading.value = false
  }

  return { reportIncident, loading, error, success }
}
