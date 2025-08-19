<template>
  <div class="fixed inset-0 z-50 overflow-y-auto" v-if="isOpen">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Overlay -->
      <div 
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="$emit('close')"
      ></div>

      <!-- Modal -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <form @submit.prevent="submitForm">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="mb-6">
              <h3 class="text-lg font-medium text-gray-900">
                {{ isEditing ? t('badges.modal.editTitle') : t('badges.modal.createTitle') }}
              </h3>
              <p class="mt-1 text-sm text-gray-600">
                {{ isEditing ? t('badges.modal.editSubtitle') : t('badges.modal.createSubtitle') }}
              </p>
            </div>

            <div class="space-y-4">
              <!-- Recherche utilisateur -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ t('badges.form.user') }}
                  <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <input
                    v-model="userSearch"
                    type="text"
                    :placeholder="t('badges.form.userPlaceholder')"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    @input="searchUsers"
                    @focus="showUserDropdown = true"
                  />
                  
                  <!-- Dropdown des utilisateurs -->
                  <div 
                    v-if="showUserDropdown && (searchResults.length > 0 || userSearch.length > 0)"
                    class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto"
                  >
                    <div v-if="loadingUsers" class="p-3 text-center text-gray-500">
                      <ArrowPathIcon class="w-4 h-4 animate-spin inline mr-2" />
                      {{ t('common.searching') }}
                    </div>
                    
                    <div v-else-if="searchResults.length === 0" class="p-3 text-center text-gray-500">
                      {{ t('badges.form.noUsers') }}
                    </div>
                    
                    <div v-else>
                      <button
                        v-for="user in searchResults"
                        :key="user.id"
                        type="button"
                        @click="selectUser(user)"
                        class="w-full px-3 py-2 text-left hover:bg-gray-50 focus:bg-gray-50 focus:outline-none"
                      >
                        <div class="font-medium text-gray-900">{{ user.nom_complet }}</div>
                        <div class="text-sm text-gray-500">{{ user.email }}</div>
                        <div v-if="(user.nb_badges || 0) > 0" class="text-xs text-blue-600">
                          {{ t('badges.form.userBadges', { count: user.nb_badges || 0 }) }}
                        </div>
                      </button>
                    </div>
                  </div>
                </div>
                
                <!-- Utilisateur sélectionné -->
                <div v-if="form.selectedUser" class="mt-2 p-3 bg-blue-50 rounded-lg">
                  <div class="flex items-center justify-between">
                    <div>
                      <div class="font-medium text-blue-900">{{ form.selectedUser.nom_complet }}</div>
                      <div class="text-sm text-blue-700">{{ form.selectedUser.email }}</div>
                    </div>
                    <button
                      type="button"
                      @click="clearUser"
                      class="text-blue-600 hover:text-blue-800"
                    >
                      <XMarkIcon class="w-5 h-5" />
                    </button>
                  </div>
                </div>

                <div v-if="errors.id_utilisateur" class="mt-1 text-sm text-red-600">
                  {{ errors.id_utilisateur[0] }}
                </div>
              </div>

              <!-- Droits -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ t('badges.form.rights') }}
                  <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.droit"
                  type="text"
                  :placeholder="t('badges.form.rightsPlaceholder')"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  required
                />
                <div v-if="errors.droit" class="mt-1 text-sm text-red-600">
                  {{ errors.droit[0] }}
                </div>
              </div>

              <!-- Commentaire -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  {{ t('badges.form.comment') }}
                </label>
                <textarea
                  v-model="form.commentaire"
                  rows="3"
                  :placeholder="t('badges.form.commentPlaceholder')"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                ></textarea>
                <div v-if="errors.commentaire" class="mt-1 text-sm text-red-600">
                  {{ errors.commentaire[0] }}
                </div>
              </div>
            </div>
          </div>

          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="submit"
              :disabled="loading || !isFormValid"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <ArrowPathIcon v-if="loading" class="w-4 h-4 animate-spin mr-2" />
              {{ isEditing ? t('badges.actions.update') : t('badges.actions.create') }}
            </button>
            <button
              type="button"
              @click="$emit('close')"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              {{ t('common.cancel') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, onUnmounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAuthenticatedFetch } from '~/composables/useAuthenticatedFetch'
import { useNotifications } from '~/composables/useNotifications'

// Fonction debounce simple
const debounce = (func: Function, wait: number) => {
  let timeout: NodeJS.Timeout
  return function executedFunction(...args: any[]) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Icônes
import { ArrowPathIcon, XMarkIcon } from '@heroicons/vue/24/outline'

// Props et émissions
const props = defineProps<{
  badge?: any
  isEditing?: boolean
}>()

const emit = defineEmits<{
  close: []
  saved: [badge: any]
}>()

const { t } = useI18n()
const { fetchWithAuth } = useAuthenticatedFetch()
const { showSuccess, showError } = useNotifications()

// Types
interface User {
  id: number
  nom_complet: string
  email: string
  nb_badges?: number
}

interface FormData {
  selectedUser: User | null
  id_utilisateur: number | null
  droit: string
  commentaire: string
}

interface ErrorData {
  id_utilisateur?: string[]
  droit?: string[]
  commentaire?: string[]
}

// État réactif
const loading = ref(false)
const loadingUsers = ref(false)
const showUserDropdown = ref(false)
const userSearch = ref('')
const searchResults = ref<User[]>([])
const errors = ref<ErrorData>({})

const form = reactive<FormData>({
  selectedUser: null,
  id_utilisateur: null,
  droit: '',
  commentaire: ''
})

// Computed
const isOpen = computed(() => true)

const isFormValid = computed(() => {
  return form.selectedUser && form.droit.trim().length > 0
})

// Méthodes
const searchUsers = debounce(async () => {
  if (userSearch.value.length < 2) {
    searchResults.value = []
    return
  }

  loadingUsers.value = true
  try {
    const response = await fetchWithAuth(`/badges/search-users?search=${encodeURIComponent(userSearch.value)}`)
    if (response.success) {
      searchResults.value = response.data
    }
  } catch (error) {
    console.error('Erreur lors de la recherche d\'utilisateurs:', error)
  } finally {
    loadingUsers.value = false
  }
}, 300)

const selectUser = (user: User) => {
  form.selectedUser = user
  form.id_utilisateur = user.id
  userSearch.value = user.nom_complet
  showUserDropdown.value = false
}

const clearUser = () => {
  form.selectedUser = null
  form.id_utilisateur = null
  userSearch.value = ''
}

const submitForm = async () => {
  if (!isFormValid.value) return

  loading.value = true
  errors.value = {}

  try {
    const data = {
      id_utilisateur: form.id_utilisateur,
      droit: form.droit,
      commentaire: form.commentaire || null
    }

    let response
    if (props.isEditing && props.badge) {
      response = await fetchWithAuth(`/badges/${props.badge.numero}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      })
    } else {
      response = await fetchWithAuth('/badges', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      })
    }

    if (response.success) {
      showSuccess(props.isEditing ? t('badges.updateSuccess') : t('badges.createSuccess'))
      emit('saved', response.data)
    } else {
      if (response.errors) {
        errors.value = response.errors
      } else {
        showError(response.message || t('badges.errors.saveFailed'))
      }
    }
  } catch (error) {
    console.error('Erreur lors de la sauvegarde:', error)
    showError(t('badges.errors.saveFailed'))
  } finally {
    loading.value = false
  }
}

// Gestion du clic à l'extérieur
const handleClickOutside = (event: Event) => {
  const target = event.target as HTMLElement
  if (!target.closest('.relative')) {
    showUserDropdown.value = false
  }
}

// Watchers
watch(() => props.badge, (newBadge) => {
  if (newBadge && props.isEditing) {
    form.selectedUser = newBadge.utilisateur
    form.id_utilisateur = newBadge.id_utilisateur
    form.droit = newBadge.droit
    form.commentaire = newBadge.commentaire || ''
    userSearch.value = newBadge.utilisateur.nom_complet
  }
}, { immediate: true })

// Cycle de vie
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
