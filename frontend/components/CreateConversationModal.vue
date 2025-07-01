<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
        
        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900">Nouvelle conversation</h3>
              <button 
                @click="closeModal"
                class="p-1 text-gray-400 hover:text-gray-600 rounded-lg transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="createConversation" class="flex flex-col flex-1 min-h-0">
              <!-- Nom de la conversation -->
              <div class="p-4 border-b border-gray-100">
                <label for="nom_groupe" class="block text-sm font-medium text-gray-700 mb-2">
                  Nom de la conversation
                </label>
                <input
                  id="nom_groupe"
                  v-model="nomGroupe"
                  type="text"
                  placeholder="Ex: Équipe projet, Voisins étage 2..."
                  class="w-full px-3 py-2 border border-gray-200 rounded-lg focus:ring-1 focus:ring-[#0097b2] focus:border-[#0097b2] transition-colors"
                  required
                  maxlength="100"
                />
              </div>

              <!-- Sélection des participants -->
              <div class="flex-1 min-h-0 flex flex-col">
                <div class="p-4 border-b border-gray-100">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Participants ({{ selectedUsers.length }} sélectionné{{ selectedUsers.length > 1 ? 's' : '' }})
                  </label>
                  
                  <!-- Barre de recherche -->
                  <div class="relative mb-3">
                    <input
                      v-model="searchQuery"
                      type="text"
                      placeholder="Rechercher un utilisateur..."
                      class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-1 focus:ring-[#0097b2] focus:border-[#0097b2] transition-colors"
                    />
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                  </div>

                  <!-- Utilisateurs sélectionnés -->
                  <div v-if="selectedUsers.length > 0" class="mb-3">
                    <div class="flex flex-wrap gap-2">
                      <div
                        v-for="user in selectedUsers"
                        :key="user.email"
                        class="inline-flex items-center px-2 py-1 bg-[#0097b2] text-white text-xs rounded-full"
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
                  <div v-if="loadingUsers" class="p-4 text-center text-gray-500">
                    <svg class="w-5 h-5 animate-spin mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm">Chargement...</span>
                  </div>

                  <div v-else-if="error" class="p-4 text-center text-red-500">
                    <p class="text-sm">{{ error }}</p>
                    <button @click="loadUsers" class="text-xs text-[#0097b2] hover:text-[#007a94] mt-2">
                      Réessayer
                    </button>
                  </div>

                  <div v-else class="space-y-1 p-2">
                    <div
                      v-for="user in filteredUsers"
                      :key="user.email"
                      @click="toggleUser(user)"
                      class="flex items-center p-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
                      :class="{ 'bg-blue-50': isSelected(user) }"
                    >
                      <!-- Checkbox -->
                      <div class="flex-shrink-0 mr-3">
                        <div class="w-4 h-4 border-2 border-gray-300 rounded flex items-center justify-center transition-colors"
                             :class="isSelected(user) ? 'bg-[#0097b2] border-[#0097b2]' : ''">
                          <svg v-if="isSelected(user)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                          </svg>
                        </div>
                      </div>

                      <!-- Avatar -->
                      <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                      </div>

                      <!-- Informations utilisateur -->
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ user.nom_complet }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ user.role }}</p>
                      </div>
                    </div>

                    <div v-if="filteredUsers.length === 0" class="p-4 text-center text-gray-500">
                      <p class="text-sm">Aucun utilisateur trouvé</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <div class="p-4 border-t border-gray-200 flex justify-end space-x-3">
                <button
                  type="button"
                  @click="closeModal"
                  class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                  :disabled="creating"
                >
                  Annuler
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 bg-[#0097b2] text-white rounded-lg hover:bg-[#007a94] disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
                  :disabled="!nomGroupe.trim() || selectedUsers.length === 0 || creating"
                >
                  <svg v-if="creating" class="w-4 h-4 animate-spin mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ creating ? 'Création...' : 'Créer' }}
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

// État réactif
const nomGroupe = ref('')
const searchQuery = ref('')
const selectedUsers = ref<User[]>([])
const users = ref<User[]>([])
const loadingUsers = ref(false)
const creating = ref(false)
const error = ref('')

// Utilisateurs filtrés
const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value
  
  const query = searchQuery.value.toLowerCase()
  return users.value.filter(user => 
    user.nom_complet.toLowerCase().includes(query) ||
    user.email.toLowerCase().includes(query) ||
    user.role.toLowerCase().includes(query)
  )
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

// Charger les utilisateurs
const loadUsers = async () => {
  try {
    loadingUsers.value = true
    error.value = ''
    
    const response = await $fetch<{success: boolean, users: User[], error?: string}>(`${config.public.apiBase}/conversations/users`, {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      }
    })
    
    if (response.success && response.users) {
      users.value = response.users
    } else {
      throw new Error(response.error || 'Erreur lors du chargement des utilisateurs')
    }
    
  } catch (err: any) {
    console.error('Erreur lors du chargement des utilisateurs:', err)
    error.value = err.data?.message || err.message || 'Impossible de charger les utilisateurs'
  } finally {
    loadingUsers.value = false
  }
}

// Créer la conversation
const createConversation = async () => {
  try {
    creating.value = true
    
    const response = await $fetch<{success: boolean, conversation: any, error?: string}>(`${config.public.apiBase}/conversations`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: {
        nom_groupe: nomGroupe.value.trim(),
        participants: selectedUsers.value.map(user => user.email)
      }
    })
    
    if (response.success && response.conversation) {
      emit('created', response.conversation)
      closeModal()
    } else {
      throw new Error(response.error || 'Erreur lors de la création de la conversation')
    }
    
  } catch (err: any) {
    console.error('Erreur lors de la création de la conversation:', err)
    error.value = err.data?.message || err.message || 'Impossible de créer la conversation'
  } finally {
    creating.value = false
  }
}

// Fermer la modal
const closeModal = () => {
  // Reset des données
  nomGroupe.value = ''
  searchQuery.value = ''
  selectedUsers.value = []
  error.value = ''
  
  emit('close')
}

// Charger les utilisateurs à l'ouverture
watch(() => props.show, (newShow) => {
  if (newShow) {
    loadUsers()
  }
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