import { useAuthStore } from '~/stores/auth'

export function useApiClient() {
  const authStore = useAuthStore()
  
  const apiCall = async (
    endpoint: string,
    method: 'GET' | 'POST' | 'PUT' | 'DELETE' = 'GET',
    data?: any,
    params?: Record<string, any>
  ) => {
    try {
      // Fonction utilitaire pour les headers d'authentification
      function getAuthHeaders() {
        if (process.client && !authStore.isAuthenticated) {
          authStore.initAuth()
        }
        
        if (!authStore.token) {
          throw new Error('Token d\'authentification manquant. Veuillez vous reconnecter.')
        }
        
        return {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${authStore.token}`
        }
      }
      
      // Construction de l'URL
      const config = useRuntimeConfig()
      let url = `${config.public.apiBase}${endpoint}`
      
      if (params) {
        const searchParams = new URLSearchParams()
        Object.entries(params).forEach(([key, value]) => {
          if (value !== null && value !== undefined && value !== '') {
            searchParams.append(key, value.toString())
          }
        })
        const queryString = searchParams.toString()
        if (queryString) {
          url += `?${queryString}`
        }
      }
      
      // Configuration des headers
      const headers = getAuthHeaders()
      
      // Configuration de la requête
      const fetchOptions: any = {
        method,
        headers
      }
      
      // Ajout des données pour les requêtes POST/PUT
      if (data && (method === 'POST' || method === 'PUT')) {
        fetchOptions.body = data
      }
      
      // Envoi de la requête avec $fetch
      const rawResponse = await $fetch(url, fetchOptions)
      
      // Parse the response if it's a string
      let response: any
      if (typeof rawResponse === 'string') {
        try {
          response = JSON.parse(rawResponse)
        } catch (parseError) {
          console.error('Failed to parse response string:', parseError)
          response = rawResponse
        }
      } else {
        response = rawResponse
      }
      
      return response
      
    } catch (error: any) {
      console.error('API Error:', error)
      
      // Gestion de l'authentification expirée
      if (error.status === 401 || error.statusCode === 401) {
        authStore.logout()
        await navigateTo('/login')
      }
      
      throw error
    }
  }
  
  return {
    apiCall
  }
}
