<template>
  <div>
    <!-- Bouton de notifications amélioré / configurable -->
    <transition name="notif-btn-fade">
      <button
        v-show="!open"
        :style="buttonStyle"
        @click="toggleSidebar"
        class="notif-btn group"
        :class="{ clicked: btnClicked, 'placement-inline': placement === 'inline' }"
        @animationend="btnClicked = false"
        :aria-expanded="open.toString()"
        aria-haspopup="true"
        :aria-label="t('notifications.title')"
      >
        <span class="sr-only">{{ t('notifications.title') }}</span>
        <div class="relative flex items-center justify-center">
          <!-- Cloche minimaliste -->
          <svg class="w-6 h-6 text-slate-600 group-hover:text-cyan-700 transition-colors duration-300" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 22c1.1 0 2-.9 2-2H10c0 1.1.9 2 2 2Zm7-6V11a7 7 0 1 0-14 0v5l-1.6 2.4c-.3.45 0 1.06.54 1.06H20.06c.54 0 .86-.61.54-1.06L19 16Z" />
          </svg>
          <!-- Badge -->
          <div v-if="unreadCount > 0" class="absolute -top-1.5 -right-1.5 flex items-center justify-center h-5 min-w-[20px] px-1 rounded-full text-[10px] font-semibold text-white bg-gradient-to-br from-rose-500 to-rose-600 shadow ring-1 ring-white/70">
            {{ unreadCount > 99 ? '99+' : unreadCount }}
          </div>
          <!-- Halo animé -->
          <div v-if="unreadCount > 0" class="absolute inset-0 rounded-full bg-rose-400/20 animate-ping"></div>
        </div>
      </button>
    </transition>

    <!-- Sidebar Moderne avec Glassmorphism -->
    <Teleport to="body">
      <transition name="slide">
        <div v-if="open" class="notification-sidebar" :style="sidebarWrapperStyle">
          <!-- Overlay (limité entre header et footer) -->
          <div
            v-if="open && dimLevel !== 'none'"
            @click="toggleSidebar"
            @wheel="onOverlayWheel"
            :class="overlayClass"
            :style="overlayStyle"
          ></div>
          <!-- Container principal -->
          <div class="sidebar-container" @click.stop role="dialog" :aria-label="t('notifications.title')" :style="sidebarInnerStyle">
          <!-- Header avec gradient moderne -->
          <div class="sidebar-header refined">
            <div class="flex items-start justify-between">
              <div>
                <div class="flex items-center gap-2 mb-2">
                  <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-gradient-to-br from-cyan-500/15 to-sky-600/20 ring-1 ring-white/40 shadow-inner">
                    <svg class="w-5 h-5 text-cyan-700" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22c1.1 0 2-.9 2-2H10c0 1.1.9 2 2 2Zm7-6V11a7 7 0 1 0-14 0v5l-1.6 2.4c-.3.45 0 1.06.54 1.06H20.06c.54 0 .86-.61.54-1.06L19 16Z" /></svg>
                  </div>
                  <h2 class="text-lg font-semibold tracking-tight text-slate-800">{{ t('notifications.title') }}</h2>
                </div>
                <p class="text-xs font-medium text-slate-500">{{ t('notifications.subtitle') }}</p>
              </div>
              <button @click="toggleSidebar" class="close-btn" aria-label="Close notifications">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
              </button>
            </div>
            <!-- Statistiques condensées -->
            <div class="mt-4 flex gap-3">
              <div class="mini-stat">
                <span class="label">{{ t('notifications.stats.total') }}</span>
                <span class="value">{{ totalNotifications }}</span>
              </div>
              <div class="mini-stat">
                <span class="label">{{ t('notifications.stats.pending') }}</span>
                <span class="value text-amber-600">{{ unreadCount }}</span>
              </div>
              <div class="mini-stat">
                <span class="label">{{ t('notifications.stats.approved') }}</span>
                <span class="value text-emerald-600">{{ approvedCount }}</span>
              </div>
            </div>
          </div>

          <!-- Filtres modernes -->
          <div class="px-6 py-4 border-b border-white/10">
            <div class="flex gap-2">
              <button 
                @click="showTrash = false" 
                :class="[
                  'filter-btn',
                  !showTrash ? 'filter-btn-active' : 'filter-btn-inactive'
                ]"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v1a1 1 0 001 1h2m-2 8h11M5 12h14"/>
                </svg>
                <span class="hidden sm:inline">{{ t('notifications.filters.pending') }}</span>
              </button>
              <button 
                @click="showTrash = true" 
                :class="[
                  'filter-btn',
                  showTrash ? 'filter-btn-active' : 'filter-btn-inactive'
                ]"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span class="hidden sm:inline">{{ t('notifications.filters.rejected') }}</span>
              </button>
            </div>
          </div>

          <!-- Liste des notifications -->
          <div class="notification-content" @wheel="onContentWheel">
            <!-- État de chargement -->
            <div v-if="loading" class="loading-state">
              <div class="loading-spinner"></div>
              <span class="text-gray-500">{{ t('notifications.loading') }}</span>
            </div>

            <!-- Message d'erreur -->
            <div v-else-if="error" class="error-state">
              <div class="error-icon">
                <svg class="w-12 h-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <p class="text-red-600 font-medium text-center">{{ error }}</p>
              <button @click="fetchNotifications" class="retry-btn">
                {{ t('notifications.retry') }}
              </button>
            </div>

            <!-- État vide -->
            <div v-else-if="filteredNotifications.length === 0" class="empty-state">
              <div class="empty-icon">
                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              <h3 class="text-gray-600 font-semibold">
                {{ showTrash ? t('notifications.empty.rejected') : t('notifications.empty.pending') }}
              </h3>
              <p class="text-gray-400 text-sm">
                {{ showTrash ? t('notifications.empty.rejectedDescription') : t('notifications.empty.pendingDescription') }}
              </p>
            </div>

            <!-- Cartes de notification -->
            <div
              v-for="visite in filteredNotifications"
              :key="visite.id_visite"
              class="notification-card"
            >
              <!-- En-tête de la carte -->
              <div class="card-header">
                <div class="card-title-section">
                  <div class="flex items-center gap-2 mb-1">
                    <div :class="['status-dot', getStatusDotClass(visite.statut_visite)]"></div>
                    <h3 class="card-title">
                      {{ visite.motif_visite || t('notifications.defaultVisitReason') }}
                    </h3>
                  </div>
                  <div class="card-meta">
                    <span class="card-date">{{ formatDate(visite.date_visite_start) }}</span>
                  </div>
                </div>
                <div :class="['status-badge', getStatusBadgeClass(visite.statut_visite)]">
                  {{ getStatusText(visite.statut_visite) }}
                </div>
              </div>

              <!-- Détails de la visite -->
              <div class="card-details">
                <div v-if="visite.email_visiteur" class="detail-item">
                  <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  <span class="detail-text">{{ visite.email_visiteur }}</span>
                </div>

                <div v-if="visite.nom_resident" class="detail-item">
                  <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                  </svg>
                  <span class="detail-text">{{ visite.nom_resident }}</span>
                </div>

                <div class="detail-item">
                  <svg class="detail-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span class="detail-text">
                    {{ formatTime(visite.date_visite_start) }} - {{ formatTime(visite.date_visite_end) }}
                  </span>
                </div>
              </div>

              <!-- Boutons d'action -->
              <div
                v-if="canModifyVisit(visite.statut_visite)"
                class="card-actions"
              >
                <button
                  @click="changerStatut(visite.id_visite, 'en_cours')"
                  class="action-btn accept-btn"
                  :disabled="updating"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  <span class="btn-text">{{ actionsTranslations.accept }}</span>
                </button>
                
                <button
                  @click="changerStatut(visite.id_visite, 'refusee')"
                  class="action-btn reject-btn"
                  :disabled="updating"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                  <span class="btn-text">{{ actionsTranslations.reject }}</span>
                </button>
              </div>
            </div>
          </div>
          </div>
        </div>
      </transition>
    </Teleport>
  </div>
