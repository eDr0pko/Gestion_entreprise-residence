<template>
  <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Overlay -->
      <div 
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="$emit('close')"
      ></div>

      <!-- Centre le modal -->
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <!-- Modal panel -->
      <div class="relative inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
        <div class="absolute top-0 right-0 pt-4 pr-4">
          <button
            type="button"
            class="bg-white rounded-md text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            @click="$emit('close')"
          >
            <span class="sr-only">Fermer</span>
            <XMarkIcon class="h-6 w-6" />
          </button>
        </div>

        <div class="sm:flex sm:items-start">
          <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
            <BadgeIcon class="h-6 w-6 text-blue-600" />
          </div>
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
              {{ isEditing ? 'Modifier le badge' : 'Créer un badge' }}
            </h3>
            <p class="text-sm text-gray-500 mt-1">
              {{ isEditing ? 'Modifiez les informations du badge' : 'Attribuez un badge d\'accès à un utilisateur' }}
            </p>
          </div>
        </div>

        <form @submit.prevent="handleSubmit" class="mt-6 space-y-6">
          <!-- Sélection utilisateur -->
          <div>
            <label for="user" class="block text-sm font-medium text-gray-700 mb-2">
              Utilisateur
            </label>
            <div class="relative">
              <input
                v-model="searchUser"
                type="text"
                id="user"
                placeholder="Rechercher un utilisateur..."
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                @input="searchUsers"
                autocomplete="off"
              />
              
              <!-- Liste des utilisateurs -->
              <div v-if="showUserList && users.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-xl shadow-lg max-h-60 overflow-auto">
                <button
                  v-for="user in users"
                  :key="user.id_personne"
                  type="button"
                  class="w-full px-4 py-3 text-left hover:bg-gray-50 focus:bg-gray-50 focus:outline-none flex items-center gap-3"
                  @click="selectUser(user)"
                >
                  <div class="flex-1">
                    <div class="font-medium text-gray-900">{{ user.nom_complet }}</div>
                    <div class="text-sm text-gray-500">{{ user.email }}</div>
                  </div>
                  <div v-if="user.badges_count > 0" class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded-full">
                    {{ user.badges_count }} badge(s)
                  </div>
                </button>
              </div>
              
              <div v-else-if="showUserList && searchUser && users.length === 0" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-xl shadow-lg">
                <div class="px-4 py-3 text-sm text-gray-500">
                  Aucun utilisateur trouvé
                </div>
              </div>
            </div>
            
            <!-- Utilisateur sélectionné -->
            <div v-if="form.selectedUser" class="mt-3 p-3 bg-blue-50 rounded-xl border border-blue-200">
              <div class="flex items-center justify-between">
                <div>
                  <div class="font-medium text-blue-900">{{ form.selectedUser.nom_complet }}</div>
                  <div class="text-sm text-blue-700">{{ form.selectedUser.email }}</div>
                </div>
                <button
                  type="button"
                  @click="form.selectedUser = null; searchUser = ''"
                  class="text-blue-600 hover:text-blue-800"
                >
                  <XMarkIcon class="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>

          <!-- Droits d'accès -->
          <div>
            <label for="rights" class="block text-sm font-medium text-gray-700 mb-2">
              Droits d'accès
            </label>
            <input
              v-model="form.droit"
              type="text"
              id="rights"
              placeholder="Ex: Accès bâtiment A, Bureaux..."
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              required
            />
          </div>

          <!-- Commentaire -->
          <div>
            <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
              Commentaire
            </label>
            <textarea
              v-model="form.commentaire"
              id="comment"
              rows="3"
              placeholder="Informations supplémentaires sur ce badge..."
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
            ></textarea>
          </div>

          <!-- Actions -->
          <div class="flex flex-col sm:flex-row gap-3 pt-4">
            <button
              type="button"
              @click="$emit('close')"
              class="w-full sm:w-auto px-6 py-3 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Annuler
            </button>
            <button
              type="submit"
              :disabled="!canSubmit || saving"
              class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-medium hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              <div v-if="saving" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              {{ saving ? 'Enregistrement...' : (isEditing ? 'Modifier' : 'Créer') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, reactive, computed, watch, onMounted, onUnmounted } from 'vue'
  import { useAuthenticatedFetch } from '~/composables/useAuthenticatedFetch'

  // Import des icônes
  import BadgeIcon from '~/components/icons/BadgeIcon.vue'
  import XMarkIcon from '~/components/icons/XMarkIcon.vue'

  const { fetchWithAuth } = useAuthenticatedFetch()

  // Props
  interface Props {
    badge?: any
    isEditing?: boolean
  }

  const props = withDefaults(defineProps<Props>(), {
    badge: null,
    isEditing: false
  })

  // Emits
  const emit = defineEmits<{
    close: []
    saved: []
  }>()

  // État réactif
  const saving = ref(false)
  const searchUser = ref('')
  const showUserList = ref(false)
  const users = ref<any[]>([])

  const form = reactive({
    selectedUser: null as any,
    droit: '',
    commentaire: ''
  })

  // Computed
  const canSubmit = computed(() => {
    return form.selectedUser && form.droit.trim()
  })

  // Méthodes
  let searchTimeout: NodeJS.Timeout
  const searchUsers = () => {
    clearTimeout(searchTimeout)
    if (searchUser.value.length < 2) {
      showUserList.value = false
      return
    }
    
    searchTimeout = setTimeout(async () => {
      try {
        const response = await fetchWithAuth(`/badges/search-users?search=${searchUser.value}`)
        if (response.success) {
          users.value = response.data
          showUserList.value = true
        }
      } catch (error) {
        console.error('Erreur lors de la recherche d\'utilisateurs:', error)
      }
    }, 300)
  }

  const selectUser = (user: any) => {
    form.selectedUser = user
    searchUser.value = user.nom_complet
    showUserList.value = false
  }

  const handleSubmit = async () => {
    if (!canSubmit.value || saving.value) return

    try {
      saving.value = true
      
      const data = {
        id_utilisateur: form.selectedUser.id_personne,
        droit: form.droit,
        commentaire: form.commentaire || null
      }

      let response
      if (props.isEditing) {
        response = await fetchWithAuth(`/badges/${props.badge.numero}`, {
          method: 'PUT',
          body: JSON.stringify(data)
        })
      } else {
        response = await fetchWithAuth('/badges', {
          method: 'POST',
          body: JSON.stringify(data)
        })
      }

      if (response.success) {
        emit('saved')
      } else {
        console.error('Erreur:', response.message)
      }
    } catch (error) {
      console.error('Erreur lors de la sauvegarde:', error)
    } finally {
      saving.value = false
    }
  }

  // Watchers
  watch(() => props.badge, (newBadge) => {
    if (newBadge && props.isEditing) {
      form.selectedUser = newBadge.utilisateur || null
      form.droit = newBadge.droit || ''
      form.commentaire = newBadge.commentaire || ''
      searchUser.value = newBadge.utilisateur?.nom_complet || ''
    }
  }, { immediate: true })

  // Click en dehors pour fermer la liste
  const handleClickOutside = (event: Event) => {
    const target = event.target as Element
    if (!target.closest('.relative')) {
      showUserList.value = false
    }
  }

  onMounted(() => {
    document.addEventListener('click', handleClickOutside)
  })

  onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
  })
</script>


