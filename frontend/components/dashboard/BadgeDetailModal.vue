<template>
  <div class="fixed inset-0 z-50 overflow-y-auto" v-if="badge">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Overlay -->
      <div 
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="$emit('close')"
      ></div>

      <!-- Modal -->
      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
        <div class="bg-white">
          <!-- En-tête -->
          <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                  <span class="text-white font-bold text-lg">{{ badge.numero }}</span>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">
                    {{ t('badges.detail.title', { numero: badge.numero }) }}
                  </h3>
                  <p class="text-sm text-gray-600">
                    {{ badge.utilisateur?.nom_complet || (badge.utilisateur_id ? t('badges.assignedUnknown') : t('badges.unassigned')) }}
                  </p>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <span
                  :class="getStatusClass(badge.statut_actuel)"
                  class="inline-flex px-3 py-1 rounded-full text-sm font-medium"
                >
                  {{ getStatusLabel(badge.statut_actuel) }}
                </span>
                <button
                  @click="$emit('close')"
                  class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"
                >
                  <Icon name="x-mark" class="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>

          <!-- Contenu principal -->
          <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- Informations du badge -->
              <div class="space-y-6">
                <div>
                  <h4 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ t('badges.detail.badgeInfo') }}
                  </h4>
                  
                  <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="flex justify-between">
                      <span class="font-medium text-gray-700">{{ t('badges.table.number') }}:</span>
                      <span class="text-gray-900">#{{ badge.numero }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                      <span class="font-medium text-gray-700">{{ t('badges.table.rights') }}:</span>
                      <span class="text-gray-900">{{ badge.droit }}</span>
                    </div>
                    
                    <div v-if="badge.commentaire" class="flex justify-between">
                      <span class="font-medium text-gray-700">{{ t('badges.form.comment') }}:</span>
                      <span class="text-gray-900">{{ badge.commentaire }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                      <span class="font-medium text-gray-700">{{ t('badges.table.status') }}:</span>
                      <span
                        :class="getStatusClass(badge.statut_actuel)"
                        class="inline-flex px-2 py-1 rounded-full text-xs font-medium"
                      >
                        {{ getStatusLabel(badge.statut_actuel) }}
                      </span>
                    </div>
                    
                    <div class="flex justify-between">
                      <span class="font-medium text-gray-700">{{ t('badges.table.lastUse') }}:</span>
                      <span class="text-gray-900">
                        {{ badge.derniere_utilisation ? formatDate(badge.derniere_utilisation) : t('common.never') }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Informations utilisateur -->
                <div>
                  <h4 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ t('badges.detail.userInfo') }}
                  </h4>
                  
                  <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="flex justify-between">
                      <span class="font-medium text-gray-700">{{ t('common.name') }}:</span>
                      <span class="text-gray-900">{{ badge.utilisateur.nom_complet }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                      <span class="font-medium text-gray-700">{{ t('common.email') }}:</span>
                      <span class="text-gray-900">{{ badge.utilisateur.email }}</span>
                    </div>
                    
                    <div v-if="badge.utilisateur.telephone" class="flex justify-between">
                      <span class="font-medium text-gray-700">{{ t('common.phone') }}:</span>
                      <span class="text-gray-900">{{ badge.utilisateur.telephone }}</span>
                    </div>
                  </div>
                </div>

                <!-- Actions -->
                <div>
                  <h4 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ t('badges.detail.actions') }}
                  </h4>
                  
                  <div class="flex flex-wrap gap-2">
                    <button
                      @click="$emit('edit', badge)"
                      class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-150"
                    >
                      <Icon name="pencil" class="w-4 h-4" />
                      {{ t('badges.actions.edit') }}
                    </button>
                    
                    <button
                      @click="$emit('toggle-status', badge)"
                      :class="badge.statut_actuel === 'actif' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'"
                      class="inline-flex items-center gap-2 px-4 py-2 text-white rounded-lg transition-colors duration-150"
                    >
                      <Icon :name="badge.statut_actuel === 'actif' ? 'stop' : 'play'" class="w-4 h-4" />
                      {{ badge.statut_actuel === 'actif' ? t('badges.actions.deactivate') : t('badges.actions.activate') }}
                    </button>
                    
                    <button
                      @click="$emit('delete', badge)"
                      class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-150"
                    >
                      <Icon name="trash" class="w-4 h-4" />
                      {{ t('badges.actions.delete') }}
                    </button>
                  </div>
                </div>
              </div>

              <!-- Historique des activités -->
              <div>
                <h4 class="text-lg font-semibold text-gray-900 mb-4">
                  {{ t('badges.detail.history') }}
                </h4>
                
                <div class="bg-gray-50 rounded-lg p-4 max-h-96 overflow-y-auto">
                  <div v-if="badge.suivis && badge.suivis.length > 0" class="space-y-3">
                    <div
                      v-for="suivi in badge.suivis"
                      :key="suivi.id"
                      class="bg-white rounded-lg p-3 border border-gray-200"
                    >
                      <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                          <div :class="getActivityColorClass(suivi.action_color)" class="w-3 h-3 rounded-full mt-2"></div>
                          <div class="flex-1">
                            <div class="flex items-center gap-2">
                              <span class="font-medium text-gray-900">{{ suivi.action_libelle }}</span>
                              <span class="text-sm text-gray-500">{{ formatDate(suivi.date_heure) }}</span>
                            </div>
                            <p v-if="suivi.message" class="text-sm text-gray-600 mt-1">{{ suivi.message }}</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div v-else class="text-center text-gray-500 py-8">
                    <Icon name="clock" class="w-8 h-8 mx-auto mb-2 text-gray-300" />
                    <p>{{ t('badges.detail.noHistory') }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pied de page -->
          <div class="bg-gray-50 px-6 py-3 flex justify-end">
            <button
              @click="$emit('close')"
              class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-150"
            >
              {{ t('common.close') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { computed } from 'vue'
  import { useI18n } from 'vue-i18n'

  // Props
  const props = defineProps<{
    badge: any
  }>()

  // Émissions
  const emit = defineEmits<{
    close: []
    edit: [badge: any]
    'toggle-status': [badge: any]
    delete: [badge: any]
  }>()

  const { t } = useI18n()

  // Méthodes utilitaires
  const getStatusClass = (status: string) => {
    const classes = {
      'actif': 'bg-green-100 text-green-800',
      'inactif': 'bg-red-100 text-red-800',
      'inconnu': 'bg-gray-100 text-gray-800'
    }
    return (classes as any)[status] || classes['inconnu']
  }

  const getStatusLabel = (status: string) => {
    const labels = {
      'actif': t('badges.status.active'),
      'inactif': t('badges.status.inactive'),
      'inconnu': t('badges.status.unknown')
    }
    return (labels as any)[status] || labels['inconnu']
  }

  const getActivityColorClass = (color: string) => {
    const colorClasses = {
      'green': 'bg-green-500',
      'red': 'bg-red-500',
      'blue': 'bg-blue-500',
      'orange': 'bg-orange-500',
      'emerald': 'bg-emerald-500',
      'gray': 'bg-gray-500'
    }
    return (colorClasses as any)[color] || colorClasses['gray']
  }

  const formatDate = (dateString: string) => {
    if (!dateString) return ''
    return new Date(dateString).toLocaleString('fr-FR', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit'
    })
  }
</script>


