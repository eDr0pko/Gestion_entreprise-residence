<template>
  <div class="space-y-6">
    <!-- En-tête et statistiques -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <div>
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">{{ t('badges.title') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ t('badges.subtitle') }}</p>
      </div>
      <div class="flex flex-col sm:flex-row gap-3">
        <button
          @click="showCreateModal = true"
          class="btn-primary inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-all duration-200"
        >
                          <Icon name="plus" class="w-4 h-4 mr-2" />
          {{ t('badges.actions.create') }}
        </button>
        <button
          @click="refreshData"
          :disabled="loading"
          class="btn-secondary inline-flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-all duration-200"
        >
          <Icon name="refresh" class="w-5 h-5" :class="{ 'animate-spin': loading }" />
          {{ t('badges.actions.refresh') }}
        </button>
      </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ t('badges.stats.total') }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ stats.total_badges || 0 }}</p>
          </div>
          <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
            <Icon name="identification" class="w-6 h-6 text-blue-600" />
          </div>
        </div>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ t('badges.stats.active') }}</p>
            <p class="text-2xl font-bold text-green-600">{{ stats.badges_actifs || 0 }}</p>
          </div>
          <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
            <Icon name="check-circle" class="w-6 h-6 text-green-600" />
          </div>
        </div>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ t('badges.stats.inactive') }}</p>
            <p class="text-2xl font-bold text-red-600">{{ stats.badges_inactifs || 0 }}</p>
          </div>
          <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
            <Icon name="x-circle" class="w-6 h-6 text-red-600" />
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ t('badges.stats.assigned') }}</p>
            <p class="text-2xl font-bold text-purple-600">{{ stats.badges_affectes || 0 }}</p>
          </div>
          <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
            <Icon name="user-group" class="w-6 h-6 text-purple-600" />
          </div>
        </div>
      </div>
      
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ t('badges.stats.today') }}</p>
            <p class="text-2xl font-bold text-blue-600">{{ stats.utilisations_aujourd_hui || 0 }}</p>
          </div>
          <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
            <Icon name="clock" class="w-6 h-6 text-blue-600" />
          </div>
        </div>
      </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
      <div class="flex flex-col lg:flex-row gap-4">
        <div class="flex-1">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ t('badges.search.label') }}</label>
          <div class="relative">
            <Icon name="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 dark:text-gray-500" />
            <input
              v-model="searchTerm"
              type="text"
              :placeholder="t('badges.search.placeholder')"
              class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              @input="debouncedSearch"
            />
          </div>
        </div>
        
        <div class="lg:w-48">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ t('badges.filter.status') }}</label>
          <select
            v-model="statusFilter"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            @change="loadBadges"
          >
            <option value="">{{ t('badges.filter.allStatus') }}</option>
            <option value="actif">{{ t('badges.status.active') }}</option>
            <option value="inactif">{{ t('badges.status.inactive') }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Liste des badges -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ t('badges.list.title') }}</h3>
      </div>
      
      <div v-if="loading" class="p-8 text-center">
        <div class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400">
          <Icon name="refresh" class="w-5 h-5 animate-spin" />
          {{ t('common.loading') }}
        </div>
      </div>
      
      <div v-else-if="badges.length === 0" class="p-8 text-center text-gray-500 dark:text-gray-400">
        <Icon name="identification" class="w-12 h-12 mx-auto mb-4 text-gray-300" />
        <p>{{ t('badges.list.empty') }}</p>
      </div>
      
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ t('badges.table.number') }}
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ t('badges.table.user') }}
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ t('badges.table.rights') }}
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ t('badges.table.status') }}
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ t('badges.table.lastUse') }}
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ t('badges.table.actions') }}
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
            <tr
              v-for="badge in badges"
              :key="badge.numero"
              class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-150"
              @click="viewBadge(badge)"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center gap-2">
                  <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                    <span class="text-white font-bold text-sm">{{ badge.numero }}</span>
                  </div>
                  <div>
                    <div class="font-medium text-gray-900 dark:text-white">#{{ badge.numero }}</div>
                  </div>
                </div>
              </td>
              
              <td class="px-6 py-4">
                <div>
                  <div class="font-medium text-gray-900 dark:text-white">
                    {{ badge.utilisateur?.nom_complet || (badge.utilisateur_id ? t('badges.assignedUnknown') : t('badges.unassigned')) }}
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">{{ badge.utilisateur?.email || '-' }}</div>
                  <div v-if="!badge.utilisateur_id" class="text-xs text-blue-600 font-medium">{{ t('badges.availableForAssignment') }}</div>
                </div>
              </td>
              
              <td class="px-6 py-4">
                <div>
                  <div class="font-medium text-gray-900 dark:text-white">{{ badge.droit }}</div>
                  <div v-if="badge.commentaire" class="text-sm text-gray-500 dark:text-gray-400">{{ badge.commentaire }}</div>
                </div>
              </td>
              
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="getStatusClass(badge.statut_actuel || badge.statut)"
                  class="inline-flex px-2 py-1 rounded-full text-xs font-medium"
                >
                  {{ getStatusLabel(badge.statut_actuel || badge.statut) }}
                </span>
              </td>
              
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ badge.derniere_utilisation ? formatDate(badge.derniere_utilisation) : t('common.never') }}
              </td>
              
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click.stop="viewBadge(badge)"
                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-150"
                    :title="t('badges.actions.view')"
                  >
                    <Icon name="eye" class="w-4 h-4" />
                  </button>
                  
                  <button
                    @click.stop="editBadge(badge)"
                    class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors duration-150"
                    :title="t('badges.actions.edit')"
                  >
                    <Icon name="pencil" class="w-4 h-4" />
                  </button>
                  
                  <button
                    @click.stop="toggleStatus(badge)"
                    :class="badge.statut_actuel === 'actif' ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50'"
                    class="p-2 rounded-lg transition-colors duration-150"
                    :title="badge.statut_actuel === 'actif' ? t('badges.actions.deactivate') : t('badges.actions.activate')"
                  >
                    <Icon :name="badge.statut_actuel === 'actif' ? 'stop' : 'play'" class="w-4 h-4" />
                  </button>
                  
                  <button
                    @click.stop="deleteBadge(badge)"
                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150"
                    :title="t('badges.actions.delete')"
                  >
                    <Icon name="trash" class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Activités récentes -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ t('badges.activities.title') }}</h3>
      </div>
      
      <div v-if="stats.dernieres_activites && stats.dernieres_activites.length > 0" class="divide-y divide-gray-200">
        <div
          v-for="activite in stats.dernieres_activites"
          :key="activite.id"
          class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-900 transition-colors duration-150"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div :class="getActivityColorClass(activite.action_color)" class="w-3 h-3 rounded-full"></div>
              <div>
                <p class="font-medium text-gray-900 dark:text-white">
                  {{ activite.action_libelle }} - Badge #{{ activite.badge_numero }}
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ activite.utilisateur_nom }}</p>
                <p v-if="activite.message" class="text-sm text-gray-500 dark:text-gray-400">{{ activite.message }}</p>
              </div>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
              {{ formatDate(activite.date_heure) }}
            </div>
          </div>
        </div>
      </div>
      
      <div v-else class="p-8 text-center text-gray-500 dark:text-gray-400">
        <Icon name="clock" class="w-12 h-12 mx-auto mb-4 text-gray-300" />
        <p>{{ t('badges.activities.empty') }}</p>
      </div>
    </div>

    <!-- Modal de création/modification -->
    <BadgeFormModal
      v-if="showCreateModal || showEditModal"
      :badge="editingBadge"
      :is-editing="showEditModal"
      @close="closeModal"
      @saved="handleBadgeSaved"
    />

    <!-- Modal de détails -->
    <BadgeDetailModal
      v-if="showDetailModal"
      :badge="viewingBadge"
      @close="showDetailModal = false"
      @edit="editBadge"
      @toggle-status="toggleStatus"
      @delete="deleteBadge"
    />
  </div>
