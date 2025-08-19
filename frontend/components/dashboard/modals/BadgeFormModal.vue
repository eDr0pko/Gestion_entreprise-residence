<template>
  <TransitionRoot appear :show="true" as="template">
    <Dialog as="div" class="relative z-50" @close="$emit('close')">
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black bg-opacity-25" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel class="w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
              <!-- En-tête -->
              <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900 mb-6">
                {{ isEdit ? t('badges.editBadge') : t('badges.createBadge') }}
              </DialogTitle>

              <!-- Formulaire -->
              <form @submit.prevent="submitForm" class="space-y-6">
                
                <!-- Recherche et sélection de personne -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ t('badges.form.person') }} <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <input
                      v-model="personSearch"
                      type="text"
                      :placeholder="t('badges.form.searchPersonPlaceholder')"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      @input="searchPersonnes"
                      @focus="showPersonSuggestions = true"
                    />
                    <SearchIcon class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
                    
                    <!-- Suggestions de personnes -->
                    <div 
                      v-if="showPersonSuggestions && personSuggestions.length > 0"
                      class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"
                    >
                      <div
                        v-for="person in personSuggestions"
                        :key="person.id_personne"
                        @click="selectPerson(person)"
                        class="px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0"
                      >
                        <div class="font-medium">{{ person.prenom }} {{ person.nom }}</div>
                        <div class="text-sm text-gray-500">{{ person.email }}</div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Personne sélectionnée -->
                  <div v-if="form.selectedPerson" class="mt-2 p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center justify-between">
                      <div>
                        <div class="font-medium text-blue-900">
                          {{ form.selectedPerson.prenom }} {{ form.selectedPerson.nom }}
                        </div>
                        <div class="text-sm text-blue-700">{{ form.selectedPerson.email }}</div>
                      </div>
                      <button
                        type="button"
                        @click="clearSelectedPerson"
                        class="text-blue-600 hover:text-blue-800"
                      >
                        <XMarkIcon class="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                  
                  <div v-if="errors.id_utilisateur" class="mt-1 text-sm text-red-600">
                    {{ errors.id_utilisateur }}
                  </div>
                </div>

                <!-- Droits d'accès -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ t('badges.form.accessRights') }} <span class="text-red-500">*</span>
                  </label>
                  <select
                    v-model="form.droit"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required
                  >
                    <option value="">{{ t('badges.form.selectRights') }}</option>
                    <option value="Accès total">{{ t('badges.accessRights.full') }}</option>
                    <option value="Accès bâtiment A">{{ t('badges.accessRights.buildingA') }}</option>
                    <option value="Accès bâtiment B">{{ t('badges.accessRights.buildingB') }}</option>
                    <option value="Accès bâtiment C">{{ t('badges.accessRights.buildingC') }}</option>
                    <option value="Accès bureaux">{{ t('badges.accessRights.offices') }}</option>
                    <option value="Accès parking">{{ t('badges.accessRights.parking') }}</option>
                    <option value="Accès espaces communs">{{ t('badges.accessRights.commonAreas') }}</option>
                    <option value="Accès services">{{ t('badges.accessRights.services') }}</option>
                    <option value="Accès restreint">{{ t('badges.accessRights.restricted') }}</option>
                  </select>
                  <div v-if="errors.droit" class="mt-1 text-sm text-red-600">
                    {{ errors.droit }}
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
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                    maxlength="512"
                  ></textarea>
                  <div class="mt-1 text-sm text-gray-500">
                    {{ commentLength }}/512
                  </div>
                  <div v-if="errors.commentaire" class="mt-1 text-sm text-red-600">
                    {{ errors.commentaire }}
                  </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                  <button
                    type="button"
                    @click="$emit('close')"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
                  >
                    {{ t('common.cancel') }}
                  </button>
                  <button
                    type="submit"
                    :disabled="loading || !isFormValid"
                    class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all flex items-center"
                  >
                    <LoadingSpinner v-if="loading" size="sm" color="text-white" class="mr-2" />
                    {{ isEdit ? t('common.save') : t('common.create') }}
                  </button>
                </div>
              </form>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup lang="ts">
  import { ref, computed, onMounted, watch } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useNotification } from '~/composables/useNotification'
  import { useApiClient } from '~/composables/useApiClient'
  import { debounce } from 'lodash-es'
  // @ts-ignore
  import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
  } from '@headlessui/vue'
  import SearchIcon from '~/components/icons/SearchIcon.vue'
  import XMarkIcon from '~/components/icons/XMarkIcon.vue'
  import LoadingSpinner from '../components/LoadingSpinner.vue'

  interface Badge {
    numero?: number
    id_utilisateur: number
    commentaire: string
    droit: string
    personne?: {
      id_personne: number
      nom: string
      prenom: string
      email: string
    }
  }

  interface Person {
    id_personne: number
    nom: string
    prenom: string
    email: string
  }

  interface Props {
    isEdit: boolean
    badge?: Badge | null
  }

  const props = withDefaults(defineProps<Props>(), {
    badge: null
  })

  const emit = defineEmits<{
    close: []
    saved: []
  }>()

  const { t } = useI18n()
  const { showNotification } = useNotification()
  const { apiCall } = useApiClient()

  // État
  const loading = ref(false)
  const showPersonSuggestions = ref(false)
  const personSearch = ref('')
  const personSuggestions = ref<Person[]>([])

  const form = ref({
    id_utilisateur: '',
    droit: '',
    commentaire: '',
    selectedPerson: null as Person | null
  })

  const errors = ref({
    id_utilisateur: '',
    droit: '',
    commentaire: ''
  })

  // Computed
  const commentLength = computed(() => form.value.commentaire.length)

  const isFormValid = computed(() => {
    return form.value.selectedPerson && form.value.droit.trim() !== ''
  })

  // Méthodes
  const searchPersonnes = debounce(async () => {
    if (personSearch.value.length < 2) {
      personSuggestions.value = []
      return
    }

    try {
      const response = await apiCall('/badges/search-users', 'GET', null, {
        search: personSearch.value
      })
      
      if (response.success) {
        personSuggestions.value = response.data || []
      }
    } catch (error) {
      console.error('Erreur lors de la recherche de personnes:', error)
      personSuggestions.value = []
    }
  }, 300)

  const selectPerson = (person: Person) => {
    form.value.selectedPerson = person
    form.value.id_utilisateur = person.id_personne.toString()
    personSearch.value = `${person.prenom} ${person.nom}`
    showPersonSuggestions.value = false
    errors.value.id_utilisateur = ''
  }

  const clearSelectedPerson = () => {
    form.value.selectedPerson = null
    form.value.id_utilisateur = ''
    personSearch.value = ''
    errors.value.id_utilisateur = ''
  }

  const clearErrors = () => {
    errors.value = {
      id_utilisateur: '',
      droit: '',
      commentaire: ''
    }
  }

  const validateForm = () => {
    clearErrors()
    let isValid = true

    if (!form.value.selectedPerson) {
      errors.value.id_utilisateur = t('badges.form.errors.personRequired')
      isValid = false
    }

    if (!form.value.droit.trim()) {
      errors.value.droit = t('badges.form.errors.rightsRequired')
      isValid = false
    }

    return isValid
  }

  const submitForm = async () => {
    if (!validateForm()) return

    try {
      loading.value = true
      
      const data = {
        id_utilisateur: parseInt(form.value.id_utilisateur),
        droit: form.value.droit,
        commentaire: form.value.commentaire || null
      }

      let response
      if (props.isEdit && props.badge) {
        response = await apiCall(`/api/badges/${props.badge.numero}`, 'PUT', data)
      } else {
        response = await apiCall('/badges', 'POST', data)
      }

      if (response.success) {
        showNotification(
          props.isEdit ? t('badges.messages.updated') : t('badges.messages.created'),
          'success'
        )
        emit('saved')
        emit('close')
      }
    } catch (error: any) {
      console.error('Erreur lors de la sauvegarde:', error)
      
      if (error.response?.data?.errors) {
        // Erreurs de validation du serveur
        const serverErrors = error.response.data.errors
        for (const [field, messages] of Object.entries(serverErrors)) {
          if (Array.isArray(messages) && messages.length > 0) {
            errors.value[field as keyof typeof errors.value] = messages[0]
          }
        }
      } else {
        showNotification(
          props.isEdit ? t('badges.errors.updateFailed') : t('badges.errors.createFailed'),
          'error'
        )
      }
    } finally {
      loading.value = false
    }
  }

  // Cycle de vie
  onMounted(() => {
    if (props.isEdit && props.badge) {
      form.value.id_utilisateur = props.badge.id_utilisateur.toString()
      form.value.droit = props.badge.droit
      form.value.commentaire = props.badge.commentaire || ''
      
      if (props.badge.personne) {
        form.value.selectedPerson = props.badge.personne
        personSearch.value = `${props.badge.personne.prenom} ${props.badge.personne.nom}`
      }
    }
  })

  // Click outside pour fermer les suggestions
  watch(() => showPersonSuggestions.value, (newVal) => {
    if (newVal) {
      setTimeout(() => {
        document.addEventListener('click', handleClickOutside)
      }, 100)
    } else {
      document.removeEventListener('click', handleClickOutside)
    }
  })

  const handleClickOutside = (event: Event) => {
    const target = event.target as Element
    if (!target.closest('.relative')) {
      showPersonSuggestions.value = false
    }
  }
</script>


