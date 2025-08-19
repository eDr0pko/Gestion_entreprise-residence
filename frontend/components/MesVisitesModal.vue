<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
    @click.self="$emit('close')"
  >
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl mx-auto max-h-[90vh] overflow-hidden">
      <!-- Header -->
      <div class="bg-gradient-to-r from-teal-600 to-teal-700 text-white p-6">
        <div class="flex items-center justify-between">
          <h2 class="text-2xl font-bold flex items-center gap-3">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            {{ t('mesVisites.title', 'Mes Visites') }}
          </h2>
          <button 
            @click="$emit('close')" 
            class="text-white/80 hover:text-white text-2xl font-bold p-2 rounded-xl hover:bg-white/10 transition-colors focus:outline-none"
          >
            ×
          </button>
        </div>
        
        <!-- Stats row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 text-center">
            <div class="text-2xl font-bold">{{ totalVisites }}</div>
            <div class="text-sm text-white/80">{{ t('mesVisites.stats.total', 'Total') }}</div>
          </div>
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-yellow-200">{{ pendingVisites }}</div>
            <div class="text-sm text-white/80">{{ t('mesVisites.stats.pending', 'En attente') }}</div>
          </div>
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-green-200">{{ approvedVisites }}</div>
            <div class="text-sm text-white/80">{{ t('mesVisites.stats.approved', 'Approuvées') }}</div>
          </div>
          <div class="bg-white/10 backdrop-blur-sm rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-red-200">{{ rejectedVisites }}</div>
            <div class="text-sm text-white/80">{{ t('mesVisites.stats.rejected', 'Refusées') }}</div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="p-6 border-b border-gray-100">
        <div class="flex flex-col md:flex-row gap-4">
          <div class="flex-1">
            <input 
              v-model="searchQuery"
              type="text" 
              :placeholder="t('mesVisites.search.placeholder', 'Rechercher par motif, visiteur...')"
              class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
            />
          </div>
          <div class="flex gap-3">
            <select 
              v-model="selectedStatus"
              class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent bg-white"
            >
              <option value="">{{ t('mesVisites.filters.allStatus', 'Tous les statuts') }}</option>
              <option value="programmee">{{ t('mesVisites.status.scheduled', 'Programmée') }}</option>
              <option value="en_attente">{{ t('mesVisites.status.pending', 'En attente') }}</option>
              <option value="en_cours">{{ t('mesVisites.status.inProgress', 'En cours') }}</option>
              <option value="terminee">{{ t('mesVisites.status.completed', 'Terminée') }}</option>
              <option value="annulee">{{ t('mesVisites.status.cancelled', 'Annulée') }}</option>
              <option value="refusee">{{ t('mesVisites.status.rejected', 'Refusée') }}</option>
            </select>
            <select 
              v-model="sortBy"
              class="px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent bg-white"
            >
              <option value="date_desc">{{ t('mesVisites.sort.dateDesc', 'Plus récent') }}</option>
              <option value="date_asc">{{ t('mesVisites.sort.dateAsc', 'Plus ancien') }}</option>
              <option value="status">{{ t('mesVisites.sort.status', 'Par statut') }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div class="max-h-[50vh] overflow-y-auto">
        <div v-if="loading" class="flex items-center justify-center py-12">
          <div class="flex items-center gap-3 text-gray-500">
            <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ t('mesVisites.loading', 'Chargement...') }}
          </div>
        </div>

        <div v-else-if="error" class="flex items-center justify-center py-12">
          <div class="text-center">
            <svg class="w-12 h-12 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <p class="text-red-600 font-medium">{{ error }}</p>
            <button 
              @click="loadVisites"
              class="mt-3 px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors"
            >
              {{ t('mesVisites.retry', 'Réessayer') }}
            </button>
          </div>
        </div>

        <div v-else-if="filteredVisites.length === 0" class="flex items-center justify-center py-12">
          <div class="text-center">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p class="text-gray-500 font-medium">{{ t('mesVisites.noVisits', 'Aucune visite trouvée') }}</p>
            <p class="text-gray-400 text-sm mt-1">{{ t('mesVisites.noVisitsDesc', 'Vous n\'avez pas encore de demandes de visite') }}</p>
          </div>
        </div>

        <div v-else class="p-6">
          <div class="space-y-4">
            <div 
              v-for="visite in filteredVisites" 
              :key="visite.id_visite"
              class="bg-gradient-to-r from-white to-gray-50 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden"
              :class="getVisiteCardClass(visite.statut_visite)"
            >
              <!-- Status bar -->
              <div 
                class="h-1 w-full"
                :class="getStatusBarClass(visite.statut_visite)"
              ></div>

              <div class="p-6">
                <div class="flex items-start justify-between">
                  <div class="flex-1 min-w-0">
                    <!-- Header -->
                    <div class="flex items-center gap-3 mb-3">
                      <div 
                        class="w-3 h-3 rounded-full flex-shrink-0"
                        :class="getStatusDotClass(visite.statut_visite)"
                      ></div>
                      <h3 class="font-bold text-lg text-gray-900 truncate">
                        {{ visite.motif_visite || t('mesVisites.defaultMotif', 'Visite') }}
                      </h3>
                      <span 
                        class="px-3 py-1 rounded-full text-xs font-semibold flex-shrink-0"
                        :class="getStatusBadgeClass(visite.statut_visite)"
                      >
                        {{ getStatusText(visite.statut_visite) }}
                      </span>
                    </div>

                    <!-- Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                      <div class="space-y-2">
                        <div class="flex items-center gap-2 text-gray-600">
                          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                          </svg>
                          <span class="text-sm">
                            {{ getVisitorDisplayName(visite) }}
                          </span>
                        </div>
                        <div v-if="visite.telephone_visiteur" class="flex items-center gap-2 text-gray-600">
                          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21L6.3 10.91a11.042 11.042 0 005.9 5.9l1.523-3.924a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                          </svg>
                          <span class="text-sm">{{ visite.telephone_visiteur }}</span>
                        </div>
                      </div>
                      <div class="space-y-2">
                        <div class="flex items-center gap-2 text-gray-600">
                          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                          </svg>
                          <span class="text-sm font-medium">{{ formatDate(visite.date_visite_start) }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-600">
                          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                          </svg>
                          <span class="text-sm">{{ formatTime(visite.date_visite_start) }} - {{ formatTime(visite.date_visite_end) }}</span>
                        </div>
                      </div>
                    </div>

                    <!-- Comments -->
                    <div v-if="visite.commentaire" class="bg-gray-50 rounded-xl p-3 mb-4">
                      <p class="text-sm text-gray-600 italic">{{ visite.commentaire }}</p>
                    </div>
                  </div>
                </div>

                <!-- Actions -->
                <div 
                  v-if="canModifyStatus(visite.statut_visite)"
                  class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-200"
                >
                  <button
                    @click="changerStatut(visite.id_visite, 'programmee')"
                    class="px-4 py-2 bg-green-100 text-green-800 rounded-xl hover:bg-green-200 transition-colors text-sm font-medium flex items-center gap-2"
                    :disabled="updating"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ t('mesVisites.actions.approve', 'Approuver') }}
                  </button>
                  
                  <button
                    @click="changerStatut(visite.id_visite, 'refusee')"
                    class="px-4 py-2 bg-red-100 text-red-800 rounded-xl hover:bg-red-200 transition-colors text-sm font-medium flex items-center gap-2"
                    :disabled="updating"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    {{ t('mesVisites.actions.reject', 'Refuser') }}
                  </button>

                  <button
                    @click="showReportModal(visite)"
                    class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-xl hover:bg-yellow-200 transition-colors text-sm font-medium flex items-center gap-2"
                    :disabled="updating"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ t('mesVisites.actions.reschedule', 'Reporter') }}
                  </button>

                  <button
                    @click="showDetailsModal(visite)"
                    class="px-4 py-2 bg-blue-100 text-blue-800 rounded-xl hover:bg-blue-200 transition-colors text-sm font-medium flex items-center gap-2"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ t('mesVisites.actions.details', 'Détails') }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <p class="text-sm text-gray-500">
            {{ t('mesVisites.footer.showing', 'Affichage de') }} {{ filteredVisites.length }} {{ t('mesVisites.footer.visits', 'visite(s)') }}
          </p>
          <div class="flex gap-2">
            <button 
              @click="loadVisites"
              class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors text-sm font-medium flex items-center gap-2"
              :disabled="loading"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              {{ t('mesVisites.actions.refresh', 'Actualiser') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, onMounted, watch } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useAuthStore } from '@/stores/auth'

  const { t } = useI18n()

  const props = defineProps({
    isOpen: {
      type: Boolean,
      required: true
    }
  })

  const emit = defineEmits(['close', 'refresh'])

  const authStore = useAuthStore()
  const config = useRuntimeConfig()

  // State
  const visites = ref([])
  const loading = ref(false)
  const error = ref('')
  const updating = ref(false)

  // Filters
  const searchQuery = ref('')
  const selectedStatus = ref('')
  const sortBy = ref('date_desc')

  // Computed
  const filteredVisites = computed(() => {
    let filtered = [...visites.value]

    // Filter by search query
    if (searchQuery.value) {
      const query = searchQuery.value.toLowerCase()
      filtered = filtered.filter(visite => 
        visite.motif_visite?.toLowerCase().includes(query) ||
        visite.nom_visiteur?.toLowerCase().includes(query) ||
        visite.prenom_visiteur?.toLowerCase().includes(query) ||
        visite.email_visiteur?.toLowerCase().includes(query) ||
        visite.telephone_visiteur?.includes(query)
      )
    }

    // Filter by status
    if (selectedStatus.value) {
      filtered = filtered.filter(visite => visite.statut_visite === selectedStatus.value)
    }

    // Sort
    filtered.sort((a, b) => {
      switch (sortBy.value) {
        case 'date_asc':
          return new Date(a.date_visite_start) - new Date(b.date_visite_start)
        case 'date_desc':
          return new Date(b.date_visite_start) - new Date(a.date_visite_start)
        case 'status':
          return a.statut_visite.localeCompare(b.statut_visite)
        default:
          return new Date(b.date_visite_start) - new Date(a.date_visite_start)
      }
    })

    return filtered
  })

  const totalVisites = computed(() => visites.value.length)
  const pendingVisites = computed(() => visites.value.filter(v => v.statut_visite === 'en_attente').length)
  const approvedVisites = computed(() => visites.value.filter(v => ['programmee', 'en_cours', 'terminee'].includes(v.statut_visite)).length)
  const rejectedVisites = computed(() => visites.value.filter(v => ['annulee', 'refusee'].includes(v.statut_visite)).length)

  // Methods
  const loadVisites = async () => {
    try {
      error.value = ''
      loading.value = true

      const response = await fetch(`${config.public.apiBase}/visites`, {
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': 'application/json'
        }
      })

      const data = await response.json()

      if (!response.ok || !data.success) {
        throw new Error(data.message || t('mesVisites.errors.loadFailed', 'Erreur lors du chargement'))
      }

      // Filter visits that concern the current user
      const user = authStore.user || {}
      if (user.role === 'resident') {
        // For residents, show visits for their apartment/person
        visites.value = data.visites.filter(visite => 
          visite.id_personne_visite === user.id_personne ||
          visite.resident_id === user.id_personne
        )
      } else {
        // For guards/admins, show all visits
        visites.value = data.visites
      }
    } catch (err) {
      console.error('Error loading visits:', err)
      error.value = err.message || t('mesVisites.errors.loadFailed', 'Erreur lors du chargement des visites.')
    } finally {
      loading.value = false
    }
  }

  const changerStatut = async (id, statut) => {
    try {
      updating.value = true
      
      const response = await fetch(`${config.public.apiBase}/visites/${id}/statut`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ statut })
      })

      const data = await response.json()

      if (!response.ok || !data.success) {
        throw new Error(data.message || t('mesVisites.errors.updateFailed', 'Erreur lors du changement de statut'))
      }

      // Update local state
      const visite = visites.value.find(v => v.id_visite === id)
      if (visite) {
        visite.statut_visite = statut
      }

      // Emit refresh event to update parent
      emit('refresh')
      
    } catch (err) {
      console.error('Error updating status:', err)
      error.value = err.message || t('mesVisites.errors.updateFailed', 'Erreur lors du changement de statut.')
    } finally {
      updating.value = false
    }
  }

  // Helper methods
  const formatDate = (dateStr) => {
    if (!dateStr) return ''
    const date = new Date(dateStr)
    return date.toLocaleDateString('fr-FR', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  }

  const formatTime = (dateStr) => {
    if (!dateStr) return ''
    const date = new Date(dateStr)
    return date.toLocaleTimeString('fr-FR', {
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  const getVisitorDisplayName = (visite) => {
    if (!visite) return ''
    if (visite.nom_visiteur && visite.prenom_visiteur) {
      return `${visite.prenom_visiteur} ${visite.nom_visiteur}`
    }
    return visite.email_visiteur || t('mesVisites.unknownVisitor', 'Visiteur inconnu')
  }

  const canModifyStatus = (status) => {
    return ['en_attente', 'programmee'].includes(status)
  }

  const getStatusText = (status) => {
    const statusMap = {
      'programmee': t('mesVisites.status.scheduled', 'Programmée'),
      'en_attente': t('mesVisites.status.pending', 'En attente'),
      'en_cours': t('mesVisites.status.inProgress', 'En cours'),
      'terminee': t('mesVisites.status.completed', 'Terminée'),
      'annulee': t('mesVisites.status.cancelled', 'Annulée'),
      'refusee': t('mesVisites.status.rejected', 'Refusée')
    }
    return statusMap[status] || status
  }

  const getStatusBadgeClass = (status) => {
    const classes = {
      'programmee': 'bg-blue-100 text-blue-800',
      'en_attente': 'bg-yellow-100 text-yellow-800',
      'en_cours': 'bg-green-100 text-green-800',
      'terminee': 'bg-gray-100 text-gray-800',
      'annulee': 'bg-red-100 text-red-800',
      'refusee': 'bg-red-100 text-red-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
  }

  const getStatusDotClass = (status) => {
    const classes = {
      'programmee': 'bg-blue-500',
      'en_attente': 'bg-yellow-500',
      'en_cours': 'bg-green-500',
      'terminee': 'bg-gray-500',
      'annulee': 'bg-red-500',
      'refusee': 'bg-red-500'
    }
    return classes[status] || 'bg-gray-500'
  }

  const getStatusBarClass = (status) => {
    const classes = {
      'programmee': 'bg-blue-500',
      'en_attente': 'bg-yellow-500',
      'en_cours': 'bg-green-500',
      'terminee': 'bg-gray-500',
      'annulee': 'bg-red-500',
      'refusee': 'bg-red-500'
    }
    return classes[status] || 'bg-gray-500'
  }

  const getVisiteCardClass = (status) => {
    const classes = {
      'programmee': 'border-l-4 border-l-blue-500',
      'en_attente': 'border-l-4 border-l-yellow-500',
      'en_cours': 'border-l-4 border-l-green-500',
      'terminee': 'border-l-4 border-l-gray-500',
      'annulee': 'border-l-4 border-l-red-500',
      'refusee': 'border-l-4 border-l-red-500'
    }
    return classes[status] || 'border-l-4 border-l-gray-500'
  }

  const showReportModal = (visite) => {
    // TODO: Implement reschedule functionality
    console.log('Reschedule visit:', visite)
  }

  const showDetailsModal = (visite) => {
    // TODO: Implement details modal
    console.log('Show details:', visite)
  }

  // Watch for modal open/close
  watch(() => props.isOpen, (newVal) => {
    if (newVal) {
      loadVisites()
    }
  })

  // Load on mount if modal is open
  onMounted(() => {
    if (props.isOpen) {
      loadVisites()
    }
  })
</script>

<style scoped>
  /* Custom scrollbar for webkit browsers */
  .overflow-y-auto::-webkit-scrollbar {
    width: 6px;
  }

  .overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.3);
    border-radius: 3px;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.5);
  }

  /* Smooth transitions */
  * {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
  }
</style>


