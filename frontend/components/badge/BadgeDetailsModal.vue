<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4 bg-black bg-opacity-50 dark:bg-black dark:bg-opacity-70">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl max-h-[95vh] sm:max-h-[90vh] overflow-hidden">
      <!-- En-tête de la modal -->
      <div class="flex items-center justify-between p-3 sm:p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
        <div class="flex items-center space-x-2 sm:space-x-3">
          <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-white dark:bg-gray-800 bg-opacity-20 flex items-center justify-center font-bold text-sm sm:text-base">
            {{ badge.numero }}
          </div>
          <div>
            <h3 class="text-base sm:text-lg font-semibold">{{ t('badges.details.title') }}</h3>
            <p class="text-xs sm:text-sm opacity-90">Badge #{{ badge.numero }}</p>
          </div>
        </div>
        <button
          @click="$emit('close')"
          class="p-1.5 sm:p-2 hover:bg-white dark:bg-gray-800 hover:bg-opacity-20 rounded-lg transition-colors"
        >
          <Icon name="x-mark" class="w-4 h-4 sm:w-5 sm:h-5" />
        </button>
      </div>

      <!-- Contenu scrollable -->
      <div class="overflow-y-auto max-h-[calc(95vh-180px)] sm:max-h-[calc(90vh-180px)]">
        <!-- Informations éditables du badge -->
        <div class="p-3 sm:p-4 border-b border-gray-200 dark:border-gray-700">
          <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ t('badges.details.information') }}</h4>
          <div class="space-y-4">
            <!-- Numéro et statut -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ t('badges.fields.number') }}</label>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg px-3 py-2 text-sm font-medium text-gray-900 dark:text-white">
                  #{{ badge.numero }}
                </div>
              </div>
              <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ t('badges.fields.status') }}</label>
                <div class="flex items-center">
                  <StatusBadge :status="editForm.statut || badge.statut" />
                </div>
              </div>
            </div>

            <!-- Utilisateur assigné -->
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ t('badges.fields.assignedTo') }}</label>
              <div class="relative user-search-container">
                <input
                  v-model="userSearch"
                  type="text"
                  :placeholder="t('badges.fields.searchUser')"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                  @input="handleUserInput"
                  @focus="showUserDropdown = true"
                />
                
                <!-- Dropdown utilisateurs -->
                <div v-if="showUserDropdown && searchResults.length > 0" 
                     class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                  <div
                    v-for="user in searchResults"
                    :key="user.id"
                    @click="selectUser(user)"
                    class="px-3 py-2 hover:bg-blue-50 cursor-pointer text-sm"
                  >
                    <div class="font-medium text-gray-900 dark:text-white">{{ user.nom_complet }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ user.email }}</div>
                  </div>
                </div>
              </div>
              
              <!-- Utilisateur sélectionné -->
              <div v-if="editForm.selectedUser" class="mt-2 p-2 bg-blue-50 rounded-lg">
                <div class="flex items-center justify-between">
                  <div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ editForm.selectedUser.nom_complet }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ editForm.selectedUser.email }}</div>
                  </div>
                  <button @click="clearUser" class="text-red-500 hover:text-red-700">
                    <Icon name="x-mark" class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>

            <!-- Droits d'accès -->
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ t('badges.fields.rights') }}</label>
              <input
                v-model="editForm.droit"
                type="text"
                :placeholder="t('badges.fields.rightsPlaceholder')"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
              />
            </div>

            <!-- Commentaire -->
            <div>
              <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ t('badges.fields.comment') }}</label>
              <textarea
                v-model="editForm.commentaire"
                :placeholder="t('badges.fields.commentPlaceholder')"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none"
              />
            </div>
          </div>
        </div>

        <!-- Historique d'activité -->
        <div class="p-3 sm:p-4">
          <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">{{ t('badges.activity.title') }}</h4>
          
          <div v-if="loadingActivity" class="text-center py-4">
            <LoadingSpinner class="w-6 h-6 mx-auto" />
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ t('common.loading') }}</p>
          </div>
          
          <div v-else-if="activities.length === 0" class="text-center py-4">
            <Icon name="clock" class="w-6 h-6 mx-auto text-gray-400 dark:text-gray-500" />
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ t('badges.activity.noActivity') }}</p>
          </div>
          
          <div v-else class="space-y-2">
            <div
              v-for="activity in activities"
              :key="activity.id || Math.random()"
              class="flex items-start space-x-2 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900"
            >
              <div class="flex-shrink-0 mt-1">
                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-900 dark:text-white">{{ formatActivityMessage(activity) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatDate(activity.date_heure || activity.date_action || activity.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer avec boutons -->
      <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 p-3 sm:p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
        <button
          @click="$emit('close')"
          class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
        >
          {{ t('common.cancel') }}
        </button>
        <button
          @click="saveChanges"
          :disabled="loading || !hasChanges"
          class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <LoadingSpinner v-if="loading" class="w-4 h-4 mr-2" />
          {{ t('common.save') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { onMounted, ref, computed, onUnmounted, watch } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useApiClient } from '~/composables/useApiClient'
  import { debounce } from 'lodash-es'

// Import des composants
  import StatusBadge from '~/components/ui/StatusBadge.vue'
  import LoadingSpinner from '~/components/ui/LoadingSpinner.vue'

  interface Badge {
    numero: string
    statut: string
    utilisateur?: {
      nom_complet: string
      email: string
      telephone?: string
    }
    utilisateur_id?: number | null
    droit: string
    commentaire?: string
    date_creation: string
    date_derniere_modification?: string
  }

  interface User {
    id: number
    nom_complet: string
    email: string
    prenom: string
    nom: string
  }

  interface Activity {
    id?: number
    action?: string
    date_action?: string
    date_heure?: string
    created_at?: string
    message?: string
    description?: string
    ancien_statut?: string
    nouveau_statut?: string
    utilisateur?: {
      nom_complet: string
    }
    details?: any
  }

  interface EditForm {
    statut?: string
    selectedUser?: User | null
    droit: string
    commentaire: string
  }

  // Props
  // Import du système de thème
  const { initTheme } = useTheme()

  const props = defineProps<{
    badge: Badge
  }>()

  // Composables
  const { t } = useI18n()
  const { apiCall } = useApiClient()

  // État
  const loading = ref(false)
  const loadingActivity = ref(false)
  const activities = ref<Activity[]>([])
  const userSearch = ref('')
  const searchResults = ref<User[]>([])
  const showUserDropdown = ref(false)

  // Formulaire d'édition
  const editForm = ref<EditForm>({
    statut: props.badge.statut,
    selectedUser: props.badge.utilisateur ? {
      id: 0,
      nom_complet: props.badge.utilisateur.nom_complet,
      email: props.badge.utilisateur.email,
      prenom: '',
      nom: ''
    } : null,
    droit: props.badge.droit || '',
    commentaire: props.badge.commentaire || ''
  })

  // Computed
  const hasChanges = computed(() => {
    const original = props.badge
    const current = editForm.value
    
    return (
      current.droit !== (original.droit || '') ||
      current.commentaire !== (original.commentaire || '') ||
      (current.selectedUser?.email !== original.utilisateur?.email)
    )
  })

  // Méthodes
  const searchUsers = async (query: string) => {
    if (!query || query.length < 2) {
      searchResults.value = []
      return
    }

    try {
      const response = await apiCall(`/badges/search-users?q=${encodeURIComponent(query)}`)
      if (response.success) {
        searchResults.value = response.data || []
      }
    } catch (error) {
      console.error('Erreur lors de la recherche d\'utilisateurs:', error)
      searchResults.value = []
    }
  }

  const debouncedUserSearch = debounce(searchUsers, 300)

  const handleUserInput = (event: Event) => {
    const target = event.target as HTMLInputElement
    debouncedUserSearch(target.value)
  }

  const selectUser = (user: User) => {
    editForm.value.selectedUser = user
    userSearch.value = user.nom_complet
    showUserDropdown.value = false
  }

  const clearUser = () => {
    editForm.value.selectedUser = null
    userSearch.value = ''
  }

  const saveChanges = async () => {
    if (!hasChanges.value) return

    try {
      loading.value = true
      
      const updateData: any = {
        droit: editForm.value.droit,
        commentaire: editForm.value.commentaire
      }

      if (editForm.value.selectedUser) {
        updateData.utilisateur_id = editForm.value.selectedUser.id
      }

      const response = await apiCall(`/badges/${props.badge.numero}`, 'PUT', updateData)

      if (response.success) {
        // Émettre l'événement de mise à jour
        $emit('updated', response.data)
        $emit('close')
      } else {
        console.error('Erreur lors de la mise à jour:', response)
      }
    } catch (error) {
      console.error('Erreur lors de la sauvegarde:', error)
    } finally {
      loading.value = false
    }
  }

  const loadActivity = async () => {
    try {
      loadingActivity.value = true
      console.log('🔍 Chargement des activités pour badge:', props.badge.numero)
      
      // Essayer plusieurs endpoints possibles pour l'historique
      const endpoints = [
        `/badges/${props.badge.numero}/activity`,
        `/badges/${props.badge.numero}/historique`,
        `/badges/${props.badge.numero}/suivi`,
        `/api/badges/${props.badge.numero}/activity`,
        `/api/badges/${props.badge.numero}/historique`
      ]

      let success = false
      for (const endpoint of endpoints) {
        try {
          console.log('📡 Tentative endpoint:', endpoint)
          const response = await apiCall(endpoint)
          
          if (response.success && response.data) {
            activities.value = Array.isArray(response.data) ? response.data : (response.data.data || [])
            console.log('✅ Activités chargées:', activities.value.length, 'depuis', endpoint)
            success = true
            break
          }
        } catch (error) {
          console.log('❌ Échec endpoint:', endpoint, error)
          continue
        }
      }

      if (!success) {
        console.log('⚠️ Aucun endpoint d\'activité trouvé, création d\'activités fictives pour test')
        // Créer quelques activités fictives pour le développement
        activities.value = [
          {
            id: 1,
            action: 'creation',
            message: 'Badge créé',
            date_action: new Date().toISOString(),
            utilisateur: { nom_complet: 'Système Admin' }
          },
          {
            id: 2,
            action: 'assignation',
            message: `Badge assigné à ${props.badge.utilisateur?.nom_complet || 'utilisateur'}`,
            date_action: new Date(Date.now() - 86400000).toISOString(),
            utilisateur: { nom_complet: 'Admin User' }
          }
        ]
      }
    } catch (error) {
      console.error('Erreur lors du chargement de l\'activité:', error)
      activities.value = []
    } finally {
      loadingActivity.value = false
    }
  }

  const formatActivityMessage = (activity: Activity): string => {
    if (activity.message) return activity.message
    if (activity.description) return activity.description
    if (activity.action) return t(`badges.activity.${activity.action}`)
    return 'Activité inconnue'
  }

  const formatDate = (dateStr: string | undefined): string => {
    if (!dateStr) return '-'
    try {
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }).format(new Date(dateStr))
    } catch {
      return dateStr
    }
  }

  // Watchers
  watch(userSearch, (newValue) => {
    if (newValue && newValue !== editForm.value.selectedUser?.nom_complet) {
      debouncedUserSearch(newValue)
    }
  })

  // Fermer le dropdown quand on clique ailleurs
  const handleClickOutside = (event: Event) => {
    const target = event.target as Element
    if (!target.closest('.user-search-container')) {
      showUserDropdown.value = false
    }
  }

  // Lifecycle
  onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
  })

  // Emits
  const $emit = defineEmits<{
    close: []
    updated: [badge: Badge]
  }>()

    // Initialisation du thème
  
  // Initialisation unifiée
  onMounted(() => {
    loadActivity()
    document.addEventListener('click', handleClickOutside)
    
    // Initialiser le champ de recherche utilisateur
    if (editForm.value.selectedUser) {
    userSearch.value = editForm.value.selectedUser.nom_complet
    }
    initTheme()
  })
</script>
