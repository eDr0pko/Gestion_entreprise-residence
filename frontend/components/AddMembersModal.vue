<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show" class="fixed inset-0 z-[60] overflow-y-auto">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-black dark:bg-opacity-70 transition-opacity" @click="closeModal"></div>
        
        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ t('components.addMembersModal.title') }}</h3>
              <button 
                @click="closeModal"
                class="p-1 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 rounded-lg transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="addMembers" class="flex flex-col flex-1 min-h-0">
              <!-- Sélection des nouveaux membres -->
              <div class="flex-1 min-h-0 flex flex-col">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ t('components.addMembersModal.selectMembers') }} – {{ selectedUsers.length }} {{ t('components.addMembersModal.selected', { count: selectedUsers.length }) }}
                  </label>
                  
                  <!-- Barre de recherche -->
                  <div class="relative mb-3">
                    <input
                      v-model="searchQuery"
                      type="text"
                      :placeholder="t('components.addMembersModal.searchPlaceholder')"
                      class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-1 focus:ring-[#0097b2] focus:border-[#0097b2] transition-colors bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                    />
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                  </div>

                  <!-- Utilisateurs sélectionnés -->
                  <div v-if="selectedUsers.length > 0" class="mb-3">
                    <div class="flex flex-wrap gap-2">
                      <div
                        v-for="user in selectedUsers"
                        :key="user.email"
                        class="inline-flex items-center px-2 py-1 bg-[#0097b2] dark:bg-[#007a94] text-white text-xs rounded-full"
                      >
                        <span>{{ user.nom_complet }}</span>
                        <button
                          type="button"
                          @click="removeUser(user)"
                          class="ml-1 p-0.5 hover:bg-[#007a94] dark:hover:bg-[#006880] rounded-full transition-colors"
                        >
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Liste des utilisateurs disponibles -->
                <div class="flex-1 overflow-y-auto">
                  <div v-if="loadingUsers" class="p-4 text-center text-gray-500 dark:text-gray-400">
                    <svg class="w-5 h-5 animate-spin mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm">{{ t('components.addMembersModal.loading') }}</span>
                  </div>

                  <div v-else-if="error" class="p-4 text-center text-red-500 dark:text-red-400">
                    <p class="text-sm">{{ error }}</p>
                    <button @click="loadAvailableUsers" class="text-xs text-[#0097b2] hover:text-[#007a94] dark:text-[#00b3d1] dark:hover:text-[#0097b2] mt-2">
                      {{ t('components.addMembersModal.retry') }}
                    </button>
                  </div>

                  <div v-else class="space-y-1 p-2">
                    <div
                      v-for="user in filteredAvailableUsers"
                      :key="user.email"
                      @click="toggleUser(user)"
                      class="flex items-center p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                      :class="{ 'bg-blue-50 dark:bg-blue-900/30': isSelected(user) }"
                    >
                      <!-- Checkbox -->
                      <div class="flex-shrink-0 mr-3">
                        <div class="w-4 h-4 border-2 border-gray-300 dark:border-gray-600 rounded flex items-center justify-center transition-colors"
                             :class="isSelected(user) ? 'bg-[#0097b2] border-[#0097b2] dark:bg-[#007a94] dark:border-[#007a94]' : ''">
                          <svg v-if="isSelected(user)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                          </svg>
                        </div>
                      </div>

                      <!-- Avatar -->
                      <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                      </div>

                      <!-- Informations utilisateur -->
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ user.nom_complet }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ t('components.addMembersModal.role', { role: user.role }) }}</p>
                      </div>
                    </div>

                    <div v-if="filteredAvailableUsers.length === 0" class="p-4 text-center text-gray-500 dark:text-gray-400">
                      <p class="text-sm">
                        {{
                          availableUsers.length === 0
                            ? t('components.addMembersModal.allUsersAreMembers')
                            : t('components.addMembersModal.noUserFound')
                        }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button
                  type="button"
                  @click="closeModal"
                  class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors"
                  :disabled="adding"
                >
                  {{ t('components.addMembersModal.cancel') }}
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 bg-[#0097b2] dark:bg-[#007a94] text-white rounded-lg hover:bg-[#007a94] dark:hover:bg-[#006880] disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
                  :disabled="selectedUsers.length === 0 || adding"
                >
                  <svg v-if="adding" class="w-4 h-4 animate-spin mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ adding ? t('components.addMembersModal.adding') : t('components.addMembersModal.addMembers', { count: selectedUsers.length }) }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
  import { onMounted } from 'vue'
  import { useI18n } from 'vue-i18n'

// Import du système de thème
  const { initTheme } = useTheme()

  const { t } = useI18n()

  interface User {
    email: string
    nom_complet: string
    nom: string
    prenom: string
    role: string
  }

  interface Member {
    email: string
    nom_complet: string
    nom: string
    prenom: string
    role: string
    is_current_user: boolean
    date_adhesion: string
  }

  interface Conversation {
    id_groupe_message: number
    nom_groupe: string
  }

  interface Props {
    show: boolean
    conversation: Conversation | null
    existingMembers: Member[]
  }

  const props = defineProps<Props>()

  const emit = defineEmits<{
    close: []
    membersAdded: [members: Member[]]
  }>()

  const authStore = useAuthStore()
  const config = useRuntimeConfig()

  // État réactif
  const searchQuery = ref('')
  const selectedUsers = ref<User[]>([])
  const availableUsers = ref<User[]>([])
  const loadingUsers = ref(false)
  const adding = ref(false)
  const error = ref('')

  // Utilisateurs filtrés (qui ne sont pas déjà membres)
  const filteredAvailableUsers = computed(() => {
    let filtered = availableUsers.value
    
    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase()
      filtered = filtered.filter(user => 
        user.nom_complet.toLowerCase().includes(query) ||
        user.email.toLowerCase().includes(query) ||
        user.role.toLowerCase().includes(query)
      )
    }
    
    return filtered
  })

  // Vérifier si un utilisateur est sélectionné
  const isSelected = (user: User) => {
    return selectedUsers.value.some(selected => selected.email === user.email)
  }

  // Ajouter/retirer un utilisateur
  const toggleUser = (user: User) => {
    if (isSelected(user)) {
      removeUser(user)
    } else {
      selectedUsers.value.push(user)
    }
  }

  // Retirer un utilisateur
  const removeUser = (user: User) => {
    const index = selectedUsers.value.findIndex(selected => selected.email === user.email)
    if (index !== -1) {
      selectedUsers.value.splice(index, 1)
    }
  }

  // Charger les utilisateurs disponibles (qui ne sont pas déjà membres)
  const loadAvailableUsers = async () => {
    if (loadingUsers.value) return
    try {
      loadingUsers.value = true
      error.value = ''
      console.debug('[AddMembersModal] Loading available users')

      // Restaurer token si nécessaire
      let workingToken = authStore.token as unknown as string | null
      if ((!workingToken || typeof workingToken !== 'string') && process.client) {
        const stored = localStorage.getItem('auth_token')
        if (stored) {
          workingToken = stored
          // @ts-ignore
          authStore.token = stored
          console.debug('[AddMembersModal] Token restauré depuis localStorage')
        }
      }
      if (!workingToken || typeof workingToken !== 'string') {
        error.value = 'Token manquant – veuillez vous reconnecter.'
        console.warn('[AddMembersModal] Aucun token disponible')
        return
      }
      const tokenPreview = workingToken.length > 8 ? workingToken.substring(0,8)+'...' : '[court]'
      console.debug('[AddMembersModal] Utilisation token:', tokenPreview)

      const url = `${config.public.apiBase}/conversations/users`
      let response: any = await $fetch<any>(url, {
        headers: {
          'Authorization': `Bearer ${workingToken}`,
          'Accept': 'application/json'
        }
      }).catch((e: any) => { console.error('[AddMembersModal] Network/parse error', e); throw e })

      if (typeof response === 'string') {
        try { response = JSON.parse(response) } catch { /* ignore parse error */ }
      }
      console.debug('[AddMembersModal] Raw response keys:', response ? Object.keys(response) : [])
      const usersArray = Array.isArray(response?.users) ? response.users : []
      const successFlag = (typeof response?.success === 'boolean') ? response.success : usersArray.length > 0
      if (!successFlag) {
        error.value = response?.error || response?.message || 'Erreur lors du chargement des utilisateurs'
        return
      }
      const existingEmails = props.existingMembers.map(m => m.email)
      availableUsers.value = (usersArray as User[]).filter((u: User) => !existingEmails.includes(u.email))
      console.debug('[AddMembersModal] Users loaded (filtered):', availableUsers.value.length)
    } catch (err: any) {
      console.error('Erreur lors du chargement des utilisateurs:', err)
      error.value = err?.data?.message || err?.message || 'Impossible de charger les utilisateurs'
    } finally {
      loadingUsers.value = false
    }
  }

  // Ajouter les membres sélectionnés
  const addMembers = async () => {
    if (!props.conversation || selectedUsers.value.length === 0) return
    
    try {
      adding.value = true
      console.debug('[AddMembersModal] Adding members', selectedUsers.value.map(u => u.email))
      const url = `${config.public.apiBase}/conversations/${props.conversation.id_groupe_message}/members`
      let response: any = await $fetch<any>(url, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: {
          members: selectedUsers.value.map(user => user.email)
        }
      }).catch((e: any) => { console.error('[AddMembersModal] Network/parse error (add)', e); throw e })

      // Permettre le backend proxy de renvoyer une chaîne brute
      if (typeof response === 'string') {
        try { response = JSON.parse(response); console.debug('[AddMembersModal] Parsed string response for addMembers') } catch {/* ignore */}
      }
      console.debug('[AddMembersModal] Raw add members response keys:', Object.keys(response || {}))

      const hasMembersArray = response && Array.isArray(response.members)
      const successFlag = (typeof response?.success === 'boolean') ? response.success : hasMembersArray

      if (!successFlag) {
        // Essayer de détecter un message d'erreur détaillé
        const serverMessage = response?.error || response?.message
        throw new Error(serverMessage || 'Erreur lors de l\'ajout des membres')
      }

      const newMembers: Member[] = hasMembersArray ? response.members : []
      console.debug('[AddMembersModal] Members added count:', newMembers.length)

      if (newMembers.length > 0) {
        emit('membersAdded', newMembers)
      } else {
        console.debug('[AddMembersModal] No new members actually added (likely already present)')
      }
      closeModal()
    } catch (err: any) {
      console.error('Erreur lors de l\'ajout des membres:', err)
      error.value = err.data?.message || err.message || 'Impossible d\'ajouter les membres'
    } finally {
      adding.value = false
    }
  }

  // Fermer la modal
  const closeModal = () => {
    // Reset des données
    searchQuery.value = ''
    selectedUsers.value = []
    error.value = ''
    
    emit('close')
  }

  // Charger les utilisateurs à l'ouverture
  watch(() => props.show, (newShow) => {
    if (newShow) {
      loadAvailableUsers()
    }
  })

  // Initialisation du thème
  onMounted(() => {
    initTheme()
  })
</script>

<style scoped>
  .modal-enter-active, .modal-leave-active {
    transition: all 0.3s ease;
  }

  .modal-enter-from, .modal-leave-to {
    opacity: 0;
    transform: scale(0.9);
  }
</style>


