<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show" class="fixed inset-0 z-[60] overflow-y-auto">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal"></div>
        
        <!-- Modal -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold text-gray-900">Ajouter des membres</h3>
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
            <form @submit.prevent="addMembers" class="flex flex-col flex-1 min-h-0">
              <!-- Sélection des nouveaux membres -->
              <div class="flex-1 min-h-0 flex flex-col">
                <div class="p-4 border-b border-gray-100">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Sélectionner de nouveaux membres ({{ selectedUsers.length }} sélectionné{{ selectedUsers.length > 1 ? 's' : '' }})
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

                <!-- Liste des utilisateurs disponibles -->
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
                    <button @click="loadAvailableUsers" class="text-xs text-[#0097b2] hover:text-[#007a94] mt-2">
                      Réessayer
                    </button>
                  </div>

                  <div v-else class="space-y-1 p-2">
                    <div
                      v-for="user in filteredAvailableUsers"
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

                    <div v-if="filteredAvailableUsers.length === 0" class="p-4 text-center text-gray-500">
                      <p class="text-sm">{{ availableUsers.length === 0 ? 'Tous les utilisateurs sont déjà membres' : 'Aucun utilisateur trouvé' }}</p>
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
                  :disabled="adding"
                >
                  Annuler
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 bg-[#0097b2] text-white rounded-lg hover:bg-[#007a94] disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center"
                  :disabled="selectedUsers.length === 0 || adding"
                >
                  <svg v-if="adding" class="w-4 h-4 animate-spin mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ adding ? 'Ajout...' : `Ajouter ${selectedUsers.length} membre${selectedUsers.length > 1 ? 's' : ''}` }}
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
      // Filtrer les utilisateurs qui ne sont pas déjà membres
      const existingEmails = props.existingMembers.map(member => member.email)
      availableUsers.value = response.users.filter(user => !existingEmails.includes(user.email))
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

// Ajouter les membres sélectionnés
const addMembers = async () => {
  if (!props.conversation || selectedUsers.value.length === 0) return
  
  try {
    adding.value = true
    
    const response = await $fetch<{success: boolean, members: Member[], error?: string}>(`${config.public.apiBase}/conversations/${props.conversation.id_groupe_message}/members`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: {
        members: selectedUsers.value.map(user => user.email)
      }
    })
    
    if (response.success && response.members) {
      emit('membersAdded', response.members)
      closeModal()
    } else {
      throw new Error(response.error || 'Erreur lors de l\'ajout des membres')
    }
    
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