<template>
  <div>
    <NavBar />
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 pt-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Gestion des Invités</h1>
          <p class="text-gray-600 dark:text-gray-400">Gérer les invités de la résidence</p>
        </div>

        <!-- Statistiques des invités -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Invités Actifs</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ activeGuests.length }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Invités Inactifs</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ inactiveGuests.length }}</p>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Invités</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ guests.length }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Liste des invités -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
              <h2 class="text-lg font-medium text-gray-900 dark:text-white">Liste des Invités</h2>
              <button 
                @click="refreshGuests"
                :disabled="loading"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
              >
                <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ loading ? 'Chargement...' : 'Actualiser' }}
              </button>
            </div>
          </div>
          
          <div v-if="loading" class="p-6 text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Chargement des invités...</p>
          </div>

          <div v-else-if="error" class="p-6 text-center text-red-600">
            <p>Erreur: {{ error }}</p>
          </div>

          <div v-else-if="guests.length === 0" class="p-6 text-center text-gray-500 dark:text-gray-400">
            <p>Aucun invité trouvé</p>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Email
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Nom Complet
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Téléphone
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Statut
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                <tr v-for="guest in guests" :key="guest.email">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ guest.email }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ guest.prenom }} {{ guest.nom }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                    {{ guest.numero_telephone || 'Non renseigné' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="guest.invite?.actif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                      {{ guest.invite?.actif ? 'Actif' : 'Inactif' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button 
                      v-if="guest.invite?.actif"
                      @click="deactivateGuest(guest.email)"
                      :disabled="processing"
                      class="text-red-600 hover:text-red-900 disabled:opacity-50"
                    >
                      Désactiver
                    </button>
                    <span v-else class="text-gray-400 dark:text-gray-500">Inactif</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, onMounted } from 'vue'
  import { useAuthStore } from '~/stores/auth'
  import { useRuntimeConfig } from '#app'

// Import du système de thème
  const { initTheme } = useTheme()

  // Protection de page admin
  definePageMeta({
    middleware: ['auth']
  })

  const authStore = useAuthStore()
  const config = useRuntimeConfig()

  // État
  const guests = ref([])
  const loading = ref(false)
  const processing = ref(false)
  const error = ref(null)

  // Invités actifs/inactifs
  const activeGuests = computed(() => guests.value.filter(guest => guest.invite?.actif))
  const inactiveGuests = computed(() => guests.value.filter(guest => !guest.invite?.actif))

  // Récupérer la liste des invités
  const fetchGuests = async () => {
    try {
      loading.value = true
      error.value = null
      
      const response = await $fetch(`${config.public.apiBase}/guests`, {
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': 'application/json',
        }
      })
      
      if (response.success) {
        guests.value = response.data
      } else {
        throw new Error(response.message || 'Erreur lors du chargement des invités')
      }
    } catch (err) {
      console.error('Erreur lors du chargement des invités:', err)
      error.value = err.message || 'Erreur lors du chargement des invités'
    } finally {
      loading.value = false
    }
  }

  // Désactiver un invité
  const deactivateGuest = async (email) => {
    if (!confirm('Êtes-vous sûr de vouloir désactiver cet invité ?')) {
      return
    }
    
    try {
      processing.value = true
      
      const response = await $fetch(`${config.public.apiBase}/guests/${email}/deactivate`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': 'application/json',
        }
      })
      
      if (response.success) {
        // Mettre à jour la liste locale
        const guestIndex = guests.value.findIndex(guest => guest.email === email)
        if (guestIndex !== -1) {
          guests.value[guestIndex].invite.actif = false
        }
      } else {
        throw new Error(response.message || 'Erreur lors de la désactivation')
      }
    } catch (err) {
      console.error('Erreur lors de la désactivation:', err)
      alert('Erreur lors de la désactivation: ' + (err.message || 'Erreur inconnue'))
    } finally {
      processing.value = false
    }
  }

  // Actualiser la liste
  const refreshGuests = () => {
    fetchGuests()
  }

  // Vérifier que l'utilisateur est admin et initialiser le thème
  onMounted(async () => {
    initTheme()
    await authStore.checkAuth()
    
    if (!authStore.isAuthenticated) {
      await navigateTo('/login')
      return
    }
    
    if (authStore.userRole !== 'admin') {
      await navigateTo('/principale')
      return
    }
    
    fetchGuests()
  })
</script>


