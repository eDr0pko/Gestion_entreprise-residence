<template>
  <div class="space-y-6">
    <!-- En-tête -->
    <div class="flex flex-col space-y-4 lg:flex-row lg:items-center lg:justify-between lg:space-y-0">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight lg:text-3xl">
          {{ t('badges.title') }}
        </h1>
        <p class="mt-1 text-sm text-gray-600 lg:mt-2 lg:text-base">
          {{ t('badges.description') }}
        </p>
      </div>
      <div class="flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-3 lg:mt-0">
        <button
          @click="refreshBadges"
          :disabled="loading"
          class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 transition-colors"
        >
          <RefreshIcon class="w-4 h-4 mr-2" :class="{ 'animate-spin': loading }" />
          {{ t('common.refresh') }}
        </button>
        <button
          @click="openCreateModal"
          class="inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all shadow-sm"
        >
          <PlusIcon class="w-4 h-4 mr-2" />
          {{ t('badges.newBadge') }}
        </button>
      </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:gap-6">
      <StatCard 
        :title="t('badges.stats.total')"
        :value="stats.total_badges"
        :loading="loadingStats"
        icon="badge"
        color="blue"
      />
      <StatCard 
        :title="t('badges.stats.active')"
        :value="stats.badges_actifs"
        :loading="loadingStats"
        icon="check-circle"
        color="green"
      />
      <StatCard 
        :title="t('badges.stats.inactive')"
        :value="stats.badges_inactifs"
        :loading="loadingStats"
        icon="x-circle"
        color="red"
      />
      <StatCard 
        :title="t('badges.stats.suspended')"
        :value="stats.badges_suspendus"
        :loading="loadingStats"
        icon="pause-circle"
        color="yellow"
      />
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 lg:p-6">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ t('common.search') }}
          </label>
          <div class="relative">
            <SearchIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
            <input
              v-model="filters.search"
              type="text"
              :placeholder="t('badges.searchPlaceholder')"
              class="pl-10 w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
              @input="debouncedSearch"
            />
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ t('badges.statusLabel') }}
          </label>
          <select
            v-model="filters.statut"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
            @change="() => loadBadges()"
          >
            <option value="">{{ t('badges.allStatuses') }}</option>
            <option value="actif">{{ t('badges.status.active') }}</option>
            <option value="inactif">{{ t('badges.status.inactive') }}</option>
            <option value="suspendu">{{ t('badges.status.suspended') }}</option>
            <option value="en_attente">{{ t('badges.status.pending') }}</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ t('badges.sortBy') }}
          </label>
          <select
            v-model="filters.sort_by"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
            @change="() => loadBadges()"
          >
            <option value="numero">{{ t('badges.sortOptions.number') }}</option>
            <option value="personne">{{ t('badges.sortOptions.person') }}</option>
            <option value="droit">{{ t('badges.sortOptions.rights') }}</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            {{ t('common.order') }}
          </label>
          <select
            v-model="filters.sort_order"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
            @change="() => loadBadges()"
          >
            <option value="desc">{{ t('common.descending') }}</option>
            <option value="asc">{{ t('common.ascending') }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Liste des badges -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">{{ t('badges.badgesList') }}</h2>
      </div>
      
      <div v-if="loading" class="p-8 text-center">
        <LoadingSpinner />
        <p class="mt-2 text-sm text-gray-600 lg:text-base">{{ t('common.loading') }}</p>
      </div>

      <div v-else-if="badges.length === 0" class="p-8 text-center">
        <BadgeIcon class="mx-auto w-12 h-12 text-gray-400" />
        <p class="mt-2 text-sm text-gray-600 lg:text-base">{{ t('badges.noBadges') }}</p>
      </div>

      <div v-else class="divide-y divide-gray-200">
        <div
          v-for="badge in badges"
          :key="badge.numero"
          class="p-4 hover:bg-gray-50 transition-colors cursor-pointer lg:p-6"
          @click="openBadgeDetails(badge)"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3 lg:space-x-4 min-w-0 flex-1">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 flex items-center justify-center text-white font-bold text-sm lg:text-base">
                  {{ badge.numero }}
                </div>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex flex-col space-y-1 lg:flex-row lg:items-center lg:space-y-0 lg:space-x-2">
                  <h3 class="text-sm font-medium text-gray-900 truncate lg:text-lg">
                    {{ badge.utilisateur ? badge.utilisateur.nom_complet : (badge.utilisateur_id ? t('badges.assignedUnknown') : t('badges.unassigned')) }}
                  </h3>
                  <StatusBadge :status="badge.statut" />
                </div>
                <p class="text-xs text-gray-600 truncate lg:text-sm">{{ badge.utilisateur?.email || (badge.utilisateur_id ? '-' : t('badges.availableForAssignment')) }}</p>
                <p class="text-xs text-gray-500 mt-1 lg:text-sm">{{ badge.droit }}</p>
                <p v-if="badge.commentaire" class="text-xs text-gray-500 italic mt-1 lg:text-sm">{{ badge.commentaire }}</p>
              </div>
            </div>
            <div class="flex items-center space-x-1 lg:space-x-2 ml-2">
              <!-- Bouton Détails - toujours visible avec design amélioré -->
              <button
                @click.stop="openBadgeDetails(badge)"
                class="inline-flex items-center px-2 py-1.5 lg:px-3 lg:py-2 text-xs lg:text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:border-blue-300 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                :title="t('badges.actions.view')"
              >
                <EyeIcon class="w-3 h-3 lg:w-4 lg:h-4 mr-1" />
                <span class="hidden sm:inline">{{ t('badges.actions.view') }}</span>
              </button>
              
              <!-- Bouton Activation/Désactivation avec design amélioré -->
              <button
                v-if="badge.statut === 'inactif'"
                @click.stop="activerBadge(badge)"
                class="inline-flex items-center px-2 py-1.5 lg:px-3 lg:py-2 text-xs lg:text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 hover:border-green-300 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1"
                :title="t('badges.actions.activate')"
              >
                <PlayIcon class="w-3 h-3 lg:w-4 lg:h-4 mr-1" />
                <span class="hidden md:inline">{{ t('badges.actions.activate') }}</span>
              </button>
              <button
                v-else-if="badge.statut === 'actif'"
                @click.stop="desactiverBadge(badge)"
                class="inline-flex items-center px-2 py-1.5 lg:px-3 lg:py-2 text-xs lg:text-sm font-medium text-orange-700 bg-orange-50 border border-orange-200 rounded-lg hover:bg-orange-100 hover:border-orange-300 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-1"
                :title="t('badges.actions.deactivate')"
              >
                <PauseIcon class="w-3 h-3 lg:w-4 lg:h-4 mr-1" />
                <span class="hidden md:inline">{{ t('badges.actions.deactivate') }}</span>
              </button>
              
              <!-- Bouton Modifier avec design amélioré -->
              <button
                @click.stop="openEditModal(badge)"
                class="inline-flex items-center px-2 py-1.5 lg:px-3 lg:py-2 text-xs lg:text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 hover:border-indigo-300 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                :title="t('badges.actions.edit')"
              >
                <PencilIcon class="w-3 h-3 lg:w-4 lg:h-4 mr-1" />
                <span class="hidden lg:inline">{{ t('badges.actions.edit') }}</span>
              </button>
              
              <!-- Bouton Supprimer avec design amélioré -->
              <button
                @click.stop="deleteBadge(badge)"
                class="inline-flex items-center px-2 py-1.5 lg:px-3 lg:py-2 text-xs lg:text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1"
                :title="t('badges.actions.delete')"
              >
                <TrashIcon class="w-3 h-3 lg:w-4 lg:h-4 mr-1" />
                <span class="hidden lg:inline">{{ t('badges.actions.delete') }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.total > pagination.per_page" class="px-4 py-4 border-t border-gray-200 lg:px-6">
        <Pagination
          :current-page="pagination.current_page"
          :total-pages="pagination.last_page"
          :total-items="pagination.total"
          :per-page="pagination.per_page"
          @page-changed="changePage"
        />
      </div>
    </div>

    <!-- Modals -->
    <BadgeFormModal
      v-if="showCreateModal"
      :is-edit="false"
      @close="closeCreateModal"
      @saved="onBadgeSaved"
    />

    <BadgeFormModal
      v-if="showEditModal"
      :is-edit="true"
      :badge="selectedBadge"
      @close="closeEditModal"
      @saved="onBadgeSaved"
    />

    <BadgeDetailsModal
      v-if="showDetailsModal"
      :badge="selectedBadge"
      @close="closeDetailsModal"
      @updated="onBadgeUpdated"
    />
  </div>
</template>

<script setup lang="ts">
  import { ref, onMounted, computed } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useNotification } from '~/composables/useNotification'
  import { useApiClient } from '~/composables/useApiClient'

  // Import des composants
  import StatCard from '~/components/ui/StatCard.vue'
  import StatusBadge from '~/components/ui/StatusBadge.vue'
  import LoadingSpinner from '~/components/ui/LoadingSpinner.vue'
  import Pagination from '~/components/ui/Pagination.vue'
  import BadgeFormModal from '~/components/badge/BadgeFormModal.vue'
  import BadgeDetailsModal from '~/components/badge/BadgeDetailsModal.vue'

  // Import des icônes
  import PlusIcon from '~/components/icons/PlusIcon.vue'
  import RefreshIcon from '~/components/icons/RefreshIcon.vue'
  import SearchIcon from '~/components/icons/SearchIcon.vue'
  import EyeIcon from '~/components/icons/EyeIcon.vue'
  import PencilIcon from '~/components/icons/PencilIcon.vue'
  import TrashIcon from '~/components/icons/TrashIcon.vue'
  import PlayIcon from '~/components/icons/PlayIcon.vue'
  import PauseIcon from '~/components/icons/PauseIcon.vue'
  import BadgeIcon from '~/components/icons/BadgeIcon.vue'

  const { t } = useI18n()
  const { showNotification } = useNotification()
  const { apiCall } = useApiClient()

  // État
  const loading = ref(false)
  const loadingStats = ref(false)
  const badges = ref<any[]>([])
  const stats = ref({
    total_badges: 0,
    badges_actifs: 0,
    badges_inactifs: 0,
    badges_suspendus: 0
  })

  const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0
  })

  const filters = ref({
    search: '',
    statut: '',
    sort_by: 'numero',
    sort_order: 'desc'
  })

  // Modals
  const showCreateModal = ref(false)
  const showEditModal = ref(false)
  const showDetailsModal = ref(false)
  const selectedBadge = ref<any>(null)

  // Méthodes
  const loadBadges = async (page = 1) => {
    try {
      loading.value = true
      const params = {
        page,
        per_page: pagination.value.per_page,
        ...filters.value
      }
      
      const response = await apiCall('/badges', 'GET', null, params)
      
      if (response.success) {
        // Adapter la structure de données si nécessaire
        if (response.data.data) {
          // Structure avec pagination
          badges.value = response.data.data || []
          pagination.value = {
            current_page: response.data.current_page || 1,
            last_page: response.data.last_page || 1,
            per_page: response.data.per_page || 15,
            total: response.data.total || 0
          }
        } else {
          // Structure simple
          badges.value = response.data || []
        }
      } else {
        console.error('Erreur badges response:', response)
        badges.value = []
      }
    } catch (error) {
      console.error('Erreur lors du chargement des badges:', error)
      showNotification(t('badges.errors.loadFailed'), 'error')
      badges.value = []
    } finally {
      loading.value = false
    }
  }

  const loadStats = async () => {
    try {
      loadingStats.value = true
      const response = await apiCall('/badges/stats')
      
      if (response.success) {
        stats.value = response.data
      } else {
        console.error('Erreur stats response:', response)
      }
    } catch (error) {
      console.error('Erreur lors du chargement des statistiques:', error)
      // Essayer l'endpoint alternatif
      try {
        const response = await apiCall('/badges/statistiques')
        if (response.success) {
          stats.value = response.data
        }
      } catch (error2) {
        console.error('Erreur sur endpoint alternatif:', error2)
      }
    } finally {
      loadingStats.value = false
    }
  }

  const refreshBadges = () => {
    loadBadges(pagination.value.current_page)
    loadStats()
  }

  const debouncedSearch = () => {
    loadBadges(1)
  }

  const changePage = (page: number) => {
    loadBadges(page)
  }

  // Actions sur les badges
  const activerBadge = async (badge: any) => {
    try {
      const response = await apiCall(`/badges/${badge.numero}/toggle-status`, 'POST')
      
      if (response.success) {
        showNotification(t('badges.messages.activated'), 'success')
        refreshBadges()
      }
    } catch (error) {
      console.error('Erreur lors de l\'activation:', error)
      showNotification(t('badges.errors.activateFailed'), 'error')
    }
  }

  const desactiverBadge = async (badge: any) => {
    try {
      const response = await apiCall(`/badges/${badge.numero}/toggle-status`, 'POST')
      
      if (response.success) {
        showNotification(t('badges.messages.deactivated'), 'success')
        refreshBadges()
      }
    } catch (error) {
      console.error('Erreur lors de la désactivation:', error)
      showNotification(t('badges.errors.deactivateFailed'), 'error')
    }
  }

  const deleteBadge = async (badge: any) => {
    if (!confirm(t('badges.confirmDelete'))) return
    
    try {
      const response = await apiCall(`/badges/${badge.numero}`, 'DELETE')
      
      if (response.success) {
        showNotification(t('badges.messages.deleted'), 'success')
        refreshBadges()
      }
    } catch (error) {
      console.error('Erreur lors de la suppression:', error)
      showNotification(t('badges.errors.deleteFailed'), 'error')
    }
  }

  // Gestion des modals
  const openCreateModal = () => {
    showCreateModal.value = true
  }

  const closeCreateModal = () => {
    showCreateModal.value = false
  }

  const openEditModal = (badge: any) => {
    selectedBadge.value = badge
    showEditModal.value = true
  }

  const closeEditModal = () => {
    showEditModal.value = false
    selectedBadge.value = null
  }

  const openBadgeDetails = (badge: any) => {
    selectedBadge.value = badge
    showDetailsModal.value = true
  }

  const closeDetailsModal = () => {
    showDetailsModal.value = false
    selectedBadge.value = null
  }

  const onBadgeUpdated = (updatedBadge: any) => {
    showDetailsModal.value = false
    refreshBadges()
    showNotification(t('badges.updateSuccess'), 'success')
  }

  const onBadgeSaved = () => {
    refreshBadges()
  }

  // Lifecycle
  onMounted(async () => {
    console.log('🔍 Chargement initial des badges et statistiques...')
    await Promise.all([loadBadges(), loadStats()])
    console.log('📊 Stats chargées:', stats.value)
    console.log('🏷️ Badges chargés:', badges.value.length, 'badges')
  })
</script>


