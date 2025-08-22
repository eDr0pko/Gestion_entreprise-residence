<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-black dark:bg-opacity-70 transition-opacity" @click="closeModal"></div>
        
        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ t('createConversationModal.title') }}</h3>
              <button 
                @click="closeModal"
                class="p-1 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300 rounded-lg transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="createConversation" class="flex flex-col flex-1 min-h-0">
              <!-- Nom de la conversation -->
              <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                <label for="nom_groupe" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ t('createConversationModal.nameLabel') }}
                </label>
                <input
                  id="nom_groupe"
                  v-model="nomGroupe"
                  type="text"
                  :placeholder="t('createConversationModal.namePlaceholder')"
                  class="w-full px-3 py-2 border border-gray-200 dark:border-gray-700 dark:border-gray-600 rounded-lg focus:ring-1 focus:ring-[#0097b2] focus:border-[#0097b2] transition-colors bg-white dark:bg-gray-800 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                  required
                  maxlength="100"
                />
              </div>

              <!-- Sélection des participants -->
              <div class="flex-1 min-h-0 flex flex-col">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ t('createConversationModal.participantsLabel', { count: selectedUsers.length }) }}
                  </label>
                  
                  <!-- Barre de recherche -->
                  <div class="relative mb-3">
                    <input
                      v-model="searchQuery"
                      type="text"
                      :placeholder="t('createConversationModal.searchPlaceholder')"
                      class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-1 focus:ring-[#0097b2] focus:border-[#0097b2] transition-colors"
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
                          class="ml-1 p-0.5 hover:bg-[#007a94] rounded-full transition-colors"
                        >
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Liste des utilisateurs -->
                <div class="flex-1 overflow-y-auto">
                  <div v-if="loadingUsers" class="p-4 text-center text-gray-500 dark:text-gray-400 dark:text-gray-500">
                    <svg class="w-5 h-5 animate-spin mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm">{{ t('createConversationModal.loadingUsers') }}</span>
                  </div>

                  <div v-else-if="error" class="p-4 text-center text-red-500">
                    <p class="text-sm">{{ error }}</p>
                    <button @click="loadUsers" class="text-xs text-[#0097b2] dark:text-[#00b3d1] hover:text-[#007a94] mt-2">
                      {{ t('createConversationModal.retry') }}
                    </button>
                  </div>

                  <div v-else class="space-y-1 p-2">
                    <div
                      v-for="user in filteredUsers"
                      :key="user.email"
                      @click="toggleUser(user)"
                      class="flex items-center p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900 cursor-pointer transition-colors"
                      :class="{ 'bg-blue-50': isSelected(user) }"
                    >
                      <!-- Checkbox -->
                      <div class="flex-shrink-0 mr-3">
                        <div class="w-4 h-4 border-2 border-gray-300 dark:border-gray-600 rounded flex items-center justify-center transition-colors"
                             :class="isSelected(user) ? 'bg-[#0097b2] dark:bg-[#007a94] border-[#0097b2]' : ''">
                          <svg v-if="isSelected(user)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                          </svg>
                        </div>
                      </div>

                      <!-- Avatar -->
                      <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                      </div>

                      <!-- Informations utilisateur -->
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ user.nom_complet }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500 truncate">{{ user.role }}</p>
                      </div>
                    </div>

                    <div v-if="filteredUsers.length === 0" class="p-4 text-center text-gray-500 dark:text-gray-400 dark:text-gray-500">
                      <p class="text-sm">{{ t('createConversationModal.noUser') }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button
                  type="button"
                  @click="closeModal"
                  class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:bg-gray-600 rounded-lg transition-colors"
                  :disabled="creating"
                >
                  {{ t('createConversationModal.cancel') }}
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 bg-[#0097b2] dark:bg-[#007a94] text-white rounded-lg hover:bg-[#007a94] disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
                  :disabled="!nomGroupe.trim() || selectedUsers.length === 0 || creating"
                >
                  <svg v-if="creating" class="w-4 h-4 animate-spin mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ creating ? t('createConversationModal.creating') : t('createConversationModal.create') }}
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
  import { onMounted, ref, computed, watch } from 'vue'
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

  interface Props {
    show: boolean
  }

  const props = defineProps<Props>()

  const emit = defineEmits<{
    close: []
    created: [conversation: any]
  }>()

  const authStore = useAuthStore()
  const config = useRuntimeConfig()

  const nomGroupe = ref('')
  const searchQuery = ref('')
  const selectedUsers = ref<User[]>([])
  const users = ref<User[]>([])
  const loadingUsers = ref(false)
  const creating = ref(false)
  const error = ref('')

  const filteredUsers = computed(() => {
    if (!searchQuery.value) return users.value
    const query = searchQuery.value.toLowerCase()
    return users.value.filter(user => 
      user.nom_complet.toLowerCase().includes(query) ||
      user.email.toLowerCase().includes(query) ||
      user.role.toLowerCase().includes(query)
    )
  })

  const isSelected = (user: User) => {
    return selectedUsers.value.some(selected => selected.email === user.email)
  }

  const toggleUser = (user: User) => {
    if (isSelected(user)) {
      removeUser(user)
    } else {
      selectedUsers.value.push(user)
    }
  }

  const removeUser = (user: User) => {
    const index = selectedUsers.value.findIndex(selected => selected.email === user.email)
    if (index !== -1) {
      selectedUsers.value.splice(index, 1)
    }
  }

  const loadUsers = async () => {
    // Empêcher les appels multiples en parallèle
    if (loadingUsers.value) return
    try {
      loadingUsers.value = true
      error.value = ''

      // Vérification / récupération du token (race condition potentielle au montage)
      let workingToken = authStore.token as unknown as string | null
      if ((!workingToken || typeof workingToken !== 'string') && process.client) {
        const stored = localStorage.getItem('auth_token')
        if (stored) {
          workingToken = stored
          // Ne pas forcer le type sur le store (store JS) – assignment toléré
          // @ts-ignore
          authStore.token = stored
          console.debug('[CreateConversationModal] Token restauré depuis localStorage')
        }
      }

      if (!workingToken || typeof workingToken !== 'string') {
        console.warn('[CreateConversationModal] Aucun token disponible avant requête utilisateurs')
        error.value = 'Token manquant – veuillez vous reconnecter.'
        return
      }

      const tokenPreview = workingToken.length > 8 ? workingToken.substring(0,8) + '...' : '[court]'
      console.debug('[CreateConversationModal] Chargement des utilisateurs avec token (tronqué):', tokenPreview)
      const response = await $fetch<any>(`${config.public.apiBase}/conversations/users`, {
        headers: {
          'Authorization': `Bearer ${workingToken}`,
          'Accept': 'application/json'
        }
      }).catch((fetchErr: any) => {
        console.error('[CreateConversationModal] Erreur réseau/fetch brute:', fetchErr)
        throw fetchErr
      })

      console.debug('[CreateConversationModal] Réponse brute /conversations/users:', response)

      // Normalisation (si string avec espaces / BOM)
      let parsed: any = response
      if (typeof response === 'string') {
        const trimmed = response.trim()
        try {
          parsed = JSON.parse(trimmed)
          console.debug('[CreateConversationModal] Réponse parsée depuis string')
        } catch (e) {
          console.warn('[CreateConversationModal] Impossible de parser la réponse texte en JSON, utilisation brute')
        }
      }

      const success = !!parsed?.success
      const usersPayload: User[] | undefined = parsed?.users
      if (success && Array.isArray(usersPayload)) {
        users.value = usersPayload
        console.debug(`[CreateConversationModal] ${users.value.length} utilisateurs chargés`)
      } else {
        const apiError = (parsed && (parsed.error || parsed.message)) || 'Réponse inattendue du serveur'
        throw new Error(apiError)
      }
    } catch (err: any) {
      console.error('Erreur lors du chargement des utilisateurs:', err)
      // Conserver le message original pour faciliter le debug
      const msg = err?.data?.message || err?.message || 'Impossible de charger les utilisateurs'
      error.value = msg
    } finally {
      loadingUsers.value = false
    }
  }

  const createConversation = async () => {
    try {
      creating.value = true
      error.value = ''
      const payload = {
        nom_groupe: nomGroupe.value.trim(),
        participants: selectedUsers.value.map(user => user.email)
      }
      console.debug('[CreateConversationModal] Envoi création conversation payload:', JSON.stringify(payload))
      const rawResponse = await $fetch<any>(`${config.public.apiBase}/conversations`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: payload
      })
      console.debug('[CreateConversationModal] Réponse création conversation brute:', rawResponse)

      // Normalisation similaire à loadUsers (gestion des retours avec espaces / lignes vides)
      let response = rawResponse
      if (typeof rawResponse === 'string') {
        const trimmed = rawResponse.trim()
        try {
          response = JSON.parse(trimmed)
          console.debug('[CreateConversationModal] Réponse création conversation parsée depuis string (taille initiale:', rawResponse.length, ')')
        } catch (e) {
          console.warn('[CreateConversationModal] Impossible de parser la réponse création conversation texte en JSON, utilisation brute')
        }
      }

      if (response && response.success && response.conversation) {
        emit('created', response.conversation)
        closeModal()
      } else {
        const apiError = (response && (response.error || response.message)) || 'Erreur lors de la création de la conversation'
        throw new Error(apiError)
      }
    } catch (err: any) {
      console.error('Erreur lors de la création de la conversation:', err)
      // Afficher erreurs de validation si présentes
      if (err?.data?.errors) {
        const flat = Object.entries(err.data.errors).map(([k,v]: any) => `${k}: ${(v as any[]).join(', ')}`).join(' | ')
        error.value = flat
      } else {
        error.value = err?.data?.message || err.message || 'Impossible de créer la conversation'
      }
    } finally {
      creating.value = false
    }
  }

  const closeModal = () => {
    nomGroupe.value = ''
    searchQuery.value = ''
    selectedUsers.value = []
    error.value = ''
    emit('close')
  }

  watch(() => props.show, (newShow) => {
    if (newShow) {
      loadUsers()
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