</template>

<script setup>
  import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
  import { useAuthStore } from '@/stores/auth'
  import { useI18n } from 'vue-i18n'
  
  const authStore = useAuthStore()
  const config = useRuntimeConfig()
  // Grab both t and current locale for dynamic date/time formatting
  const { t, locale } = useI18n()

  const props = defineProps({
    placement: { type: String, default: 'top-right' }, // 'top-right' | 'bottom-right' | 'inline'
    dimLevel: { type: String, default: 'light' } // 'none' | 'light' | 'medium'
  })

  // State
  const open = ref(false)
  const btnClicked = ref(false)
  const showTrash = ref(false)
  const notifications = ref([])
  const loading = ref(false)
  const updating = ref(false)
  const error = ref('')
  
  // Emits
  const emit = defineEmits(['update:open'])

  // Computed
  const filteredNotifications = computed(() =>
    notifications.value.filter(v =>
      showTrash.value ? v.statut_visite === 'refusee' || v.statut_visite === 'annulee' : !['refusee', 'annulee'].includes(v.statut_visite)
    )
  )

  const totalNotifications = computed(() => notifications.value.length)
  
  const unreadCount = computed(() => 
    notifications.value.filter(v => 
      !['terminee', 'en_cours', 'refusee', 'annulee'].includes(v.statut_visite)
    ).length
  )

  const approvedCount = computed(() => 
    notifications.value.filter(v => v.statut_visite === 'en_cours' || v.statut_visite === 'terminee').length
  )

  // Methods
  const toggleSidebar = () => {
    btnClicked.value = true
    open.value = !open.value
    emit('update:open', open.value)
    if (open.value) fetchNotifications()
  }

  // Keyboard shortcuts (n to toggle, ESC to close)
  const handleKey = (e) => {
    if (e.key === 'n' && !e.metaKey && !e.ctrlKey) {
      e.preventDefault()
      if (!open.value) toggleSidebar(); else if (document.activeElement && document.activeElement.tagName === 'BODY') toggleSidebar()
    }
    if (e.key === 'Escape' && open.value) {
      open.value = false
      emit('update:open', false)
    }
  }

  const fetchNotifications = async () => {
    if (!authStore.token) return

    loading.value = true
    error.value = ''
    try {
      const response = await fetch(`${config.public.apiBase}/visites`, {
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        }
      })

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      const text = await response.text()
      if (!text) {
        notifications.value = []
        return
      }

      try {
        const data = JSON.parse(text)
        notifications.value = data.visites || data || []
      } catch (parseError) {
        console.error('Error parsing JSON:', parseError)
        console.error('Response text:', text)
        error.value = t('notifications.errors.invalidResponse')
        notifications.value = []
      }
    } catch (error) {
      console.error('Error fetching notifications:', error)
      error.value = t('notifications.errors.fetchFailed')
      notifications.value = []
    } finally {
      loading.value = false
    }
  }

  const changerStatut = async (visiteId, statut) => {
    if (!authStore.token || updating.value) return

    updating.value = true
    error.value = ''
    try {
      const response = await fetch(`${config.public.apiBase}/visites/${visiteId}/statut`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ statut: statut })
      })

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      // Refresh notifications after successful status change
      await fetchNotifications()
      
      // Show success message
      error.value = t('notifications.success.statusChanged')
      setTimeout(() => error.value = '', 3000)
      
    } catch (error) {
      console.error('Error changing status:', error)
      error.value = t('notifications.errors.statusChangeFailed')
      setTimeout(() => error.value = '', 5000)
    } finally {
      updating.value = false
    }
  }

  const canModifyVisit = (statut) => {
    return ['programmee', 'en_attente'].includes(statut)
  }

  // Cache des traductions pour éviter les erreurs répétées
  const statusTranslations = computed(() => {
    try {
      return {
        'programmee': t('notifications.status.scheduled'),
        'en_attente': t('notifications.status.pending'),
        'en_cours': t('notifications.status.inProgress'),
        'terminee': t('notifications.status.completed'),
        'annulee': t('notifications.status.cancelled'),
        'refusee': t('notifications.status.rejected'),
        'banni': t('notifications.status.banned')
      }
    } catch (e) {
      // Fallback en cas d'erreur i18n
      return {
        'programmee': 'Programmée',
        'en_attente': 'En attente',
        'en_cours': 'En cours',
        'terminee': 'Terminée',
        'annulee': 'Annulée',
        'refusee': 'Refusée',
        'banni': 'Banni'
      }
    }
  })

  const actionsTranslations = computed(() => {
    try {
      return {
        accept: t('notifications.actions.accept'),
        reject: t('notifications.actions.reject')
      }
    } catch (e) {
      // Fallback en cas d'erreur i18n
      return {
        accept: 'Accepter',
        reject: 'Refuser'
      }
    }
  })

  const getStatusText = (statut) => {
    return statusTranslations.value[statut] || statut
  }

  const getStatusDotClass = (statut) => {
    const classMap = {
      'programmee': 'bg-blue-500',
      'en_attente': 'bg-yellow-500',
      'en_cours': 'bg-green-500',
      'terminee': 'bg-gray-500',
      'annulee': 'bg-red-500',
      'refusee': 'bg-red-600',
      'banni': 'bg-black'
    }
    return classMap[statut] || 'bg-gray-400'
  }

  const getStatusBadgeClass = (statut) => {
    const classMap = {
      'programmee': 'badge-blue',
      'en_attente': 'badge-yellow',
      'en_cours': 'badge-green',
      'terminee': 'badge-gray',
      'annulee': 'badge-red',
      'refusee': 'badge-red',
      'banni': 'badge-black'
    }
    return classMap[statut] || 'badge-gray'
  }

  // Map our i18n locale to appropriate browser locale tags
  const localeMap = {
    fr: 'fr-FR',
    en: 'en-GB', // Using en-GB for 24h format; adjust if 12h desired
    zh: 'zh-CN'
  }

  const currentLocale = () => localeMap[locale.value] || 'en-GB'

  const formatDate = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    try {
      return date.toLocaleDateString(currentLocale(), {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      })
    } catch {
      return date.toISOString().slice(0,10)
    }
  }

  const formatTime = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    try {
      return date.toLocaleTimeString(currentLocale(), {
        hour: '2-digit',
        minute: '2-digit'
      })
    } catch {
      const h = String(date.getHours()).padStart(2,'0')
      const m = String(date.getMinutes()).padStart(2,'0')
      return `${h}:${m}`
    }
  }

  // Auto-refresh
  let refreshInterval
  onMounted(() => {
    if (open.value) fetchNotifications()
    refreshInterval = setInterval(fetchNotifications, 30000) // Refresh every 30 seconds
    window.addEventListener('keydown', handleKey)
  })

  onUnmounted(() => {
    if (refreshInterval) clearInterval(refreshInterval)
    window.removeEventListener('keydown', handleKey)
  })

  // Dynamic placement style for button
  const buttonStyle = computed(() => {
    if (props.placement === 'bottom-right') {
      return 'bottom:1.5rem;right:1.25rem;top:auto;'
    }
    if (props.placement === 'inline') {
      return 'position:static;'
    }
    return 'top:6.5rem;right:1rem;' // default top-right refined
  })

  const dimLevel = computed(() => props.dimLevel)
  // Dimensions dynamiques pour ne pas couvrir header/footer
  const headerHeight = ref(56) // valeur par défaut
  const footerHeight = ref(80) // valeur par défaut approximative
  const atBottom = ref(false)

  const measureLayout = () => {
    // Mesure header/footer s'ils existent
    const headerEl = document.querySelector('header')
    const footerEl = document.querySelector('footer')
    if (headerEl) headerHeight.value = headerEl.getBoundingClientRect().height
    if (footerEl) footerHeight.value = footerEl.getBoundingClientRect().height
  }

  const viewportHeight = () => window.innerHeight || document.documentElement.clientHeight

  const availableHeight = computed(() => Math.max(0, viewportHeight() - headerHeight.value))

  const sidebarWrapperStyle = computed(() => ({
    top: headerHeight.value + 'px'
  }))

  const sidebarInnerStyle = computed(() => ({
    height: `calc(100vh - ${headerHeight.value}px)`
  }))

  const overlayClass = computed(() => {
    switch (dimLevel.value) {
      case 'medium':
        return 'fixed left-0 right-0 bg-slate-900/25 z-[50000] transition-colors'
      case 'light':
        return 'fixed left-0 right-0 bg-slate-900/5 z-[50000] transition-colors'
      case 'none':
      default:
        return 'fixed left-0 right-0 pointer-events-none z-[50000]'
    }
  })

  const overlayStyle = computed(() => ({
    top: headerHeight.value + 'px',
    bottom: atBottom.value ? footerHeight.value + 'px' : 0
  }))

  // Empêcher la propagation du scroll vers le planning quand la souris est sur le contenu
  const onContentWheel = (e) => {
    const el = e.currentTarget
    if (!el) return
    e.stopPropagation()
    const atTop = el.scrollTop === 0
    const atBottom = Math.ceil(el.scrollTop + el.clientHeight) >= el.scrollHeight
    if ((atTop && e.deltaY < 0) || (atBottom && e.deltaY > 0)) {
      // Empêche le scroll de la page quand on atteint une extrémité
      e.preventDefault()
    }
  }

  // Laisse défiler le planning quand la souris est sur l'overlay (en dehors du panneau)
  const onOverlayWheel = (e) => {
    // On ne bloque pas le scroll global : relayer explicitement
    const delta = e.deltaY
    window.scrollBy({ top: delta, behavior: 'auto' })
  }

  const updateAtBottom = () => {
    const scrollY = window.scrollY || window.pageYOffset
    const viewport = viewportHeight()
    const fullHeight = document.documentElement.scrollHeight || document.body.scrollHeight
    atBottom.value = scrollY + viewport >= fullHeight - 4 // tolérance
  }

  const handleResize = () => {
    measureLayout()
    updateAtBottom()
  }

  // Mesurer à l'ouverture et sur resize
  watch(open, (val) => {
    if (val) {
      // délai pour que le DOM applique les layouts
      requestAnimationFrame(() => measureLayout())
    }
  })

  onMounted(() => {
    measureLayout()
    updateAtBottom()
    window.addEventListener('resize', handleResize)
    window.addEventListener('scroll', updateAtBottom, { passive: true })
  })

  onUnmounted(() => {
    window.removeEventListener('resize', handleResize)
    window.removeEventListener('scroll', updateAtBottom)
  })
