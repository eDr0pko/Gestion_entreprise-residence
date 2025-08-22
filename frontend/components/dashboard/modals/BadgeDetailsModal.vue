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
            <DialogPanel class="w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left align-middle shadow-xl transition-all">
              <!-- En-tête -->
              <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center text-white font-bold text-xl">
                      {{ badge.numero }}
                    </div>
                    <div>
                      <DialogTitle as="h3" class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ t('badges.badgeDetails') }}
                      </DialogTitle>
                      <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ badge.personne ? `${badge.personne.prenom} ${badge.personne.nom}` : t('badges.unassigned') }}
                      </p>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <StatusBadge :status="badge.statut || 'nouveau'" />
                    <button
                      @click="$emit('close')"
                      class="p-2 text-gray-400 hover:text-gray-600 dark:text-gray-400 hover:bg-white dark:bg-gray-800 rounded-lg transition-colors"
                    >
                      <Icon name="x-mark" class="w-5 h-5" />
                    </button>
                  </div>
                </div>
              </div>

              <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                  
                  <!-- Informations principales -->
                  <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Informations du badge -->
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6">
                      <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ t('badges.badgeInfo') }}
                      </h4>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ t('badges.badgeNumber') }}
                          </label>
                          <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                            #{{ badge.numero }}
                          </p>
                        </div>
                        <div>
                          <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ t('badges.status') }}
                          </label>
                          <div class="mt-1">
                            <StatusBadge :status="badge.statut || 'nouveau'" />
                          </div>
                        </div>
                        <div class="md:col-span-2">
                          <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ t('badges.accessRights') }}
                          </label>
                          <p class="mt-1 text-gray-900 dark:text-white">
                            {{ badge.droit }}
                          </p>
                        </div>
                        <div v-if="badge.commentaire" class="md:col-span-2">
                          <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ t('badges.comment') }}
                          </label>
                          <p class="mt-1 text-gray-900 dark:text-white">
                            {{ badge.commentaire }}
                          </p>
                        </div>
                      </div>
                    </div>

                    <!-- Informations du propriétaire -->
                    <div v-if="badge.personne" class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6">
                      <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ t('badges.owner') }}
                      </h4>
                      <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-gray-400 to-gray-600 flex items-center justify-center text-white font-bold">
                          {{ badge.personne.prenom.charAt(0) }}{{ badge.personne.nom.charAt(0) }}
                        </div>
                        <div>
                          <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ badge.personne.prenom }} {{ badge.personne.nom }}
                          </p>
                          <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ badge.personne.email }}
                          </p>
                        </div>
                      </div>
                    </div>

                    <!-- Historique des actions -->
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6">
                      <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                          {{ t('badges.history') }}
                        </h4>
                        <button
                          @click="loadHistorique"
                          :disabled="loadingHistorique"
                          class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                        >
                          <RefreshIcon class="w-4 h-4 inline mr-1" :class="{ 'animate-spin': loadingHistorique }" />
                          {{ t('common.refresh') }}
                        </button>
                      </div>
                      
                      <div v-if="loadingHistorique" class="text-center py-8">
                        <LoadingSpinner />
                      </div>
                      
                      <div v-else-if="historique.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                        {{ t('badges.noHistory') }}
                      </div>
                      
                      <div v-else class="space-y-4 max-h-96 overflow-y-auto">
                        <div
                          v-for="(event, index) in historique"
                          :key="index"
                          class="flex items-start space-x-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700"
                        >
                          <div :class="getEventIconClasses(event.action)" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center">
                            <Icon :name="getEventIcon(event.action)" class="w-4 h-4" />
                          </div>
                          <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                              <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ getActionLabel(event.action) }}
                              </p>
                              <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ formatDate(event.date_heure) }}
                              </p>
                            </div>
                            <p v-if="event.message" class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                              {{ event.message }}
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Actions -->
                  <div class="space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6">
                      <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ t('badges.actions.title') }}
                      </h4>
                      <div class="space-y-3">
                        <button
                          @click="$emit('edit')"
                          class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors"
                        >
                          <Icon name="pencil" class="w-4 h-4 mr-2" />
                          {{ t('badges.actions.edit') }}
                        </button>
                        
                        <button
                          v-if="badge.statut === 'inactif'"
                          @click="activerBadge"
                          :disabled="loading"
                          class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 disabled:opacity-50 transition-colors"
                        >
                          <PlayIcon class="w-4 h-4 mr-2" />
                          {{ t('badges.actions.activate') }}
                        </button>
                        
                        <button
                          v-else-if="badge.statut === 'actif'"
                          @click="desactiverBadge"
                          :disabled="loading"
                          class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 disabled:opacity-50 transition-colors"
                        >
                          <PauseIcon class="w-4 h-4 mr-2" />
                          {{ t('badges.actions.deactivate') }}
                        </button>
                        
                        <button
                          v-if="badge.statut !== 'suspendu'"
                          @click="suspendeBadge"
                          :disabled="loading"
                          class="w-full flex items-center justify-center px-4 py-2 bg-yellow-600 text-white font-medium rounded-lg hover:bg-yellow-700 disabled:opacity-50 transition-colors"
                        >
                          <PauseCircleIcon class="w-4 h-4 mr-2" />
                          {{ t('badges.actions.suspend') }}
                        </button>
                      </div>
                    </div>

                    <!-- Statistiques rapides -->
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-6">
                      <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ t('badges.quickStats') }}
                      </h4>
                      <div class="space-y-3">
                        <div class="flex justify-between">
                          <span class="text-sm text-gray-600 dark:text-gray-400">{{ t('badges.totalActions') }}</span>
                          <span class="text-sm font-medium">{{ historique.length }}</span>
                        </div>
                        <div class="flex justify-between">
                          <span class="text-sm text-gray-600 dark:text-gray-400">{{ t('badges.lastActivity') }}</span>
                          <span class="text-sm font-medium">
                            {{ historique.length > 0 ? formatDateShort(historique[0].date_heure) : t('common.never') }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup lang="ts">
  import { onMounted, ref } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useNotification } from '~/composables/useNotification'
  import { useApiClient } from '~/composables/useApiClient'
  import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot,  } from '@headlessui/vue'

  // Components
  import StatusBadge from '../components/StatusBadge.vue'
  import LoadingSpinner from '../components/LoadingSpinner.vue'

  interface Props {
    badge: any
  }

  interface HistoriqueEvent {
    action: string;
    date_heure: string;
    message?: string;
  }

  // Import du système de thème
  const { initTheme } = useTheme()

  const props = defineProps<Props>()

  const emit = defineEmits<{
    close: []
    edit: []
  }>()

  const { t } = useI18n()
  const { showNotification } = useNotification()
  const { apiCall } = useApiClient()

  // État
  const loading = ref(false)
  const loadingHistorique = ref(false)
  const historique = ref<HistoriqueEvent[]>([])

  // Méthodes
  const loadHistorique = async () => {
    try {
      loadingHistorique.value = true
      const response = await apiCall(`/api/badges/${props.badge.numero}/historique`)
      
      if (response.success) {
        historique.value = response.data.historique || []
      }
    } catch (error) {
      console.error('Erreur lors du chargement de l\'historique:', error)
      showNotification(t('badges.errors.historyFailed'), 'error')
    } finally {
      loadingHistorique.value = false
    }
  }

  const activerBadge = async () => {
    try {
      loading.value = true
      const response = await apiCall(`/api/badges/${props.badge.numero}/activer`, 'POST')
      
      if (response.success) {
        showNotification(t('badges.messages.activated'), 'success')
        // Mettre à jour le statut local
        props.badge.statut = 'actif'
        // Recharger l'historique
        loadHistorique()
      }
    } catch (error) {
      console.error('Erreur lors de l\'activation:', error)
      showNotification(t('badges.errors.activateFailed'), 'error')
    } finally {
      loading.value = false
    }
  }

  const desactiverBadge = async () => {
    try {
      loading.value = true
      const response = await apiCall(`/api/badges/${props.badge.numero}/desactiver`, 'POST')
      
      if (response.success) {
        showNotification(t('badges.messages.deactivated'), 'success')
        props.badge.statut = 'inactif'
        loadHistorique()
      }
    } catch (error) {
      console.error('Erreur lors de la désactivation:', error)
      showNotification(t('badges.errors.deactivateFailed'), 'error')
    } finally {
      loading.value = false
    }
  }

  const suspendeBadge = async () => {
    try {
      loading.value = true
      const response = await apiCall(`/api/badges/${props.badge.numero}/suspendre`, 'POST')
      
      if (response.success) {
        showNotification(t('badges.messages.suspended'), 'success')
        props.badge.statut = 'suspendu'
        loadHistorique()
      }
    } catch (error) {
      console.error('Erreur lors de la suspension:', error)
      showNotification(t('badges.errors.suspendFailed'), 'error')
    } finally {
      loading.value = false
    }
  }

  const getEventIcon = (action: string) => {
    switch (action) {
      case 'activation':
        return 'check-circle'
      case 'desactivation':
        return 'x-circle'
      case 'suspension':
        return 'pause-circle'
      case 'creation':
        return 'plus'
      case 'modification':
      case 'attribution':
        return 'cog'
      default:
        return 'check-circle'
    }
  }

  const getEventIconClasses = (action: string) => {
    switch (action) {
      case 'activation':
        return 'bg-green-100 text-green-600'
      case 'desactivation':
        return 'bg-red-100 text-red-600'
      case 'suspension':
        return 'bg-yellow-100 text-yellow-600'
      case 'creation':
        return 'bg-blue-100 text-blue-600'
      case 'modification':
      case 'attribution':
        return 'bg-purple-100 text-purple-600'
      default:
        return 'bg-gray-100 text-gray-600'
    }
  }

  const getActionLabel = (action: string) => {
    const labels: Record<string, string> = {
      'activation': t('badges.actions.activated'),
      'desactivation': t('badges.actions.deactivated'),
      'suspension': t('badges.actions.suspended'),
      'creation': t('badges.actions.created'),
      'modification': t('badges.actions.modified'),
      'attribution': t('badges.actions.assigned'),
      'utilisation': t('badges.actions.used')
    }
    return labels[action] || action
  }

  const formatDate = (dateString: string) => {
    const date = new Date(dateString)
    return date.toLocaleString('fr-FR', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  const formatDateShort = (dateString: string) => {
    const now = new Date()
    const date = new Date(dateString)
    const diffTime = Math.abs(now.getTime() - date.getTime())
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

    if (diffDays === 1) {
      return t('common.today')
    } else if (diffDays === 2) {
      return t('common.yesterday')
    } else if (diffDays < 7) {
      return t('common.daysAgo', { count: diffDays - 1 })
    } else {
      return date.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' })
    }
  }

  // Lifecycle
  // Initialisation du thème
  
  // Initialisation unifiée
  onMounted(() => {
    loadHistorique()
    initTheme()
  })
</script>


