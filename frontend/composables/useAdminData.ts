import { ref } from 'vue'
import { useAuthStore } from '~/stores/auth'

export interface PersonData {
  id_personne: number;
  nom: string;
  prenom: string;
  email: string;
  numero_telephone?: string;
  photo_profil?: string;
  role?: string;
  statut?: string;
  date_creation?: string;
  date_nomination?: string;
  adresse_logement?: string;
  niveau_acces?: number;
  roles?: string[];
}

export function useAdminData() {
  const incidents = ref<any[]>([])
  const logs = ref<any[]>([])
  const residents = ref<PersonData[]>([])
  const guests = ref<any[]>([])
  const stats = ref<any>({})

  const loadingIncidents = ref(false)
  const loadingLogs = ref(false)
  const loadingResidents = ref(false)
  const loadingGuests = ref(false)
  const loadingStats = ref(false)

  const errorIncidents = ref<any>(null)
  const errorLogs = ref<any>(null)
  const errorResidents = ref<any>(null)
  const errorGuests = ref<any>(null)
  const errorStats = ref<any>(null)

  // Fonction utilitaire pour les headers d'authentification
  function getAuthHeaders() {
    const authStore = useAuthStore()
    
    if (process.client && !authStore.isAuthenticated) {
      authStore.initAuth()
      console.log('[useAdminData] After initAuth - token exists:', !!authStore.token)
    }
    
    if (!authStore.token) {
      console.error('[useAdminData] No token available!')
      throw new Error('Token d\'authentification manquant. Veuillez vous reconnecter.')
    }
    
    return {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${authStore.token}`
    }
  }

  async function fetchIncidents() {
    loadingIncidents.value = true
    errorIncidents.value = null
    
    try {
      const headers = getAuthHeaders()
      const config = useRuntimeConfig()
      const rawResponse: any = await $fetch(`${config.public.apiBase}/admin/incidents`, { headers })
      
      // Parse the response if it's a string
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
      
      if (response && response.success && response.data) {
        if (Array.isArray(response.data)) {
          incidents.value = response.data
        } else {
          // Fallback si ce n'est pas un tableau
          incidents.value = []
        }
      } else {
        incidents.value = []
      }
    } catch (error) {
      console.error('[useAdminData] ❌ Error fetching incidents:', error)
      errorIncidents.value = error
      incidents.value = []
    } finally {
      loadingIncidents.value = false
    }
  }

  async function fetchLogs() {
    loadingLogs.value = true
    errorLogs.value = null
    
    try {
      console.log('[useAdminData] Fetching logs...')
      
      const headers = getAuthHeaders()
      const config = useRuntimeConfig()
      const rawResponse: any = await $fetch(`${config.public.apiBase}/admin/logs`, { headers })
      
      // Parse the response if it's a string
      let response: any
      if (typeof rawResponse === 'string') {
        try {
          response = JSON.parse(rawResponse)
        } catch (parseError) {
          console.error('[useAdminData] Failed to parse logs response string:', parseError)
          response = rawResponse
        }
      } else {
        response = rawResponse
      }
      
      console.log('[useAdminData] Logs response:', response)
      
      if (response && response.success) {
        // Vérifier d'abord response.data
        if (Array.isArray(response.data)) {
          logs.value = response.data
          console.log('[useAdminData] ✅ Successfully updated logs with', logs.value.length, 'items')
        } 
        // Vérifier ensuite response.data.data (structure imbriquée)
        else if (response.data?.data && Array.isArray(response.data.data)) {
          logs.value = response.data.data
          console.log('[useAdminData] ✅ Successfully updated logs with nested data:', logs.value.length, 'items')
        } 
        else {
          logs.value = []
          console.warn('[useAdminData] ⚠️ No valid logs array found in response')
        }
      } else {
        console.warn('[useAdminData] ❌ Logs response not successful:', response)
        logs.value = []
      }
    } catch (error) {
      console.error('[useAdminData] ❌ Error fetching logs:', error)
      errorLogs.value = error
      logs.value = []
    } finally {
      loadingLogs.value = false
    }
  }

  async function fetchResidents() {
    loadingResidents.value = true
    errorResidents.value = null
    
    try {
      console.log('[useAdminData] Fetching residents...')
      
      const headers = getAuthHeaders()
      const config = useRuntimeConfig()
      const rawResponse: any = await $fetch(`${config.public.apiBase}/admin/persons`, { headers })
      
      // Parse the response if it's a string
      let response: any
      if (typeof rawResponse === 'string') {
        try {
          response = JSON.parse(rawResponse)
        } catch (parseError) {
          console.error('[useAdminData] Failed to parse residents response string:', parseError)
          response = rawResponse
        }
      } else {
        response = rawResponse
      }
      
      console.log('[useAdminData] Residents response:', response)
      
      if (response && response.success && response.persons) {
        // API retourne {success: true, persons: [...]}
        if (Array.isArray(response.persons)) {
          residents.value = response.persons
          console.log('[useAdminData] ✅ Successfully updated residents with', residents.value.length, 'items')
        } else {
          residents.value = []
          console.warn('[useAdminData] ⚠️ Persons is not an array for residents')
        }
      } else {
        console.warn('[useAdminData] ❌ Residents response not successful:', response)
        residents.value = []
      }
    } catch (error) {
      console.error('[useAdminData] ❌ Error fetching residents:', error)
      errorResidents.value = error
      residents.value = []
    } finally {
      loadingResidents.value = false
    }
  }

  async function fetchGuests() {
    loadingGuests.value = true
    errorGuests.value = null
    
    try {
      console.log('[useAdminData] Fetching guests...')
      
      const headers = getAuthHeaders()
      const config = useRuntimeConfig()
      const rawResponse: any = await $fetch(`${config.public.apiBase}/admin/guests`, { headers })
      
      // Parse the response if it's a string
      let response: any
      if (typeof rawResponse === 'string') {
        try {
          response = JSON.parse(rawResponse)
        } catch (parseError) {
          console.error('[useAdminData] Failed to parse guests response string:', parseError)
          response = rawResponse
        }
      } else {
        response = rawResponse
      }
      
      console.log('[useAdminData] Guests response:', response)
      
      if (response && response.success && response.data) {
        if (Array.isArray(response.data)) {
          guests.value = response.data
          console.log('[useAdminData] ✅ Successfully updated guests with', guests.value.length, 'items')
        } else {
          guests.value = []
          console.warn('[useAdminData] ⚠️ Data is not an array for guests')
        }
      } else {
        console.warn('[useAdminData] ❌ Guests response not successful:', response)
        guests.value = []
      }
    } catch (error) {
      console.error('[useAdminData] ❌ Error fetching guests:', error)
      errorGuests.value = error
      guests.value = []
    } finally {
      loadingGuests.value = false
    }
  }

  async function fetchStats() {
    loadingStats.value = true
    errorStats.value = null
    
    try {
      console.log('[useAdminData] Fetching stats...')
      
      const headers = getAuthHeaders()
      const config = useRuntimeConfig()
      const rawResponse: any = await $fetch(`${config.public.apiBase}/admin/stats`, { headers })
      
      // Parse the response if it's a string
      let response: any
      if (typeof rawResponse === 'string') {
        try {
          response = JSON.parse(rawResponse)
        } catch (parseError) {
          console.error('[useAdminData] Failed to parse stats response string:', parseError)
          response = rawResponse
        }
      } else {
        response = rawResponse
      }
      
      console.log('[useAdminData] Stats response:', response)
      
      // L'API stats retourne directement l'objet sans champ success
      if (response && typeof response === 'object') {
        // Vérifier si c'est un objet stats valide (doit avoir au moins users, groups, etc.)
        if (response.users || response.groups || response.messages) {
          stats.value = response
          console.log('[useAdminData] ✅ Successfully updated stats directly from response')
        } else {
          console.warn('[useAdminData] ⚠️ Invalid stats structure in response')
          stats.value = {}
        }
      } else {
        console.warn('[useAdminData] ❌ Stats response not an object:', response)
        stats.value = {}
      }
    } catch (error) {
      console.error('[useAdminData] ❌ Error fetching stats:', error)
      errorStats.value = error
      stats.value = {}
    } finally {
      loadingStats.value = false
    }
  }

  // Fonctions CRUD pour les personnes
  async function addPerson(data: any) {
    try {
      const headers = getAuthHeaders()
      const config = useRuntimeConfig()
      console.log('Ajout d\'une personne avec les données:', data)
      
      const response: any = await $fetch(`${config.public.apiBase}/admin/persons`, { 
        method: 'POST',
        headers: {
          ...headers,
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: data,
        responseType: 'json'
      })
      
      console.log('Réponse reçue du backend:', response)
      console.log('Type de la réponse:', typeof response)
      
      // Parse the response if it's a string
      let parsedResponse = response
      if (typeof response === 'string') {
        try {
          parsedResponse = JSON.parse(response)
          console.log('Réponse parsée:', parsedResponse)
        } catch (parseError) {
          console.error('Erreur de parsing JSON:', parseError)
          throw new Error('Réponse invalide du serveur')
        }
      }
      
      console.log('parsedResponse.success:', parsedResponse?.success)
      
      if (parsedResponse && parsedResponse.success === true) {
        console.log('Succès confirmé, rechargement de la liste...')
        // Recharger la liste
        await fetchResidents()
        return parsedResponse
      } else {
        console.error('Réponse inattendue du backend:', parsedResponse)
        throw new Error(parsedResponse?.message || 'Erreur lors de la création')
      }
    } catch (error: any) {
      console.error('[useAdminData] Error adding person:', error)
      console.error('Détails de l\'erreur:', error.data || error.response || error)
      throw error
    }
  }

  async function updatePersonData(personId: number, data: any) {
    try {
      const headers = getAuthHeaders()
      const config = useRuntimeConfig()
      const response: any = await $fetch(`${config.public.apiBase}/admin/persons/${personId}`, { 
        method: 'PUT',
        headers,
        body: data
      })
      
      if (response && response.success) {
        // Recharger la liste
        await fetchResidents()
        return response
      } else {
        throw new Error(response.message || 'Erreur lors de la mise à jour')
      }
    } catch (error) {
      console.error('[useAdminData] Error updating person:', error)
      throw error
    }
  }

  async function deletePersonData(personId: number) {
    try {
      const headers = getAuthHeaders()
      const config = useRuntimeConfig()
      const response: any = await $fetch(`${config.public.apiBase}/admin/persons/${personId}`, { 
        method: 'DELETE',
        headers
      })
      
      if (response && response.success) {
        // Recharger la liste
        await fetchResidents()
        return response
      } else {
        throw new Error(response.message || 'Erreur lors de la suppression')
      }
    } catch (error) {
      console.error('[useAdminData] Error deleting person:', error)
      throw error
    }
  }

  async function deleteAvatar(personId: number) {
    try {
      const headers = getAuthHeaders()
      const config = useRuntimeConfig()
      const response: any = await $fetch(`${config.public.apiBase}/admin/persons/${personId}/avatar`, { 
        method: 'DELETE',
        headers
      })
      
      if (response && response.success) {
        // Recharger la liste
        await fetchResidents()
        return response
      } else {
        throw new Error(response.message || 'Erreur lors de la suppression de l\'avatar')
      }
    } catch (error) {
      console.error('[useAdminData] Error deleting avatar:', error)
      throw error
    }
  }

  return {
    // Data
    incidents,
    logs,
    residents,
    guests,
    stats,
    
    // Loading states
    loadingIncidents,
    loadingLogs,
    loadingResidents,
    loadingGuests,
    loadingStats,
    
    // Error states
    errorIncidents,
    errorLogs,
    errorResidents,
    errorGuests,
    errorStats,
    
    // Functions
    fetchIncidents,
    fetchLogs,
    fetchResidents,
    fetchGuests,
    fetchStats,
    
    // CRUD functions
    addPerson,
    updatePersonData,
    deletePersonData,
    deleteAvatar
  }
}


