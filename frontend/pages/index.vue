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
                  Accès simplifié et rapide
                </li>
                <li class="flex items-center">
                  <svg class="w-4 h-4 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  Aucun mot de passe requis
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
                <button @click="showGuestLogin = true"
                  class="w-full py-3 px-6 bg-white hover:bg-gray-50 text-emerald-600 border-2 border-emerald-500 hover:border-emerald-600 rounded-2xl font-semibold transition-all duration-300 text-center transform hover:scale-105">
                  <span class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                    Accéder à mon compte invité
                  </span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Informations supplémentaires -->
        <div class="text-center mt-12">
          <p class="text-gray-500 text-sm">
            Vous avez des questions ? 
            <a href="#" class="text-[#0097b2] hover:text-[#007a94] font-medium transition-colors duration-200">
              Contactez l'administration
            </a>
          </p>
        </div>
      </div>
    </div>

    <!-- Modal de connexion invité -->
    <div v-if="showGuestLogin" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 relative">
        <!-- Bouton fermer -->
        <button @click="showGuestLogin = false" 
          class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors duration-200">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>

        <!-- Contenu du modal -->
        <div class="text-center mb-6">
          <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
            </svg>
          </div>
          <h3 class="text-2xl font-bold text-gray-800 mb-2">Connexion Invité</h3>
          <p class="text-gray-600">Entrez votre email pour recevoir un code d'accès</p>
        </div>

        <!-- Formulaire de connexion invité -->
        <form @submit.prevent="handleGuestLogin" class="space-y-6">
          <div>
            <label for="guestEmail" class="block text-sm font-semibold text-gray-800 mb-2">
              Adresse email
            </label>
            <input
              id="guestEmail"
              v-model="guestEmail"
              type="email"
              required
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300"
              :class="{ 'border-red-400 focus:ring-red-500 focus:border-red-500': guestLoginError }"
              placeholder="votre.email@exemple.com"
            />
            <p v-if="guestLoginError" class="text-sm text-red-600 mt-2">{{ guestLoginError }}</p>
          </div>

          <button
            type="submit"
            :disabled="isGuestLoginLoading"
            class="w-full py-3 px-6 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-xl font-semibold transition-all duration-300 disabled:opacity-60">
            <span v-if="isGuestLoginLoading" class="flex items-center justify-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Envoi en cours...
            </span>
            <span v-else>Envoyer le code d'accès</span>
          </button>
        </form>

        <!-- Information -->
        <p class="text-xs text-gray-500 text-center mt-4">
          Un code de vérification sera envoyé à votre adresse email
        </p>
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

// États pour la modal de connexion invité
const showGuestLogin = ref(false)
const guestEmail = ref('')
const guestLoginError = ref('')
const isGuestLoginLoading = ref(false)

// Gérer la connexion invité
const handleGuestLogin = async () => {
  if (!guestEmail.value.trim()) {
    guestLoginError.value = 'L\'adresse email est requise'
    return
  }

  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(guestEmail.value)) {
    guestLoginError.value = 'L\'adresse email n\'est pas valide'
    return
  }

  try {
    isGuestLoginLoading.value = true
    guestLoginError.value = ''
    const config = useRuntimeConfig()
    
    console.log('Envoi du code de vérification à:', guestEmail.value)
    
    // Vérifier si l'invité existe et envoyer le code de vérification
    const response: any = await $fetch(`${config.public.apiBase}/guest/send-verification-code`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: {
        email: guestEmail.value.trim()
      }
    })

    if (response.success) {
      // Code envoyé avec succès, fermer la modal et rediriger vers la vérification
      showGuestLogin.value = false
      
      console.log('Code de vérification envoyé avec succès')
      if (response.debug_code) {
        console.log('Code de débogage:', response.debug_code)
      }
      
      // Rediriger vers la page de vérification
      await navigateTo(`/guest-verify?email=${encodeURIComponent(guestEmail.value)}`)
    } else {
      guestLoginError.value = response.message || 'Erreur lors de l\'envoi du code'
    }
  } catch (error: any) {
    console.error('Erreur d\'envoi du code:', error)
    
    if (error.status === 404) {
      guestLoginError.value = 'Aucun compte invité trouvé avec cette adresse email'
    } else {
      guestLoginError.value = error.data?.message || 'Une erreur est survenue. Veuillez réessayer.'
    }
  } finally {
    isGuestLoginLoading.value = false
  }
}

// Nettoyer les erreurs quand on ferme la modal
watch(showGuestLogin, (newValue) => {
  if (!newValue) {
    guestEmail.value = ''
    guestLoginError.value = ''
    isGuestLoginLoading.value = false
  }
})

// Vérifier si l'utilisateur est déjà connecté au montage
onMounted(async () => {
  authStore.initAuth()
  
  // Si l'utilisateur est connecté, le rediriger
  if (authStore.isAuthenticated) {
    const isValid = await authStore.checkAuth()
    if (isValid) {
      await navigateTo('/planning')
    }
  }
  
  // Vérifier s'il y a un invité en local storage
  if (process.client) {
    const guestUser = localStorage.getItem('guest_user')
    if (guestUser) {
      console.log('Invité trouvé en localStorage, redirection vers messages')
      await navigateTo('/messages')
    }
  }
})
</script>