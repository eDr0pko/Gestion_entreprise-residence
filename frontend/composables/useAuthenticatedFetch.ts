import { useAuthStore } from '~/stores/auth'

export function useAuthenticatedFetch() {
  const authStore = useAuthStore()
  const config = useRuntimeConfig()

  const getAuthHeaders = () => {
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

  const fetchWithAuth = async (url: string, options: any = {}) => {
    const headers = getAuthHeaders()
    const fullUrl = url.startsWith('/') ? `${config.public.apiBase}${url}` : url
    
    const requestOptions = {
      ...options,
      headers: {
        ...headers,
        ...options.headers
      }
    }

    try {
      const rawResponse = await $fetch(fullUrl, requestOptions)
      
      // Parse response if it's a string
      let response: any
      if (typeof rawResponse === 'string') {
        try {
          response = JSON.parse(rawResponse)
        } catch (parseError) {
          response = rawResponse
        }
      } else {
        response = rawResponse
      }
      
      return response
    } catch (error) {
      console.error('Fetch error:', error)
      throw error
    }
  }

  return {
    fetchWithAuth
  }
}
