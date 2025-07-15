<template>
  <header class="bg-gradient-to-r from-white via-gray-50 to-white border-b border-gray-200 px-6 lg:px-10 py-3 flex items-center justify-between sticky top-0 z-50 backdrop-blur-sm min-h-[56px]">
    <!-- Effet de glassmorphisme subtil -->
    <div class="absolute inset-0 bg-gradient-to-r from-[#0097b2]/3 via-transparent to-[#00b4d8]/3 pointer-events-none"></div>
    
    <div class="relative flex items-center gap-6 w-full min-h-[40px]">
      <!-- Bouton retour planning (gauche, partout sauf sur /planning) -->
      <NuxtLink
        v-if="$route.path !== '/planning'"
        to="/planning"
        class="flex items-center gap-3 text-[#0097b2] hover:text-[#007a94] hover:bg-white/80 backdrop-blur-sm rounded-xl px-5 py-3 transition-all duration-200 shadow-md mr-6 text-lg"
        title="Retour planning"
        style="min-width: 0;"
      >
        <svg class="w-6 h-6 lg:w-7 lg:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        <span class="hidden sm:inline font-semibold">Retour</span>
      </NuxtLink>

      <!-- Titre et badges -->
      <div class="flex items-center gap-5 flex-1 min-w-0">
        <h1 class="truncate text-xl lg:text-2xl font-extrabold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">{{ title }}</h1>
        <!-- Badge global (ex: messages non lus) -->
        <Transition name="badge">
          <div 
            v-if="badge && badge.count > 0"
            class="bg-red-500 text-white text-xs lg:text-sm font-bold rounded-full min-w-[20px] lg:min-w-[24px] h-5 lg:h-6 flex items-center justify-center px-1 lg:px-2"
            :title="badge.title"
          >
            {{ badge.count > 99 ? '99+' : badge.count }}
          </div>
        </Transition>
      </div>
    </div>

    <!-- Actions à droite -->
    <div class="relative flex items-center gap-6 lg:gap-7 xl:gap-9">
      <!-- Bouton signalement d'incident (flottant, orange, visible partout) -->
      <button
        @click="showIncidentModal = true"
        class="p-3 text-lg text-[#e67e22] hover:text-white hover:bg-[#e67e22]/90 backdrop-blur-sm rounded-xl transition-all duration-200 tooltip-container shadow-md mx-1 scale-90"
        title="Signaler un incident"
      >
        <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 20H3a2 2 0 01-2-2V6a2 2 0 012-2h18a2 2 0 012 2v12a2 2 0 01-2 2z" />
        </svg>
      </button>
      <ReportIncidentModal v-if="showIncidentModal" @close="showIncidentModal = false" />
      <!-- Statut de connexion (si props fournis) -->
      <div class="flex items-center gap-2 mr-4 px-5 py-2 min-h-[38px] min-w-[135px] max-w-[180px] bg-white/60 rounded-2xl shadow-inner border border-gray-200 backdrop-blur-md transition-all duration-200 scale-95">
        <div 
          class="w-4 h-4 rounded-full"
          :class="error ? 'bg-red-500' : loadingConversations ? 'bg-yellow-500 animate-pulse' : 'bg-green-500'"
          :title="error ? 'Erreur de connexion' : loadingConversations ? 'Chargement...' : 'Connecté'"
        ></div>
        <span v-if="error" class="text-sm font-bold text-red-500 hidden lg:inline">Hors ligne</span>
        <span v-else-if="loadingConversations" class="text-sm font-bold text-yellow-600 hidden lg:inline">Chargement...</span>
        <span v-else class="text-sm font-bold text-green-600 hidden lg:inline">En ligne</span>
      </div>
      <!-- Actions personnalisées -->
      <slot name="actions"></slot>

      <!-- Bouton messages (sauf sur /messages) -->
      <NuxtLink
        v-if="$route.path !== '/messages'"
        to="/messages"
        class="p-3 text-lg text-[#0097b2] hover:text-[#007a94] hover:bg-white/80 backdrop-blur-sm rounded-xl transition-all duration-200 tooltip-container shadow-md mx-1 scale-90"
        title="Messages"
      >
        <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m-2 8a9 9 0 110-18 9 9 0 010 18zm0 0v-2a2 2 0 012-2h4a2 2 0 012 2v2" />
        </svg>
      </NuxtLink>

      <!-- Bouton dashboard admin/gardien -->
      <NuxtLink
        v-if="userRole === 'admin' || userRole === 'gardien'"
        to="/dashboard"
        class="p-3 text-lg text-[#0097b2] hover:text-[#007a94] hover:bg-white/80 backdrop-blur-sm rounded-xl transition-all duration-200 tooltip-container shadow-md mx-1 scale-90"
        title="Tableau de bord"
      >
        <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" />
        </svg>
      </NuxtLink>

      <!-- Bouton profil avec engrenage -->
      <NuxtLink 
        v-if="!hideProfileButton"
        to="/profile" 
        class="p-3 text-lg text-gray-500 hover:text-[#0097b2] hover:bg-white/80 backdrop-blur-sm rounded-xl transition-all duration-200 tooltip-container shadow-md mx-1 scale-90"
        title="Profil et paramètres"
      >
        <svg class="w-5 h-5 lg:w-6 lg:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
        </svg>
      </NuxtLink>

      <!-- Menu hamburger (mobile) -->
      <button 
        v-if="showMobileMenu"
        @click="toggleMobileMenu"
        class="lg:hidden p-2 text-lg text-gray-500 hover:text-[#0097b2] hover:bg-white/80 backdrop-blur-sm rounded-lg transition-all duration-200 shadow-sm mx-1 scale-90"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </button>
    </div>
  </header>
</template>

<script setup lang="ts">
import { useAuthStore } from '~/stores/auth'
import { ref } from 'vue'
import ReportIncidentModal from '~/components/ReportIncidentModal.vue'
interface Badge {
  count: number
  title: string
}


interface Props {
  title: string
  backTo?: string
  showBackButton?: boolean
  showMobileMenu?: boolean
  hideProfileButton?: boolean
  badge?: Badge
  error?: string
  loadingConversations?: boolean
}


const props = withDefaults(defineProps<Props>(), {
  showBackButton: false,
  showMobileMenu: false,
  hideProfileButton: false,
  error: '',
  loadingConversations: false
})

const emit = defineEmits<{
  toggleMobileMenu: []
  goBack: []
}>()

const goBack = () => {
  emit('goBack')
}

const toggleMobileMenu = () => {
  emit('toggleMobileMenu')
}

// Récupérer le rôle utilisateur depuis le store
const authStore = useAuthStore()
const userRole = authStore.userRole

const showIncidentModal = ref(false)
</script>

<style scoped>
.badge-enter-active, .badge-leave-active {
  transition: all 0.3s ease;
}

.badge-enter-from {
  opacity: 0;
  transform: scale(0.3);
}

.badge-leave-to {
  opacity: 0;
  transform: scale(0.3);
}

.tooltip-container:hover::after {
  content: attr(title);
  position: absolute;
  bottom: -2rem;
  right: 0;
  background: rgba(0, 0, 0, 0.8);
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.75rem;
  white-space: nowrap;
  z-index: 1000;
}

@media (max-width: 1024px) {
  .tooltip-container:hover::after {
    display: none;
  }
}
</style>