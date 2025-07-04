<template>
  <div class="min-h-screen bg-gradient-to-br from-teal-50 via-white to-cyan-50 flex flex-col relative overflow-hidden">
    <!-- Éléments décoratifs de fond -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-cyan-100 to-transparent rounded-full -translate-y-48 translate-x-48 opacity-60"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-teal-100 to-transparent rounded-full translate-y-40 -translate-x-40 opacity-40"></div>
    <div class="absolute top-1/3 left-1/4 w-32 h-32 bg-gradient-to-br from-[#0097b2] to-transparent rounded-full opacity-10 animate-pulse"></div>
    
    <!-- Contenu principal centré -->
    <div class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="max-w-4xl w-full">
        <!-- En-tête avec logo et titre -->
        <div class="text-center mb-12">
          <div class="mx-auto h-24 w-24 bg-gradient-to-br from-[#0097b2] to-[#008699] rounded-3xl flex items-center justify-center mb-8 shadow-2xl transform hover:scale-105 transition-all duration-300"
               style="box-shadow: 0 25px 50px -12px rgba(0, 151, 178, 0.4);">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
          </div>
          <h1 class="text-5xl font-bold bg-gradient-to-r from-gray-900 to-[#0097b2] bg-clip-text text-transparent mb-4">
            Gestion Résidence
          </h1>
          <p class="text-xl text-gray-600 font-medium mb-2">
            Plateforme de communication résidentielle
          </p>
          <p class="text-lg text-gray-500">
            Choisissez votre mode d'accès
          </p>
        </div>

        <!-- Choix d'accès -->
        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
          <!-- Option Membres (Résidents, Gardiens, Administrateurs) -->
          <div class="relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-[#0097b2] to-[#008699] rounded-3xl opacity-20 group-hover:opacity-30 transition-opacity duration-500 blur"></div>
            <div class="relative bg-white bg-opacity-80 backdrop-blur-sm rounded-3xl shadow-xl border border-white border-opacity-50 p-8 hover:shadow-2xl transition-all duration-500 transform hover:scale-105">
              <!-- Icône -->
              <div class="flex justify-center mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-[#0097b2] to-[#008699] rounded-2xl flex items-center justify-center shadow-lg">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                  </svg>
                </div>
              </div>
              
              <!-- Contenu -->
              <h2 class="text-2xl font-bold text-gray-800 text-center mb-4">
                Membres de la Résidence
              </h2>
              <p class="text-gray-600 text-center mb-6 leading-relaxed">
                Résidents, gardiens et administrateurs
              </p>
              <ul class="text-sm text-gray-500 mb-8 space-y-2">
                <li class="flex items-center">
                  <svg class="w-4 h-4 text-[#0097b2] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  Accès complet aux conversations
                </li>
                <li class="flex items-center">
                  <svg class="w-4 h-4 text-[#0097b2] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  Gestion des groupes et événements
                </li>
                <li class="flex items-center">
                  <svg class="w-4 h-4 text-[#0097b2] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  Profil permanent
                </li>
              </ul>
              
              <!-- Bouton -->
              <div v-if="authStore.isAuthenticated">
                <!-- Utilisateur déjà connecté -->
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl">
                  <p class="text-sm text-green-700 text-center">
                    ✓ Connecté en tant que {{ (authStore.user as any)?.prenom || 'Utilisateur' }}
                  </p>
                </div>
                <NuxtLink to="/planning" 
                  class="block w-full py-4 px-6 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-2xl font-semibold text-lg transition-all duration-300 text-center transform hover:scale-105 shadow-lg">
                  <span class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Accéder à l'espace membre
                  </span>
                </NuxtLink>
                <button @click="authStore.logout()"
                  class="mt-3 w-full py-2 px-4 text-gray-600 hover:text-gray-800 font-medium transition-colors duration-200">
                  Se déconnecter
                </button>
              </div>
              <div v-else>
                <!-- Utilisateur non connecté -->
                <NuxtLink to="/login" 
                  class="block w-full py-4 px-6 bg-gradient-to-r from-[#0097b2] to-[#008699] hover:from-[#008699] hover:to-[#007a94] text-white rounded-2xl font-semibold text-lg transition-all duration-300 text-center transform hover:scale-105 shadow-lg"
                  style="box-shadow: 0 8px 25px -8px rgba(0, 151, 178, 0.4);">
                  <span class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Se connecter
                  </span>
                </NuxtLink>
              </div>
            </div>
          </div>

          <!-- Option Invités -->
          <div class="relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-3xl opacity-20 group-hover:opacity-30 transition-opacity duration-500 blur"></div>
            <div class="relative bg-white bg-opacity-80 backdrop-blur-sm rounded-3xl shadow-xl border border-white border-opacity-50 p-8 hover:shadow-2xl transition-all duration-500 transform hover:scale-105">
              <!-- Icône -->
              <div class="flex justify-center mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-2xl flex items-center justify-center shadow-lg">
                  <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                  </svg>
                </div>
              </div>
              
              <!-- Contenu -->
              <h2 class="text-2xl font-bold text-gray-800 text-center mb-4">
                Accès Invité
              </h2>
              <p class="text-gray-600 text-center mb-6 leading-relaxed">
                Visiteurs et invités temporaires
              </p>
              <ul class="text-sm text-gray-500 mb-8 space-y-2">
                <li class="flex items-center">
                  <svg class="w-4 h-4 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  Participation aux conversations
                </li>
                <li class="flex items-center">
                  <svg class="w-4 h-4 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  Accès sécurisé et simplifié
                </li>
                <li class="flex items-center">
                  <svg class="w-4 h-4 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  Inscription rapide en ligne
                </li>
              </ul>
              
              <!-- Boutons -->
              <div class="space-y-3">
                <!-- Bouton S'enregistrer -->
                <NuxtLink to="/Inscription" 
                  class="block w-full py-4 px-6 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-2xl font-semibold text-lg transition-all duration-300 text-center transform hover:scale-105 shadow-lg"
                  style="box-shadow: 0 8px 25px -8px rgba(16, 185, 129, 0.4);">
                  <span class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    S'enregistrer comme invité
                  </span>
                </NuxtLink>
                
                <!-- Bouton Se connecter -->
                <NuxtLink to="/connexion"
                  class="block w-full py-3 px-6 bg-white hover:bg-gray-50 text-emerald-600 border-2 border-emerald-500 hover:border-emerald-600 rounded-2xl font-semibold transition-all duration-300 text-center transform hover:scale-105">
                  <span class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                    Accéder à mon compte invité
                  </span>
                </NuxtLink>
              </div>
            </div>
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
  title: 'Accueil - Gestion Entreprise de Résidence'
})

const authStore = useAuthStore()

// Vérifier si l'utilisateur est déjà connecté au montage
onMounted(async () => {
  authStore.initAuth()
  
  // Laisser l'utilisateur voir les options d'accès même s'il est connecté
})
</script>