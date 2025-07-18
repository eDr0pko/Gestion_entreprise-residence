<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-cyan-50 to-blue-100 flex items-center justify-center p-4">
    <div class="max-w-md w-full">
      <!-- Card principale -->
      <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl p-8 border border-white/20">
        <!-- Header -->
        <div class="text-center mb-6">
          <div class="mx-auto h-16 w-16 bg-gradient-to-br from-emerald-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-gray-900 mb-2">Connexion Invité</h1>
          <p class="text-gray-600 text-sm">Connectez-vous avec votre compte invité</p>
        </div>

        <!-- Formulaire de connexion -->
        <form @submit.prevent="handleSubmit" class="space-y-5">
          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-semibold text-gray-800 mb-2">
              Adresse email
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
              placeholder="votre@email.com"
              :disabled="loading"
            />
          </div>

          <!-- Mot de passe -->
          <div>
            <label for="password" class="block text-sm font-semibold text-gray-800 mb-2">
              Mot de passe
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
              placeholder="••••••••"
              :disabled="loading"
            />
          </div>

          <!-- Messages d'erreur/succès -->
          <div v-if="message.text" class="p-4 rounded-xl border transition-all duration-300" 
               :class="message.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700'">
            <div class="flex items-center">
              <svg v-if="message.type === 'success'" class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
              </svg>
              <svg v-else class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
              </svg>
              <span class="text-sm font-medium">{{ message.text }}</span>
            </div>
          </div>

          <!-- Bouton de connexion -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3.5 px-6 bg-gradient-to-r from-emerald-500 to-cyan-500 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-cyan-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-[1.02] disabled:transform-none"
          >
            <span v-if="loading" class="flex items-center justify-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Connexion en cours...
            </span>
            <span v-else>Se connecter</span>
          </button>
        </form>

        <!-- Liens de navigation -->
        <div class="mt-6 text-center space-y-2">
          <p class="text-sm text-gray-600">
            Pas encore de compte invité ?
            <NuxtLink to="/Inscription" class="text-emerald-600 hover:text-emerald-700 font-semibold transition-colors duration-200">
              S'inscrire ici
            </NuxtLink>
          </p>
          <p class="text-sm text-gray-600">
            Vous êtes membre de la résidence ?
            <NuxtLink to="/login" class="text-emerald-600 hover:text-emerald-700 font-semibold transition-colors duration-200">
              Connexion membre
            </NuxtLink>
          </p>
        </div>
        <!-- Besoin d'assistance et retour à l'accueil dans la card -->
        <div class="mt-8 text-center">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-4 bg-white bg-opacity-80 text-gray-500 font-medium">Besoin d'assistance ?</span>
            </div>
          </div>
          <div class="mt-4">
            <a href="#" @click.prevent="showContactModal = true" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold transition-colors duration-200 group">
              <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
              </svg>
              Contactez l'administrateur
            </a>
            <ContactAdminModal :show="showContactModal" @close="showContactModal = false" />
          </div>
          <div class="mt-6">
            <NuxtLink to="/" class="group inline-flex items-center text-sm text-gray-500 hover:text-emerald-600 font-semibold transition-colors duration-200 px-4 py-2 rounded-xl border border-gray-200 bg-white/70 hover:bg-emerald-50 shadow-sm">
              <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
              Retour à l'accueil
            </NuxtLink>
          </div>
        </div>
      </div>

      <!-- Message d'info -->
        <div class="mt-4 text-center">
          <p class="text-xs text-gray-500">
            Accès sécurisé pour les invités de la résidence
          </p>
        </div>
    </div>
  </div>
  <AppFooter />
</template>

<script setup lang="ts">
import { ref } from 'vue'

import AppFooter from '~/components/AppFooter.vue'
import ContactAdminModal from '~/components/ContactAdminModal.vue'

const showContactModal = ref(false)

// Métadonnées de la page
useHead({
  title: 'Connexion Invité - Gestion Entreprise de Résidence'
})

// État du formulaire
const form = ref({
  email: '',
  password: ''
})

// État de l'UI
const loading = ref(false)
const message = ref({
  text: '',
  type: 'info' as 'success' | 'error' | 'info'
})

// Configuration
const config = useRuntimeConfig()
const authStore = useAuthStore()

// Fonction pour afficher un message
const showMessage = (text: string, type: 'success' | 'error' | 'info' = 'info') => {
  message.value = { text, type }
  // Auto-hide après 5 secondes pour les messages de succès et d'info
  if (type !== 'error') {
    setTimeout(() => {
      message.value = { text: '', type: 'info' }
    }, 5000)
  }
}

// Fonction de soumission du formulaire
const handleSubmit = async () => {
  // Réinitialiser les messages
  message.value = { text: '', type: 'info' }
  
  // Validation côté client
  if (!form.value.email.trim()) {
    showMessage('L\'adresse email est requise', 'error')
    return
  }

  if (!form.value.password) {
    showMessage('Le mot de passe est requis', 'error')
    return
  }

  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    showMessage('L\'adresse email n\'est pas valide', 'error')
    return
  }

  loading.value = true

  try {
    console.log('🚀 [CONNEXION] Tentative de connexion invité:', form.value.email)
    
    // Connexion avec email et mot de passe
    const response: any = await $fetch(`${config.public.apiBase}/guests/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: {
        email: form.value.email.trim(),
        mot_de_passe: form.value.password
      }
    })

    console.log('✅ [CONNEXION] Réponse API:', response)

    if (response.success && response.token && response.user) {
      showMessage('Connexion réussie ! Redirection en cours...', 'success')
      
      // Stocker les informations de l'utilisateur dans le store
      authStore.setAuth(response.token, response.user)
      
      // Petit délai pour afficher le message de succès
      await new Promise(resolve => setTimeout(resolve, 1000))
      
      // Redirection vers les messages
      await navigateTo('/messages')
    } else {
      showMessage(response.message || 'Erreur lors de la connexion', 'error')
    }
  } catch (error: any) {
    console.error('❌ [CONNEXION] Erreur:', error)
    
    if (error.status === 404) {
      showMessage('Aucun compte invité trouvé avec cette adresse email', 'error')
    } else if (error.status === 422) {
      showMessage('Email ou mot de passe incorrect', 'error')
    } else if (error.status === 403) {
      showMessage('Votre compte a été désactivé. Contactez l\'administration.', 'error')
    } else {
      showMessage(error.data?.message || 'Une erreur est survenue. Veuillez réessayer.', 'error')
    }
  } finally {
    loading.value = false
  }
}

// Vérifier si l'utilisateur est déjà connecté
onMounted(async () => {
  authStore.initAuth()
  
  // Si l'utilisateur est déjà connecté, rediriger vers les messages
  if (authStore.isAuthenticated) {
    await navigateTo('/planning')
  }
})
</script>
