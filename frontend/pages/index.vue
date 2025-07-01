<template>
  <div class="min-h-screen bg-white flex flex-col">
    <!-- Contenu principal centré -->
    <div class="flex-grow flex items-center justify-center px-4">
      <div class="text-center max-w-md mx-auto">
        <!-- Logo minimaliste -->
        <div class="mb-8">
          <div class="w-20 h-20 mx-auto bg-gradient-to-br from-[#0097b2] to-[#00b4d8] rounded-full flex items-center justify-center shadow-lg shadow-[#0097b2]/20">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
          </div>
        </div>

        <!-- Titre épuré -->
        <h1 class="text-2xl font-light text-gray-800 mb-6 tracking-wide">
          Gestion Résidence
        </h1>

        <!-- Indicateur de chargement minimaliste -->
        <div class="mb-6">
          <div class="w-8 h-8 mx-auto">
            <div class="w-full h-full border-2 border-gray-200 border-t-[#0097b2] rounded-full animate-spin"></div>
          </div>
        </div>

        <!-- Message de redirection -->
        <p class="text-sm text-gray-500 font-light">
          Redirection en cours...
        </p>

        <!-- Barre de progression subtile -->
        <div class="mt-8 w-32 mx-auto">
          <div class="h-0.5 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-[#0097b2] to-[#00b4d8] rounded-full animate-pulse"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <AppFooter />
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false
})

useHead({
  title: 'Chargement - Gestion Entreprise de Résidence'
})

const authStore = useAuthStore()

// Redirection automatique
onMounted(async () => {
  authStore.initAuth()
  
  if (authStore.isAuthenticated) {
    // Vérifier le token
    const isValid = await authStore.checkAuth()
    if (isValid) {
      await navigateTo('/principale')
    } else {
      await navigateTo('/login')
    }
  } else {
    await navigateTo('/login')
  }
})
</script>