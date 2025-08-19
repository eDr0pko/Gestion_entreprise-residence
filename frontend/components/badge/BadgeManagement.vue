<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 p-6">
    <div class="max-w-7xl mx-auto space-y-8">
      <!-- En-tête moderne -->
      <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl border border-white/20 p-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
          <div class="space-y-2">
            <div class="flex items-center gap-3">
              <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                <BadgeIcon class="w-8 h-8 text-white" />
              </div>
              <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                  {{ t('badges.title') }}
                </h1>
                <p class="text-gray-600 font-medium">{{ t('badges.description') }}</p>
              </div>
            </div>
          </div>
          
          <div class="flex flex-col sm:flex-row gap-3">
            <button
              @click="refreshData"
              :disabled="loading"
              class="group px-6 py-3 bg-white border border-gray-200 rounded-2xl font-semibold text-gray-700 hover:bg-gray-50 transition-all duration-200 shadow-sm flex items-center gap-2"
            >
              <RefreshIcon class="w-5 h-5 transition-transform group-hover:rotate-180" :class="{ 'animate-spin': loading }" />
              {{ t('common.refresh') }}
            </button>
            <button
              @click="openCreateModal"
              class="group px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-2"
            >
              <PlusIcon class="w-5 h-5 group-hover:rotate-90 transition-transform duration-200" />
              {{ t('badges.newBadge') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Statistiques modernes -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="group bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div class="space-y-1">
              <p class="text-sm font-medium text-gray-600">{{ t('badges.stats.total') }}</p>
              <p class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                {{ stats.total_badges || 0 }}
              </p>
            </div>
            <div class="p-3 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl group-hover:scale-110 transition-transform duration-300">
              <BadgeIcon class="w-7 h-7 text-blue-600" />
            </div>
          </div>
          <div class="mt-4 flex items-center gap-2">
            <div class="h-2 bg-gray-200 rounded-full flex-1">
              <div class="h-2 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full w-full"></div>
            </div>
          </div>
        </div>

        <div class="group bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div class="space-y-1">
              <p class="text-sm font-medium text-gray-600">{{ t('badges.stats.active') }}</p>
              <p class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                {{ stats.badges_actifs || 0 }}
              </p>
            </div>
            <div class="p-3 bg-gradient-to-br from-green-100 to-emerald-100 rounded-2xl group-hover:scale-110 transition-transform duration-300">
              <CheckCircleIcon class="w-7 h-7 text-green-600" />
            </div>
          </div>
          <div class="mt-4 flex items-center gap-2">
            <div class="h-2 bg-gray-200 rounded-full flex-1">
              <div class="h-2 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full" 
                   :style="{ width: getPercentage(stats.badges_actifs, stats.total_badges) + '%' }"></div>
            </div>
            <span class="text-xs text-gray-500 font-medium">
              {{ getPercentage(stats.badges_actifs, stats.total_badges) }}%
            </span>
          </div>
        </div>

        <div class="group bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div class="space-y-1">
              <p class="text-sm font-medium text-gray-600">{{ t('badges.stats.inactive') }}</p>
              <p class="text-3xl font-bold bg-gradient-to-r from-red-600 to-rose-600 bg-clip-text text-transparent">
                {{ stats.badges_inactifs || 0 }}
              </p>
            </div>
            <div class="p-3 bg-gradient-to-br from-red-100 to-rose-100 rounded-2xl group-hover:scale-110 transition-transform duration-300">
              <XCircleIcon class="w-7 h-7 text-red-600" />
            </div>
          </div>
          <div class="mt-4 flex items-center gap-2">
            <div class="h-2 bg-gray-200 rounded-full flex-1">
              <div class="h-2 bg-gradient-to-r from-red-500 to-rose-500 rounded-full" 
                   :style="{ width: getPercentage(stats.badges_inactifs, stats.total_badges) + '%' }"></div>
            </div>
            <span class="text-xs text-gray-500 font-medium">
              {{ getPercentage(stats.badges_inactifs, stats.total_badges) }}%
            </span>
          </div>
        </div>

        <div class="group bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6 hover:shadow-xl transition-all duration-300">
          <div class="flex items-center justify-between">
            <div class="space-y-1">
              <p class="text-sm font-medium text-gray-600">{{ t('badges.stats.suspended') }}</p>
              <p class="text-3xl font-bold bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent">
                {{ stats.badges_suspendus || 0 }}
              </p>
            </div>
            <div class="p-3 bg-gradient-to-br from-amber-100 to-orange-100 rounded-2xl group-hover:scale-110 transition-transform duration-300">
              <PauseCircleIcon class="w-7 h-7 text-amber-600" />
            </div>
          </div>
          <div class="mt-4 flex items-center gap-2">
            <div class="h-2 bg-gray-200 rounded-full flex-1">
              <div class="h-2 bg-gradient-to-r from-amber-500 to-orange-500 rounded-full" 
                   :style="{ width: getPercentage(stats.badges_suspendus, stats.total_badges) + '%' }"></div>
            </div>
            <span class="text-xs text-gray-500 font-medium">
              {{ getPercentage(stats.badges_suspendus, stats.total_badges) }}%
            </span>
          </div>
        </div>
      </div>

      <!-- Filtres et recherche modernes -->
      <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">{{ t('common.search') }}</label>
            <div class="relative">
              <SearchIcon class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />
              <input
                v-model="filters.search"
                type="text"
                :placeholder="t('badges.searchPlaceholder')"
                class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white/80"
                @input="debouncedSearch"
              />
            </div>
          </div>
          
          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">{{ t('badges.statusLabel') }}</label>
            <select
              v-model="filters.statut"
              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white/80"
              @change="() => loadBadges()"
            >
              <option value="">{{ t('badges.allStatuses') }}</option>
              <option value="actif">{{ t('badges.status.active') }}</option>
              <option value="inactif">{{ t('badges.status.inactive') }}</option>
              <option value="suspendu">{{ t('badges.status.suspended') }}</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">{{ t('badges.sortBy') }}</label>
            <select
              v-model="filters.sort_by"
              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white/80"
              @change="() => loadBadges()"
            >
              <option value="numero">{{ t('badges.sortOptions.number') }}</option>
              <option value="personne">{{ t('badges.sortOptions.person') }}</option>
              <option value="droit">{{ t('badges.sortOptions.rights') }}</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="block text-sm font-semibold text-gray-700">{{ t('common.order') }}</label>
            <select
              v-model="filters.sort_order"
              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white/80"
              @change="() => loadBadges()"
            >
              <option value="desc">{{ t('common.descending') }}</option>
              <option value="asc">{{ t('common.ascending') }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Liste des badges moderne -->
      <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-white/20 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">{{ t('badges.badgesList') }}</h2>
            <div class="flex items-center gap-2 text-sm text-gray-600">
              <span>{{ badges.length }}</span>
              <span>{{ t('badges.badgesList').toLowerCase() }}</span>
            </div>
          </div>
        </div>
        
        <div v-if="loading" class="p-12 text-center">
          <div class="inline-flex items-center gap-3 text-gray-600">
            <div class="w-8 h-8 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
            <span class="font-medium">{{ t('badges.loading') }}</span>
          </div>
        </div>

        <div v-else-if="badges.length === 0" class="p-12 text-center">
          <div class="space-y-4">
            <div class="p-4 bg-gray-100 rounded-full w-fit mx-auto">
              <BadgeIcon class="w-12 h-12 text-gray-400" />
            </div>
            <p class="text-gray-600 font-medium">{{ t('badges.noBadges') }}</p>
          </div>
        </div>

        <div v-else class="divide-y divide-gray-100">
          <div
            v-for="badge in badges"
            :key="badge.numero"
            class="group p-6 hover:bg-blue-50/50 transition-all duration-200 cursor-pointer"
            @click="openBadgeDetails(badge)"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-4 flex-1">
                <div class="relative">
                  <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                    {{ badge.numero }}
                  </div>
                  <div 
                    :class="getStatusDotClass(badge.statut_actuel)"
                    class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white"
                  ></div>
                </div>
                
                <div class="flex-1 min-w-0 space-y-1">
                  <div class="flex items-center gap-3">
                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                      {{ badge.utilisateur?.nom_complet || t('badges.unassigned') }}
                    </h3>
                    <span 
                      :class="getStatusBadgeClass(badge.statut_actuel)"
                      class="px-3 py-1 rounded-full text-xs font-semibold"
                    >
                      {{ getStatusLabel(badge.statut_actuel) }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-600 truncate">{{ badge.utilisateur?.email || '-' }}</p>
                  <div class="flex items-center gap-4 text-sm text-gray-500">
                    <span class="flex items-center gap-1">
                      <KeyIcon class="w-4 h-4" />
                      {{ badge.droit }}
                    </span>
                    <span v-if="badge.derniere_utilisation" class="flex items-center gap-1">
                      <ClockIcon class="w-4 h-4" />
                      {{ formatDate(badge.derniere_utilisation) }}
                    </span>
                    <span v-else class="flex items-center gap-1 text-gray-400">
                      <ClockIcon class="w-4 h-4" />
                      {{ t('common.never') }}
                    </span>
                  </div>
                </div>
              </div>
              
              <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                <button
                  v-if="badge.statut_actuel === 'inactif'"
                  @click.stop="toggleStatus(badge, 'activate')"
                  class="p-2 text-green-600 hover:bg-green-100 rounded-xl transition-colors duration-200"
                  :title="t('badges.actions.activate')"
                >
                  <PlayIcon class="w-5 h-5" />
                </button>
                <button
                  v-else-if="badge.statut_actuel === 'actif'"
                  @click.stop="toggleStatus(badge, 'deactivate')"
                  class="p-2 text-red-600 hover:bg-red-100 rounded-xl transition-colors duration-200"
                  :title="t('badges.actions.deactivate')"
                >
                  <PauseIcon class="w-5 h-5" />
                </button>
                <button
                  v-else-if="badge.statut_actuel === 'suspendu'"
                  @click.stop="toggleStatus(badge, 'activate')"
                  class="p-2 text-green-600 hover:bg-green-100 rounded-xl transition-colors duration-200"
                  :title="t('badges.actions.activate')"
                >
                  <PlayIcon class="w-5 h-5" />
                </button>
                
                <button
                  @click.stop="editBadge(badge)"
                  class="p-2 text-blue-600 hover:bg-blue-100 rounded-xl transition-colors duration-200"
                  :title="t('badges.actions.edit')"
                >
                  <PencilIcon class="w-5 h-5" />
                </button>
                
                <button
                  @click.stop="deleteBadge(badge)"
                  class="p-2 text-red-600 hover:bg-red-100 rounded-xl transition-colors duration-200"
                  :title="t('badges.actions.delete')"
                >
                  <TrashIcon class="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination moderne -->
        <div v-if="pagination.total > pagination.per_page" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
              {{ t('common.showing') }} {{ ((pagination.current_page - 1) * pagination.per_page) + 1 }} 
              {{ t('common.to') }} {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} 
              {{ t('common.of') }} {{ pagination.total }} {{ t('common.results').toLowerCase() }}
            </div>
            <div class="flex items-center gap-2">
              <button
                @click="changePage(pagination.current_page - 1)"
                :disabled="pagination.current_page <= 1"
                class="px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ t('common.previous') }}
              </button>
              
              <div class="flex items-center gap-1">
                <button
                  v-for="page in getVisiblePages()"
                  :key="page"
                  @click="changePage(page)"
                  :class="[
                    'px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200',
                    page === pagination.current_page
                      ? 'bg-blue-600 text-white'
                      : 'text-gray-500 hover:bg-gray-100'
                  ]"
                >
                  {{ page }}
                </button>
              </div>
              
              <button
                @click="changePage(pagination.current_page + 1)"
                :disabled="pagination.current_page >= pagination.last_page"
                class="px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ t('common.next') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <BadgeFormModal
      v-if="showCreateModal || showEditModal"
      :badge="editingBadge"
      :is-editing="showEditModal"
      @close="closeModal"
      @saved="handleBadgeSaved"
    />

    <BadgeDetailsModal
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
    import { ref, reactive, onMounted, computed } from 'vue'
    import { useI18n } from 'vue-i18n'
    import { useAuthenticatedFetch } from '~/composables/useAuthenticatedFetch'
    import { useNotifications } from '~/composables/useNotifications'

    // Import des icônes
    import BadgeIcon from '~/components/icons/BadgeIcon.vue'
    import PlusIcon from '~/components/icons/PlusIcon.vue'
    import RefreshIcon from '~/components/icons/RefreshIcon.vue'
    import SearchIcon from '~/components/icons/SearchIcon.vue'
    import CheckCircleIcon from '~/components/icons/CheckCircleIcon.vue'
    import XCircleIcon from '~/components/icons/XCircleIcon.vue'
    import PauseCircleIcon from '~/components/icons/PauseCircleIcon.vue'
    import PlayIcon from '~/components/icons/PlayIcon.vue'
    import PauseIcon from '~/components/icons/PauseIcon.vue'
    import PencilIcon from '~/components/icons/PencilIcon.vue'
    import TrashIcon from '~/components/icons/TrashIcon.vue'
    import KeyIcon from '~/components/icons/KeyIcon.vue'
    import ClockIcon from '~/components/icons/ClockIcon.vue'

    // Import des modals
    import BadgeFormModal from './BadgeFormModal.vue'
    import BadgeDetailsModal from './BadgeDetailsModal.vue'

    const { t } = useI18n()
    const { fetchWithAuth } = useAuthenticatedFetch()
    const { showSuccess, showError } = useNotifications()

    // État réactif
    const loading = ref(false)
    const badges = ref<any[]>([])
    const stats = ref({
    total_badges: 0,
    badges_actifs: 0,
    badges_inactifs: 0,
    badges_suspendus: 0,
    utilisations_aujourd_hui: 0
    })

    const pagination = ref({
    current_page: 1,
    last_page: 1,
    per_page: 15,
    total: 0
    })

    const filters = reactive({
    search: '',
    statut: '',
    sort_by: 'numero',
    sort_order: 'desc'
    })

    // Modals
    const showCreateModal = ref(false)
    const showEditModal = ref(false)
    const showDetailModal = ref(false)
    const editingBadge = ref<any>(null)
    const viewingBadge = ref<any>(null)

    // Méthodes utilitaires
    const getPercentage = (value: number, total: number) => {
    if (!total) return 0
    return Math.round((value / total) * 100)
    }

    const getStatusDotClass = (status: string) => {
    switch (status) {
        case 'actif': return 'bg-green-500'
        case 'inactif': return 'bg-red-500'
        case 'suspendu': return 'bg-amber-500'
        default: return 'bg-gray-400'
    }
    }

    const getStatusBadgeClass = (status: string) => {
    switch (status) {
        case 'actif': return 'bg-green-100 text-green-800'
        case 'inactif': return 'bg-red-100 text-red-800'
        case 'suspendu': return 'bg-amber-100 text-amber-800'
        default: return 'bg-gray-100 text-gray-800'
    }
    }

    const getStatusLabel = (status: string) => {
    switch (status) {
        case 'actif': return t('badges.status.active')
        case 'inactif': return t('badges.status.inactive')
        case 'suspendu': return t('badges.status.suspended')
        default: return t('badges.status.unknown')
    }
    }

    const formatDate = (date: string) => {
    if (!date) return t('common.never')
    return new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
    }

    const getVisiblePages = () => {
    const current = pagination.value.current_page
    const total = pagination.value.last_page
    const pages = []
    
    const start = Math.max(1, current - 2)
    const end = Math.min(total, current + 2)
    
    for (let i = start; i <= end; i++) {
        pages.push(i)
    }
    
    return pages
    }

    // Debounce pour la recherche
    let searchTimeout: NodeJS.Timeout
    const debouncedSearch = () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        loadBadges(1)
    }, 500)
    }

    // Méthodes API
    const loadBadges = async (page = 1) => {
      console.log('🏷️ [loadBadges] Début du chargement des badges, page:', page)
      try {
        loading.value = true
        const params = new URLSearchParams({
          page: page.toString(),
          per_page: pagination.value.per_page.toString(),
          ...filters
        })
        
        console.log('🏷️ [loadBadges] Paramètres:', Object.fromEntries(params))
        console.log('🏷️ [loadBadges] URL complète:', `/badges?${params}`)
        
        const response = await fetchWithAuth(`/badges?${params}`)
        console.log('🏷️ [loadBadges] Réponse brute:', response)
        
        if (response.success) {
          console.log('🏷️ [loadBadges] Success=true, données:', response.data)
          badges.value = response.data
          // Note: Ajuster selon la structure de réponse réelle
          pagination.value.current_page = page
          pagination.value.total = response.total || response.data.length
          pagination.value.last_page = Math.ceil(pagination.value.total / pagination.value.per_page)
          console.log('🏷️ [loadBadges] Pagination mise à jour:', pagination.value)
        } else {
          console.warn('🏷️ [loadBadges] Success=false:', response)
        }
      } catch (error: any) {
        console.error('🏷️ [loadBadges] ❌ Erreur lors du chargement des badges:', error)
        console.error('🏷️ [loadBadges] ❌ Détails:', {
          message: error?.message || 'Erreur inconnue',
          status: error?.status || error?.statusCode || 'Non défini',
          data: error?.data || error?.response?.data || 'Aucune donnée'
        })
        showError(t('badges.errors.loadFailed'))
      } finally {
        loading.value = false
      }
    }

    const loadStats = async () => {
      console.log('📊 [loadStats] Début du chargement des statistiques...')
      try {
        console.log('📊 [loadStats] Appel à /badges/stats...')
        const response = await fetchWithAuth('/badges/stats')
        console.log('📊 [loadStats] Réponse brute:', response)
        
        if (response.success) {
          console.log('📊 [loadStats] Réponse success=true, données:', response.data)
          stats.value = response.data
          console.log('📊 [loadStats] Stats assignées:', stats.value)
        } else {
          console.warn('📊 [loadStats] Réponse success=false:', response)
        }
      } catch (error: any) {
        console.error('📊 [loadStats] ❌ Erreur lors du chargement des statistiques:', error)
        console.error('📊 [loadStats] ❌ Détails:', {
          message: error?.message || 'Erreur inconnue',
          status: error?.status || error?.statusCode || 'Non défini',
          data: error?.data || error?.response?.data || 'Aucune donnée'
        })
      }
    }

    const refreshData = async () => {
      console.log('🔄 RefreshData démarré...')
      console.log('🔗 URLs utilisées:')
      console.log('  - Stats: /badges/stats')
      console.log('  - Badges: /badges')
      
      try {
        // Test de connectivité API
        console.log('🧪 Test de connectivité API...')
        const healthCheck = await $fetch('/api/health')
        console.log('🏥 Health check:', healthCheck)
        
        console.log('🚀 Chargement en parallèle...')
        await Promise.all([
          loadBadges(pagination.value.current_page),
          loadStats()
        ])
        console.log('✅ RefreshData terminé avec succès')
      } catch (error: any) {
        console.error('❌ Erreur dans refreshData:', error)
        console.error('❌ Détails de l\'erreur:', {
          message: error?.message || 'Erreur inconnue',
          status: error?.status || error?.statusCode || 'Non défini',
          data: error?.data || error?.response?.data || 'Aucune donnée'
        })
      }
    }

    // Actions sur les badges
    const openCreateModal = () => {
    editingBadge.value = null
    showCreateModal.value = true
    }

    const editBadge = (badge: any) => {
    editingBadge.value = { ...badge }
    showEditModal.value = true
    showDetailModal.value = false
    }

    const openBadgeDetails = (badge: any) => {
    viewingBadge.value = badge
    showDetailModal.value = true
    }

    const closeModal = () => {
    showCreateModal.value = false
    showEditModal.value = false
    editingBadge.value = null
    }

    const handleBadgeSaved = () => {
    closeModal()
    refreshData()
    showSuccess(editingBadge.value ? t('badges.updateSuccess') : t('badges.createSuccess'))
    }

    const toggleStatus = async (badge: any, action: 'activate' | 'deactivate' | 'suspend') => {
    try {
        const response = await fetchWithAuth(`/badges/${badge.numero}/toggle-status`, {
        method: 'POST',
        body: JSON.stringify({ action })
        })

        if (response.success) {
        await refreshData()
        
        const messages = {
            activate: t('badges.activatedSuccess'),
            deactivate: t('badges.deactivatedSuccess'),
            suspend: t('badges.suspendedSuccess')
        }
        
        showSuccess(messages[action])
        } else {
        showError(response.message || t('badges.errors.toggleFailed'))
        }
    } catch (error) {
        console.error('Erreur lors du changement de statut:', error)
        showError(t('badges.errors.toggleFailed'))
    }
    }

    const deleteBadge = async (badge: any) => {
    if (!confirm(t('badges.confirmDelete', { numero: badge.numero }))) {
        return
    }

    try {
        const response = await fetchWithAuth(`/badges/${badge.numero}`, {
        method: 'DELETE'
        })

        if (response.success) {
        showSuccess(t('badges.deleteSuccess'))
        await refreshData()
        showDetailModal.value = false
        } else {
        showError(response.message || t('badges.errors.deleteFailed'))
        }
    } catch (error) {
        console.error('Erreur lors de la suppression:', error)
        showError(t('badges.errors.deleteFailed'))
    }
    }

    const changePage = (page: number) => {
    if (page >= 1 && page <= pagination.value.last_page) {
        loadBadges(page)
    }
    }

    // Lifecycle
    onMounted(() => {
    refreshData()
    })
</script>

<style scoped>
    /* Animations personnalisées */
    @keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
    }

    .group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
    }

    /* Scrollbar personnalisée */
    ::-webkit-scrollbar {
    width: 6px;
    }

    ::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
    }
</style>