</template>

<script setup lang="ts">
  // Import des composants modals
  import BadgeFormModal from '~/components/badge/BadgeFormModal.vue'
  import BadgeDetailModal from '~/components/dashboard/BadgeDetailModal.vue'
  
  // Types
  interface Badge {
    id: number
    numero: string
    statut: 'actif' | 'inactif' | 'inconnu' | 'suspendu' | 'en_attente'
    statut_actuel?: 'actif' | 'inactif' | 'inconnu' | 'suspendu' | 'en_attente'
    utilisateur_id?: number | null
    personne?: {
      nom: string
      prenom: string
    } | null
    utilisateur?: {
      nom_complet: string
      email: string
    } | null
    droit?: string
    commentaire?: string
    zone_acces?: string
    niveau_securite?: string
    attributions_count?: number
    dernier_acces?: string | null
    derniere_utilisation?: string | null
    date_derniere_utilisation?: string | null
    created_at: string
    updated_at: string
  }

  interface Activity {
    id: number
    action_libelle: string
    action_color: string
    badge_numero: string
    utilisateur_nom: string
    message?: string
    date_heure: string
  }

  interface Stats {
    total: number
    actifs: number
    inactifs: number
    assignes: number
    total_badges: number
    badges_actifs: number
    badges_inactifs: number
    badges_suspendus: number
    badges_affectes: number
    utilisations_aujourd_hui: number
    dernieres_activites: Activity[]
  }

  // Nuxt auto-imports
  const { t } = useI18n()
  const { showNotification, showSuccess, showError } = useNotifications()
  const { initTheme } = useTheme()
  const { apiCall } = useApiClient()

  // Function debounce simple
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

  // États réactifs
  const badges = ref<Badge[]>([])
  const stats = ref<Stats>({
    total: 0,
    actifs: 0,
    inactifs: 0,
    assignes: 0,
    total_badges: 0,
    badges_actifs: 0,
    badges_inactifs: 0,
    badges_suspendus: 0,
    badges_affectes: 0,
    utilisations_aujourd_hui: 0,
    dernieres_activites: []
  })
  
  const loading = ref(false)
  const searchTerm = ref('')
  const statusFilter = ref('')

  // Modales
  const showCreateModal = ref(false)
  const showEditModal = ref(false)
  const showDetailModal = ref(false)
  const editingBadge = ref<Badge | null>(null)
  const viewingBadge = ref<Badge | null>(null)

  // Recherche avec debounce
  const debouncedSearch = debounce(() => {
    loadBadges()
  }, 300)

  // Méthodes
  const loadBadges = async () => {
    loading.value = true
    try {
      const params = new URLSearchParams()
      if (searchTerm.value) params.append('search', searchTerm.value)
      if (statusFilter.value) params.append('statut', statusFilter.value)

      const response = await apiCall(`/api/badges?${params.toString()}`)
      if (response.success) {
        badges.value = response.data
      }
    } catch (error) {
      console.error('Erreur lors du chargement des badges:', error)
      showError(t('badges.errors.loadFailed'))
    } finally {
      loading.value = false
    }
  }

  const loadStats = async () => {
    try {
      const response = await apiCall('/api/badges/stats')
      if (response.success) {
        // Mise à jour correcte des stats réactives
        Object.assign(stats.value, response.data)
      }
    } catch (error) {
      console.error('Erreur lors du chargement des statistiques:', error)
    }
  }

  const refreshData = async () => {
    await Promise.all([loadBadges(), loadStats()])
  }

  const viewBadge = (badge: Badge) => {
    viewingBadge.value = badge
    showDetailModal.value = true
  }

  const editBadge = (badge: Badge) => {
    editingBadge.value = { ...badge }
    showEditModal.value = true
    showDetailModal.value = false
  }

  const deleteBadge = async (badge: Badge) => {
    if (!confirm(t('badges.confirmDelete', { numero: badge.numero }))) {
      return
    }

    try {
      const response = await apiCall(`/api/badges/${badge.numero}`, 'DELETE')

      if (response.success) {
        showSuccess(t('badges.deleteSuccess'))
        await refreshData()
      } else {
        showError(response.message || t('badges.errors.deleteFailed'))
      }
    } catch (error) {
      console.error('Erreur lors de la suppression:', error)
      showError(t('badges.errors.deleteFailed'))
    }
  }

  const toggleStatus = async (badge: Badge) => {
    try {
      const response = await apiCall(`/api/badges/${badge.numero}/toggle`, 'POST')

      if (response.success) {
        const action = response.data.nouveau_statut === 'actif' ? 'activated' : 'deactivated'
        showSuccess(t(`badges.${action}Success`))
        await refreshData()
      } else {
        showError(response.message || t('badges.errors.toggleFailed'))
      }
    } catch (error) {
      console.error('Erreur lors du changement de statut:', error)
      showError(t('badges.errors.toggleFailed'))
    }
  }

  const closeModal = () => {
    showCreateModal.value = false
    showEditModal.value = false
    editingBadge.value = null
  }

  const handleBadgeSaved = () => {
    closeModal()
    refreshData()
  }

  // Utilitaires
  const getStatusClass = (status: string) => {
    const classes = {
      'actif': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
      'inactif': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
      'inconnu': 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
      'suspendu': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
      'en_attente': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
    }
    return (classes as any)[status] || classes['inconnu']
  }

  const getStatusLabel = (status: string) => {
    const labels = {
      'actif': t('badges.status.active'),
      'inactif': t('badges.status.inactive'),
      'inconnu': t('badges.status.unknown'),
      'suspendu': t('badges.status.suspended'),
      'en_attente': t('badges.status.pending')
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

  // Lifecycle
  onMounted(async () => {
    initTheme()
    await refreshData()
  })
</script>

<style scoped>
  .btn-primary {
    background: linear-gradient(to right, rgb(37 99 235), rgb(29 78 216));
    color: white;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    transition: all 0.2s;
  }

  .btn-primary:hover {
    background: linear-gradient(to right, rgb(29 78 216), rgb(30 64 175));
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    transform: translateY(-1px);
  }

  .btn-secondary {
    background: white;
    color: rgb(55 65 81);
    border: 1px solid rgb(209 213 219);
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    transition: all 0.2s;
  }

  .btn-secondary:hover {
    background: rgb(249 250 251);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  }
</style>


