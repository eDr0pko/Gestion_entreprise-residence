<template>
  <nav class="bg-white/90 backdrop-blur-sm border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <div class="flex items-center">
          <NuxtLink to="/" class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
              </svg>
            </div>
            <span class="text-xl font-bold text-gray-800">GER</span>
          </NuxtLink>
        </div>

        <!-- Menu principal -->
        <div class="hidden md:block">
          <div class="ml-10 flex items-baseline space-x-4">
            <NuxtLink 
              to="/principale" 
              class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
              active-class="text-blue-600 bg-blue-50"
            >
              Accueil
            </NuxtLink>
            <NuxtLink 
              to="/dashboard" 
              class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
              active-class="text-blue-600 bg-blue-50"
            >
              Tableau de bord
            </NuxtLink>
            <NuxtLink 
              to="/residents" 
              class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
              active-class="text-blue-600 bg-blue-50"
            >
              Résidents
            </NuxtLink>
            <NuxtLink 
              to="/visites" 
              class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
              active-class="text-blue-600 bg-blue-50"
            >
              Visites
            </NuxtLink>
          </div>
        </div>

        <!-- Informations utilisateur et déconnexion -->
        <div class="hidden md:flex items-center space-x-4">
          <div class="text-sm text-gray-600">
            Bonjour, <span class="font-medium">{{ user?.prenom }}</span>
            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">{{ getRoleLabel(user?.role) }}</span>
          </div>
          <button 
            @click="handleLogout"
            class="bg-gradient-to-r from-red-500 to-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:from-red-600 hover:to-red-700 transition-all"
          >
            Déconnexion
          </button>
        </div>

        <!-- Menu mobile -->
        <div class="md:hidden">
          <button 
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="text-gray-600 hover:text-gray-900 focus:outline-none focus:text-gray-900 p-2"
          >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Menu mobile déroulant -->
      <div v-show="mobileMenuOpen" class="md:hidden border-t border-gray-200 pt-4 pb-3">
        <div class="space-y-1">
          <NuxtLink 
            to="/principale" 
            class="block text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-base font-medium"
            @click="mobileMenuOpen = false"
          >
            Accueil
          </NuxtLink>
          <NuxtLink 
            to="/dashboard" 
            class="block text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-base font-medium"
            @click="mobileMenuOpen = false"
          >
            Tableau de bord
          </NuxtLink>
          <NuxtLink 
            to="/residents" 
            class="block text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-base font-medium"
            @click="mobileMenuOpen = false"
          >
            Résidents
          </NuxtLink>
          <NuxtLink 
            to="/visites" 
            class="block text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-base font-medium"
            @click="mobileMenuOpen = false"
          >
            Visites
          </NuxtLink>
          <NuxtLink 
            to="/login" 
            class="block bg-gradient-to-r from-red-500 to-red-600 text-white px-3 py-2 rounded-md text-base font-medium mt-4"
            @click="handleLogout"
          >
            Déconnexion
          </NuxtLink>
        </div>
      </div>
    </div>
  </nav>
</template>


<script setup lang="ts">
  import type { User } from '~/types/index'
  const authStore = useAuthStore()
  const mobileMenuOpen = ref(false)
  const user = computed(() => authStore.user as User | null)

  // Obtenir le libellé du rôle
  const getRoleLabel = (role?: string) => {
    switch (role) {
      case 'admin': return 'Administrateur'
      case 'gardien': return 'Gardien'
      case 'resident': return 'Résident'
      default: return 'Invité'
    }
  }

  // Gérer la déconnexion
  const handleLogout = async () => {
    await authStore.logout()
    mobileMenuOpen.value = false
  }

  // Fermer le menu mobile quand on clique ailleurs
  onMounted(() => {
    document.addEventListener('click', (e) => {
      const nav = document.querySelector('nav')
      if (nav && !nav.contains(e.target as Node)) {
        mobileMenuOpen.value = false
      }
    })
  })
</script>


