<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <AppHeader 
      title="Profil" 
      back-to="/principale"
      :show-mobile-menu="true"
      @toggle-mobile-menu="toggleMobileMenu"
    />

    <!-- Contenu principal -->
    <div class="max-w-4xl mx-auto px-4 py-6 lg:py-8">
      <!-- Informations utilisateur -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:space-x-6">
          <!-- Avatar -->
          <div class="flex justify-center lg:justify-start mb-4 lg:mb-0">
            <div class="w-20 h-20 lg:w-24 lg:h-24 bg-[#0097b2] rounded-full flex items-center justify-center">
              <svg class="w-10 h-10 lg:w-12 lg:h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
            </div>
          </div>

          <!-- Informations -->
          <div class="text-center lg:text-left flex-1">
            <h2 class="text-xl lg:text-2xl font-bold text-gray-900 mb-2">
              {{ user?.nom_complet || 'Utilisateur' }}
            </h2>
            <p class="text-gray-600 mb-2">{{ user?.email }}</p>
            <div class="flex flex-wrap justify-center lg:justify-start gap-2">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#0097b2] text-white">
                {{ user?.role || 'Utilisateur' }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Sections -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Paramètres du compte -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-[#0097b2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Paramètres du compte
          </h3>
          
          <div class="space-y-3">
            <button class="w-full text-left p-3 rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-between">
              <span class="text-gray-700">Modifier les informations</span>
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
            
            <button class="w-full text-left p-3 rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-between">
              <span class="text-gray-700">Changer le mot de passe</span>
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
            
            <button class="w-full text-left p-3 rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-between">
              <span class="text-gray-700">Préférences de notification</span>
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Sécurité -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-[#0097b2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            Sécurité
          </h3>
          
          <div class="space-y-3">
            <div class="p-3 rounded-lg bg-green-50 border border-green-200">
              <div class="flex items-center">
                <svg class="w-4 h-4 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm text-green-700">Compte sécurisé</span>
              </div>
            </div>
            
            <button class="w-full text-left p-3 rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-between">
              <span class="text-gray-700">Sessions actives</span>
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="mt-6 flex flex-col sm:flex-row gap-4">
        <button 
          @click="logout"
          class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
          </svg>
          Se déconnecter
        </button>
      </div>
    </div>

    <!-- Footer -->
    <AppFooter />
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth'
})

useHead({
  title: 'Profil - Gestion Entreprise de Résidence'
})

const authStore = useAuthStore()

const user = computed(() => authStore.user)

const toggleMobileMenu = () => {
  // Logique pour le menu mobile si nécessaire
  console.log('Toggle mobile menu')
}

const logout = async () => {
  try {
    await authStore.logout()
    await navigateTo('/login')
  } catch (error) {
    console.error('Erreur lors de la déconnexion:', error)
  }
}
</script>