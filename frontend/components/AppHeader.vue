<template>
  <header class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between sticky top-0 z-50 shadow-sm">
    <div class="flex items-center space-x-3">
      <!-- Bouton retour (visible sur mobile) -->
      <button 
        v-if="showBackButton"
        @click="goBack"
        class="lg:hidden text-[#0097b2] hover:text-[#007a94] transition-colors p-1"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </button>

      <!-- Lien retour desktop -->
      <NuxtLink 
        v-if="backTo && !showBackButton" 
        :to="backTo" 
        class="hidden lg:block text-[#0097b2] hover:text-[#007a94] transition-colors"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </NuxtLink>

      <!-- Titre et badges -->
      <div class="flex items-center space-x-2">
        <h1 class="text-lg lg:text-xl font-semibold text-gray-900">{{ title }}</h1>
        
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
    <div class="flex items-center space-x-2">
      <!-- Actions personnalisées -->
      <slot name="actions"></slot>

      <!-- Bouton profil avec engrenage -->
      <NuxtLink 
        to="/profile" 
        class="p-2 text-gray-500 hover:text-[#0097b2] hover:bg-gray-100 rounded-lg transition-all duration-200 tooltip-container"
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
        class="lg:hidden p-2 text-gray-500 hover:text-[#0097b2] hover:bg-gray-100 rounded-lg transition-all duration-200"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </button>
    </div>
  </header>
</template>

<script setup lang="ts">
interface Badge {
  count: number
  title: string
}

interface Props {
  title: string
  backTo?: string
  showBackButton?: boolean
  showMobileMenu?: boolean
  badge?: Badge
}

const props = withDefaults(defineProps<Props>(), {
  showBackButton: false,
  showMobileMenu: false
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