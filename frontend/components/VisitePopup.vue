<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl w-full max-w-2xl border border-white/20 relative animate-fadeIn max-h-[90vh] overflow-hidden flex flex-col">
      <!-- Header with gradient -->
      <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 md:px-6 py-4 rounded-t-3xl flex-shrink-0">
        <div class="flex items-center justify-between text-white">
          <h2 class="text-lg md:text-xl font-bold flex items-center gap-2 md:gap-3">
            <div class="w-6 h-6 md:w-8 md:h-8 bg-white/20 rounded-xl flex items-center justify-center">
              <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
            <span class="hidden sm:inline">{{ t('planning.visitDetails.title', 'Détails de la visite') }}</span>
            <span class="sm:hidden">{{ t('planning.visitDetails.titleShort', 'Visite') }}</span>
          </h2>
          <button 
            @click="$emit('close')" 
            class="text-white/80 hover:text-white text-xl md:text-2xl font-bold p-1 md:p-2 rounded-xl hover:bg-white/10 transition-colors focus:outline-none"
          >
            ×
          </button>
        </div>
      </div>

      <!-- Scrollable Content -->
      <div v-if="visite" class="flex-1 overflow-y-auto px-4 md:px-6 py-4 md:py-6 space-y-4 md:space-y-6">
        <!-- Status section -->
        <div class="space-y-2 md:space-y-3">
          <div class="flex items-center justify-between flex-wrap gap-2">
            <span :class="[
              'inline-flex items-center px-2 md:px-3 py-1 rounded-full text-xs md:text-sm font-semibold',
              getStatusBadgeClass(visite.statut_visite)
            ]">
              <div :class="['w-1.5 h-1.5 md:w-2 md:h-2 rounded-full mr-1 md:mr-2', getStatusDotClass(visite.statut_visite)]"></div>
              {{ getStatutLabel(visite.statut_visite) }}
            </span>
            <span v-if="visite.id_visite" class="text-xs md:text-sm text-gray-400 font-mono">
              #{{ visite.id_visite }}
            </span>
          </div>

          <!-- Status modification section for admin/gardien -->
          <div v-if="canModifyStatus" class="bg-gray-50 rounded-xl md:rounded-2xl p-3 md:p-4 border border-gray-100">
            <div class="flex items-center gap-2 md:gap-3 mb-2 md:mb-3">
              <div class="w-6 h-6 md:w-8 md:h-8 bg-blue-100 rounded-lg md:rounded-xl flex items-center justify-center">
                <svg class="w-3 h-3 md:w-4 md:h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </div>
              <span class="text-xs md:text-sm font-medium text-gray-700">{{ t('planning.visitDetails.modifyStatus', 'Modifier le statut') }}</span>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
              <button
                v-for="status in availableStatuses"
                :key="status.value"
                @click="changeStatus(status.value)"
                :disabled="changingStatus || visite.statut_visite === status.value"
                :class="[
                  'px-2 md:px-3 py-2 rounded-lg text-xs md:text-sm font-medium transition-all duration-200',
                  visite.statut_visite === status.value
                    ? 'bg-gray-200 text-gray-500 cursor-not-allowed'
                    : 'bg-white border-2 hover:shadow-md transform hover:scale-105',
                  status.colorClass
                ]"
              >
                <div class="flex items-center gap-1 md:gap-2">
                  <div :class="['w-1.5 h-1.5 md:w-2 md:h-2 rounded-full', status.dotClass]"></div>
                  <span class="truncate">{{ status.label }}</span>
                </div>
              </button>
            </div>

            <!-- Loading state -->
            <div v-if="changingStatus" class="mt-2 md:mt-3 flex items-center gap-2 text-xs md:text-sm text-blue-600">
              <svg class="w-3 h-3 md:w-4 md:h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ t('planning.visitDetails.updating', 'Mise à jour en cours...') }}
            </div>

            <!-- Error state -->
            <div v-if="statusError" class="mt-2 md:mt-3 p-2 bg-red-50 border border-red-200 rounded-lg">
              <p class="text-xs md:text-sm text-red-600">{{ statusError }}</p>
            </div>
          </div>
        </div>

        <!-- Visit purpose -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl md:rounded-2xl p-3 md:p-4 border border-blue-100">
          <div class="flex items-center gap-2 md:gap-3">
            <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 rounded-lg md:rounded-xl flex items-center justify-center">
              <svg class="w-4 h-4 md:w-5 md:h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h4M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8h1m-1-4h1m4 4h1m-1-4h1"/>
              </svg>
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-xs text-blue-600 font-medium uppercase tracking-wide">{{ t('planning.visitDetails.purpose', 'Motif') }}</p>
              <p class="text-sm md:text-lg font-bold text-gray-900 truncate">{{ visite.motif_visite || '—' }}</p>
            </div>
          </div>
        </div>

        <!-- Time details and Duration in a single row on larger screens -->
        <div class="space-y-3 md:space-y-4">
          <!-- Time details -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
            <div class="bg-gray-50 rounded-xl md:rounded-2xl p-3 md:p-4 border border-gray-100">
              <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 md:w-5 md:h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-xs text-gray-600 font-medium uppercase tracking-wide">{{ t('planning.visitDetails.start', 'Début') }}</span>
              </div>
              <p class="font-mono text-xs md:text-sm text-gray-900 font-semibold">{{ formatDate(visite.date_visite_start) }}</p>
            </div>
            
            <div class="bg-gray-50 rounded-xl md:rounded-2xl p-3 md:p-4 border border-gray-100">
              <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 md:w-5 md:h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-xs text-gray-600 font-medium uppercase tracking-wide">{{ t('planning.visitDetails.end', 'Fin') }}</span>
              </div>
              <p class="font-mono text-xs md:text-sm text-gray-900 font-semibold">{{ formatDate(visite.date_visite_end) }}</p>
            </div>
          </div>

          <!-- Duration -->
          <div class="bg-purple-50 rounded-xl md:rounded-2xl p-3 md:p-4 border border-purple-100">
            <div class="flex items-center gap-2 md:gap-3">
              <div class="w-8 h-8 md:w-10 md:h-10 bg-purple-100 rounded-lg md:rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 md:w-5 md:h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div>
                <p class="text-xs text-purple-600 font-medium uppercase tracking-wide">{{ t('planning.visitDetails.duration', 'Durée') }}</p>
                <p class="text-sm md:text-lg font-bold text-gray-900">{{ getDuree(visite.date_visite_start, visite.date_visite_end) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
          <div class="bg-white rounded-xl border border-gray-200 p-3">
            <span class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ t('planning.visitDetails.visitor', 'Visiteur') }}</span>
            <p class="font-medium text-gray-900 mt-1 text-sm md:text-base truncate">
              {{ getVisitorName(visite) }}
            </p>
          </div>
          
          <div class="bg-white rounded-xl border border-gray-200 p-3">
            <span class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ t('planning.visitDetails.resident', 'Résident') }}</span>
            <p class="font-medium text-gray-900 mt-1 text-sm md:text-base">
              {{ getResidentName(visite) }}
            </p>
          </div>
        </div>

        <!-- Notes -->
        <div v-if="visite.commentaire" class="bg-yellow-50 rounded-xl md:rounded-2xl p-3 md:p-4 border border-yellow-100">
          <div class="flex items-start gap-2 md:gap-3">
            <div class="w-8 h-8 md:w-10 md:h-10 bg-yellow-100 rounded-lg md:rounded-xl flex items-center justify-center flex-shrink-0">
              <svg class="w-4 h-4 md:w-5 md:h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs text-yellow-600 font-medium uppercase tracking-wide mb-1">{{ t('planning.visitDetails.notes', 'Notes') }}</p>
              <p class="text-gray-900 text-sm md:text-base break-words">{{ visite.commentaire }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useAuthStore } from '@/stores/auth'

  const { t } = useI18n()
  const authStore = useAuthStore()
  const config = useRuntimeConfig()

  const props = defineProps({
    visite: Object
  })

  const emit = defineEmits(['close', 'statusChanged'])

  // Status modification state
  const changingStatus = ref(false)
  const statusError = ref('')

  // Check if user can modify status (admin or gardien)
  const canModifyStatus = computed(() => {
    const user = authStore.user
    if (!user) return false
    
    // Check if user is admin or gardien
    const isAdmin = user.role === 'admin' || user.niveau_acces === 'super_admin' || user.niveau_acces === 'admin_standard'
    const isGardien = user.role === 'gardien'
    
    return isAdmin || isGardien
  })

  // Available status options
  const availableStatuses = computed(() => [
    {
      value: 'programmee',
      label: t('planning.status.scheduled', 'Programmée'),
      colorClass: 'border-blue-200 hover:border-blue-300 text-blue-700',
      dotClass: 'bg-blue-500'
    },
    {
      value: 'en_cours',
      label: t('planning.status.inProgress', 'En cours'),
      colorClass: 'border-green-200 hover:border-green-300 text-green-700',
      dotClass: 'bg-green-500'
    },
    {
      value: 'terminee',
      label: t('planning.status.completed', 'Terminée'),
      colorClass: 'border-gray-200 hover:border-gray-300 text-gray-700',
      dotClass: 'bg-gray-500'
    },
    {
      value: 'annulee',
      label: t('planning.status.cancelled', 'Annulée'),
      colorClass: 'border-red-200 hover:border-red-300 text-red-700',
      dotClass: 'bg-red-500'
    }
  ])

  // Change visit status
  async function changeStatus(newStatus) {
    if (!props.visite.id_visite || changingStatus.value) return
    
    try {
      changingStatus.value = true
      statusError.value = ''
      
      const response = await fetch(`${config.public.apiBase}/visites/${props.visite.id_visite}/statut`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ statut: newStatus })
      })
      
      const data = await response.json()
      
      if (!response.ok || !data.success) {
        throw new Error(data.message || t('planning.visitDetails.statusUpdateError', 'Erreur lors de la mise à jour du statut'))
      }
      
      // Update local visite object
      props.visite.statut_visite = newStatus
      
      // Emit event to parent component
      emit('statusChanged', { visitId: props.visite.id_visite, newStatus })
      
    } catch (error) {
      console.error('Error updating visit status:', error)
      statusError.value = error.message || t('planning.visitDetails.statusUpdateError', 'Erreur lors de la mise à jour du statut')
    } finally {
      changingStatus.value = false
    }
  }

  function formatDate(str) {
    if (!str) return '—'
    const d = new Date(str)
    return d.toLocaleString('fr-FR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  }

  function getDuree(start, end) {
    if (!start || !end) return '—'
    const s = new Date(start)
    const e = new Date(end)
    const min = Math.round((e - s) / 60000)
    if (min < 60) return min + ' min'
    const h = Math.floor(min / 60)
    const m = min % 60
    return h + 'h' + (m > 0 ? ' ' + m + 'min' : '')
  }

  function getStatutLabel(statut) {
    const labels = {
      'programmee': t('planning.status.scheduled', 'Programmée'),
      'en_cours': t('planning.status.inProgress', 'En cours'),
      'terminee': t('planning.status.completed', 'Terminée'),
      'en_attente': t('planning.status.pending', 'En attente'),
      'annule': t('planning.status.cancelled', 'Annulée'),
      'annulee': t('planning.status.cancelled', 'Annulée')
    }
    return labels[statut] || statut || '—'
  }

  function getStatusBadgeClass(statut) {
    const classes = {
      'programmee': 'bg-blue-100 text-blue-800 border border-blue-200',
      'en_cours': 'bg-green-100 text-green-800 border border-green-200',
      'terminee': 'bg-gray-100 text-gray-800 border border-gray-200',
      'en_attente': 'bg-yellow-100 text-yellow-800 border border-yellow-200',
      'annule': 'bg-red-100 text-red-800 border border-red-200',
      'annulee': 'bg-red-100 text-red-800 border border-red-200'
    }
    return classes[statut] || 'bg-gray-100 text-gray-800 border border-gray-200'
  }

  function getStatusDotClass(statut) {
    const classes = {
      'programmee': 'bg-blue-500',
      'en_cours': 'bg-green-500',
      'terminee': 'bg-gray-500',
      'en_attente': 'bg-yellow-500',
      'annule': 'bg-red-500',
      'annulee': 'bg-red-500'
    }
    return classes[statut] || 'bg-gray-500'
  }

  function getVisitorName(visite) {
    if (!visite) return '—'
    
    // Priorité aux noms/prénoms si disponibles
    if (visite.nom_visiteur && visite.prenom_visiteur) {
      return `${visite.prenom_visiteur} ${visite.nom_visiteur}`
    }
    
    // Sinon afficher l'email comme fallback
    return visite.email_visiteur || '—'
  }

  function getResidentName(visite) {
    if (!visite) return '—'
    
    // Afficher nom et prénom de l'invité (résident)
    if (visite.nom_invite && visite.prenom_invite) {
      return `${visite.prenom_invite} ${visite.nom_invite}`
    }
    
    // Fallback vers l'ID si les noms ne sont pas disponibles
    return visite.id_invite ? `ID: ${visite.id_invite}` : '—'
  }
</script>

<style scoped>
  .animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(20px) scale(0.95);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  /* Custom scrollbar for webkit browsers */
  .overflow-y-auto::-webkit-scrollbar {
    width: 6px;
  }

  .overflow-y-auto::-webkit-scrollbar-track {
    background: rgba(243, 244, 246, 0.5);
    border-radius: 3px;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.6);
    border-radius: 3px;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.8);
  }

  /* Firefox scrollbar styling */
  .overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.6) rgba(243, 244, 246, 0.5);
  }

  /* Better mobile touch targets */
  @media (max-width: 640px) {
    button {
      min-height: 44px;
      min-width: 44px;
    }
  }

  /* Enhanced hover effects on larger screens */
  @media (hover: hover) and (pointer: fine) {
    button:hover {
      transform: translateY(-1px);
    }
  }

  /* Ensure proper text handling on small screens */
  .truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .break-words {
    word-wrap: break-word;
    word-break: break-word;
    hyphens: auto;
  }

  /* Smooth transitions for all interactive elements */
  button, .transition-all {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* Focus states for accessibility */
  button:focus-visible {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
  }

  /* Loading animation */
  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

  .animate-spin {
    animation: spin 1s linear infinite;
  }
</style>