</script>

<style scoped>
  /* Bouton de notification moderne */
  .notif-btn {
    position: fixed;
    z-index: 10020;
    width: 52px;
    height: 52px;
    border-radius: 20px;
    background: linear-gradient(145deg, rgba(255,255,255,0.85), rgba(255,255,255,0.55));
    backdrop-filter: blur(14px) saturate(160%);
    -webkit-backdrop-filter: blur(14px) saturate(160%);
    border: 1px solid rgba(255,255,255,0.55);
    box-shadow: 0 4px 12px -2px rgba(0,0,0,0.08), 0 8px 24px -6px rgba(15,118,184,0.2);
    transition: all .35s cubic-bezier(.4,0,.2,1), background .4s;
  }
  .notif-btn:hover {
    box-shadow: 0 6px 18px -2px rgba(0,0,0,.1), 0 10px 30px -6px rgba(14,165,233,.25);
    transform: translateY(-3px) scale(1.04);
    background: linear-gradient(145deg, rgba(255,255,255,0.95), rgba(255,255,255,0.65));
  }
  .notif-btn:active { transform: translateY(0) scale(.95); }
  .notif-btn.clicked { animation: notif-bounce .55s cubic-bezier(.68,-0.55,.265,1.55); }
  .notif-btn.placement-inline { position: static; box-shadow: none; width: 44px; height:44px; }

  @keyframes notif-bounce {
    0% { transform: scale(1); }
    25% { transform: scale(1.2) rotate(5deg); }
    50% { transform: scale(0.9) rotate(-5deg); }
    75% { transform: scale(1.1) rotate(2deg); }
    100% { transform: scale(1) rotate(0deg); }
  }

  /* Sidebar moderne */
  .notification-sidebar { position: fixed; left:0; right:0; display:flex; justify-content:flex-end; z-index:50001; pointer-events:none; }
  .notification-sidebar > div { pointer-events:auto; }

  .sidebar-container {
    position: relative;
    width: min(420px, 100%);
    height: 100%;
    background: linear-gradient(155deg, rgba(255,255,255,0.82), rgba(255,255,255,0.68));
    backdrop-filter: blur(28px) saturate(180%);
    -webkit-backdrop-filter: blur(28px) saturate(180%);
    border-left: 1px solid rgba(255,255,255,0.55);
    border-right: 1px solid rgba(255,255,255,0.25);
    box-shadow: -6px 0 28px -8px rgba(0,0,0,0.12), -2px 0 12px -4px rgba(14,165,233,0.18);
    display: flex; flex-direction: column;
    animation: slideIn .55s cubic-bezier(.4,0,.2,1);
  }
  @keyframes slideIn { from { transform: translateX(40px); opacity:0 } to { transform: translateX(0); opacity:1 } }

  /* Header avec gradient */
  .sidebar-header { padding: 20px 22px 16px 22px; }
  .sidebar-header.refined { background: transparent; }
  .close-btn { width:34px; height:34px; display:flex; align-items:center; justify-content:center; border-radius:12px; background:rgba(255,255,255,0.6); backdrop-filter:blur(8px); border:1px solid rgba(0,0,0,0.06); color:#475569; transition:all .25s; }
  .close-btn:hover { color:#0e7490; background:rgba(255,255,255,0.9); box-shadow:0 4px 12px -2px rgba(14,165,233,0.25); }
  .mini-stat { flex:1; display:flex; flex-direction:column; gap:2px; background:linear-gradient(140deg, rgba(255,255,255,0.9), rgba(255,255,255,0.55)); border:1px solid rgba(0,0,0,0.05); border-radius:14px; padding:10px 12px 8px; position:relative; overflow:hidden; }
  .mini-stat::before { content:''; position:absolute; inset:0; background:radial-gradient(circle at 30% 20%, rgba(14,165,233,0.12), transparent 60%); opacity:.6; }
  .mini-stat .label { font-size:10px; font-weight:600; letter-spacing:.05em; text-transform:uppercase; color:#64748b; }
  .mini-stat .value { font-size:18px; font-weight:600; line-height:1; color:#0f172a; }

  /* Cartes de statistiques */
  .stat-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 12px;
    text-align: center;
    transition: all 0.2s ease;
  }

  .stat-card:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
  }

  /* Boutons de filtre */
  .filter-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 10px;
    font-weight: 500;
    font-size: 14px;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    flex: 1;
    justify-content: center;
  }

  .filter-btn-active {
    background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
    color: white;
    box-shadow: 0 4px 15px -3px rgba(8, 145, 178, 0.4);
  }

  .filter-btn-inactive {
    background: rgba(255, 255, 255, 0.5);
    color: #6b7280;
    border: 1px solid rgba(0, 0, 0, 0.1);
  }

  .filter-btn-inactive:hover {
    background: rgba(255, 255, 255, 0.8);
    color: #374151;
  }

  /* Contenu des notifications */
  .notification-content { flex:1; padding:18px 20px 26px; overflow-y:auto; scroll-behavior:smooth; overscroll-behavior: contain; }

  /* État de chargement */
  .loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    gap: 16px;
  }

  .loading-spinner {
    width: 32px;
    height: 32px;
    border: 3px solid #e5e7eb;
    border-top: 3px solid #0891b2;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    to { transform: rotate(360deg); }
  }

  /* État vide */
  .empty-state {
    text-align: center;
    padding: 40px 20px;
  }

  .empty-icon {
    margin-bottom: 16px;
  }

  /* État d'erreur */
  .error-state {
    text-align: center;
    padding: 40px 20px;
  }

  .error-icon {
    margin-bottom: 16px;
  }

  .retry-btn {
    margin-top: 16px;
    padding: 8px 16px;
    background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .retry-btn:hover {
    background: linear-gradient(135deg, #0ea5e9 0%, #0891b2 100%);
    transform: translateY(-1px);
  }

  /* Cartes de notification */
  .notification-card { background:linear-gradient(150deg,rgba(255,255,255,.92),rgba(255,255,255,.75)); border:1px solid rgba(0,0,0,0.06); border-radius:22px; padding:18px 18px 16px; margin-bottom:14px; position:relative; overflow:hidden; transition:all .35s cubic-bezier(.4,0,.2,1); box-shadow:0 2px 6px -1px rgba(0,0,0,.05),0 3px 12px -3px rgba(14,165,233,.12); }
  .notification-card::after { content:''; position:absolute; inset:0; background:radial-gradient(circle at 85% 20%, rgba(14,165,233,0.12), transparent 55%); pointer-events:none; }
  .notification-card:hover { transform:translateY(-4px) scale(1.015); box-shadow:0 4px 14px -2px rgba(0,0,0,.08),0 8px 26px -6px rgba(14,165,233,.18); }

  /* En-tête de carte */
  .card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
    gap: 12px;
  }

  .card-title-section {
    flex: 1;
    min-width: 0;
  }

  .card-title {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    line-height: 1.4;
    word-wrap: break-word;
    overflow-wrap: break-word;
  }

  .card-meta {
    margin-top: 4px;
  }

  .card-date {
    font-size: 12px;
    color: #6b7280;
    font-weight: 500;
  }

  /* Point de statut */
  .status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
  }

  /* Badge de statut */
  .status-badge { padding:4px 10px; border-radius:9999px; font-size:10px; font-weight:600; letter-spacing:.05em; text-transform:uppercase; backdrop-filter:blur(4px); }

  .badge-blue { background: #dbeafe; color: #1d4ed8; }
  .badge-yellow { background: #fef3c7; color: #d97706; }
  .badge-green { background: #d1fae5; color: #059669; }
  .badge-gray { background: #f3f4f6; color: #6b7280; }
  .badge-red { background: #fee2e2; color: #dc2626; }
  .badge-black { background: #f3f4f6; color: #111827; }

  /* Détails de la carte */
  .card-details {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 16px;
  }

  .detail-item { display:flex; align-items:center; gap:8px; font-size:12px; color:#4b5563; font-weight:500; }

  .detail-icon {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
  }

  .detail-text {
    flex: 1;
    word-wrap: break-word;
    overflow-wrap: break-word;
    line-height: 1.4;
  }

  /* Actions de carte */
  .card-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .action-btn { display:flex; align-items:center; gap:6px; padding:9px 14px; border-radius:14px; font-weight:600; font-size:12px; letter-spacing:.3px; transition:all .25s; border:none; cursor:pointer; flex:1; min-width:0; justify-content:center; position:relative; overflow:hidden; }
  .action-btn::before { content:''; position:absolute; inset:0; background:radial-gradient(circle at 30% 30%, rgba(255,255,255,.35), transparent 65%); opacity:0; transition:opacity .35s; }
  .action-btn:hover::before { opacity:1; }

  .action-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
  }

  .accept-btn { background:linear-gradient(135deg,#10b981,#059669); color:#fff; box-shadow:0 2px 8px -2px rgba(16,185,129,.4); }
  .accept-btn:hover:not(:disabled) { background:linear-gradient(135deg,#34d399,#10b981); transform:translateY(-2px); box-shadow:0 6px 18px -4px rgba(16,185,129,.55); }
  .reject-btn { background:linear-gradient(135deg,#f43f5e,#e11d48); color:#fff; box-shadow:0 2px 8px -2px rgba(244,63,94,.4); }
  .reject-btn:hover:not(:disabled) { background:linear-gradient(135deg,#fb7185,#f43f5e); transform:translateY(-2px); box-shadow:0 6px 18px -4px rgba(244,63,94,.5); }

  .btn-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* Transitions */
  .notif-btn-fade-enter-active, 
  .notif-btn-fade-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .notif-btn-fade-enter-from, 
  .notif-btn-fade-leave-to {
    opacity: 0;
    transform: scale(0.8) translateY(20px);
  }

  .slide-enter-active, 
  .slide-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .slide-enter-from, 
  .slide-leave-to {
    transform: translateX(100%);
    opacity: 0;
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .notif-btn {
      width:48px; height:48px; top:5.5rem; right:.75rem; border-radius:16px;
    }
    
    .notification-sidebar {
      width: 100vw;
      max-width: none;
    }
    
    .sidebar-header {
      padding: 20px;
    }
    
    .notification-card {
      padding: 16px;
    }
    
    .card-actions {
      flex-direction: column;
    }
    
    .action-btn {
      flex: none;
      width: 100%;
    }

    .filter-btn {
      font-size: 12px;
      padding: 6px 12px;
    }

    .card-title {
      font-size: 14px;
    }
  }

  @media (max-width: 480px) {
    .stat-card {
      padding: 8px;
    }
    
    .notification-content {
      padding: 16px;
    }
    
    .sidebar-header {
      padding: 16px;
    }

    .detail-text {
      font-size: 12px;
    }
  }

  /* Scrollbar personalizada */
  .notification-content::-webkit-scrollbar {
    width: 6px;
  }

  .notification-content::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.05);
    border-radius: 10px;
  }

  .notification-content::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #0891b2, #0e7490);
    border-radius: 10px;
  }

  .notification-content::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #0ea5e9, #0891b2);
  }

  /* Focus states for accessibility */
  .notif-btn:focus-visible, .action-btn:focus-visible, .filter-btn:focus-visible { outline:2px solid #06b6d4; outline-offset:2px; }
</style>


