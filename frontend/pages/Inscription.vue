<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-emerald-50 via-cyan-50 to-blue-100">
    <div class="flex-1 flex items-center justify-center p-4">
      <div class="max-w-lg w-full">
        <!-- Card principale -->
        <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl p-8 border border-white/20">
        <!-- Header -->
        <div class="text-center mb-6">
          <div class="mx-auto h-16 w-16 bg-gradient-to-br from-emerald-500 to-cyan-500 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ t('auth.register.title') }}</h1>
          <p class="text-gray-600 text-sm">{{ t('auth.register.description') }}</p>
        </div>

        <!-- Bouton retour à l'accueil -->
        <div class="mb-6 flex justify-start">
          <NuxtLink to="/" class="inline-flex items-center px-4 py-2 rounded-xl bg-gray-50 text-[#0097b2] border border-gray-200 hover:bg-cyan-50 text-sm font-semibold shadow-sm transition-all duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            {{ t('auth.register.backToHome') }}
          </NuxtLink>
        </div>
        
        <!-- Formulaire d'inscription -->
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-semibold text-gray-800 mb-2">
              {{ t('auth.register.email') }}
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
              :placeholder="t('auth.register.emailPlaceholder')"
              :disabled="loading"
            />
          </div>

          <!-- Nom et Prénom sur la même ligne -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label for="nom" class="block text-sm font-semibold text-gray-800 mb-2">
                {{ t('auth.register.lastName') }}
              </label>
              <input
                id="nom"
                v-model="form.nom"
                type="text"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
                :placeholder="t('auth.register.lastNamePlaceholder')"
                :disabled="loading"
              />
            </div>
            <div>
              <label for="prenom" class="block text-sm font-semibold text-gray-800 mb-2">
                {{ t('auth.register.firstName') }}
              </label>
              <input
                id="prenom"
                v-model="form.prenom"
                type="text"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
                :placeholder="t('auth.register.firstNamePlaceholder')"
                :disabled="loading"
              />
            </div>
          </div>

          <!-- Téléphone -->
          <PhoneInput
            v-model="form.numero_telephone"
            :label="t('auth.register.phone')"
            :disabled="loading"
            required
          />

          <!-- Mot de passe -->
          <div>
            <label for="mot_de_passe" class="block text-sm font-semibold text-gray-800 mb-2">
              {{ t('auth.register.password') }}
            </label>
            <input
              id="mot_de_passe"
              v-model="form.mot_de_passe"
              type="password"
              required
              minlength="6"
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
              :placeholder="t('auth.register.passwordPlaceholder')"
              :disabled="loading"
            />
          </div>

          <!-- Confirmation mot de passe -->
          <div>
            <label for="mot_de_passe_confirmation" class="block text-sm font-semibold text-gray-800 mb-2">
              {{ t('auth.register.confirmPassword') }}
            </label>
            <input
              id="mot_de_passe_confirmation"
              v-model="form.mot_de_passe_confirmation"
              type="password"
              required
              minlength="6"
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white"
              :placeholder="t('auth.register.confirmPasswordPlaceholder')"
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

          <!-- Bouton d'inscription -->
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
              {{ t('auth.register.loading') }}
            </span>
            <span v-else>{{ t('auth.register.registerButton') }}</span>
          </button>
        </form>

        <!-- Liens de navigation -->
        <div class="mt-6 text-center space-y-2">
          <p class="text-sm text-gray-600">
            {{ t('auth.register.alreadyAccount') }}
            <NuxtLink to="/connexion" class="text-emerald-600 hover:text-emerald-700 font-semibold transition-colors duration-200">
              {{ t('auth.register.loginHere') }}
            </NuxtLink>
          </p>
          <p class="text-sm text-gray-600">
            {{ t('auth.register.memberAccount') }}
            <NuxtLink to="/login" class="text-emerald-600 hover:text-emerald-700 font-semibold transition-colors duration-200">
              {{ t('auth.register.memberLogin') }}
            </NuxtLink>
          </p>
        </div>

        <!-- Section aide moderne et discrète -->
        <div class="mt-8 text-center space-y-2">
          <div class="flex flex-col items-center gap-2 mb-2">
            <span class="text-base font-medium text-[#0097b2] bg-cyan-50 rounded px-3 py-1 shadow-sm">{{ t('auth.register.needHelp') }}</span>
          </div>
          <p class="text-sm text-gray-600">
            {{ t('auth.register.needHelp') }}
            <a href="#" @click.prevent="showContactModal = true" class="text-emerald-600 hover:text-emerald-700 font-semibold transition-colors duration-200 cursor-pointer">
              {{ t('auth.register.contactAdmin') }}
            </a>
          </p>
          <p class="text-sm text-gray-600">
            {{ t('auth.register.forgotPassword') }}
            <NuxtLink to="/mot-de-passe-oublie" class="text-emerald-600 hover:text-emerald-700 font-semibold transition-colors duration-200">
              {{ t('auth.register.clickHere') }}
            </NuxtLink>
          </p>
          <ContactAdminModal :show="showContactModal" @close="showContactModal = false" />
        </div>

        <!-- Message d'info -->
      <div class="mt-6 text-center">
        <p class="text-xs text-gray-500">
          {{ t('auth.register.terms') }}
        </p>
      </div>

        </div>      
      </div>
    </div>
    <AppFooter />
  </div>
</template>

<script setup lang="ts">
  import { ref } from 'vue'
  import { useI18n } from 'vue-i18n'
  import ContactAdminModal from '../components/ContactAdminModal.vue'
  import AppFooter from '../components/AppFooter.vue'

  const { t } = useI18n()

  // Métadonnées de la page
  useHead({
    title: computed(() => t('auth.register.pageTitle'))
  })

  // État du formulaire
  const form = ref({
    email: '',
    nom: '',
    prenom: '',
    numero_telephone: '',
    mot_de_passe: '',
    mot_de_passe_confirmation: ''
  })

  // État de l'UI
  const loading = ref(false)
  const message = ref({
    text: '',
    type: 'info' as 'success' | 'error' | 'info'
  })
  const showContactModal = ref(false)

  // Configuration
  const config = useRuntimeConfig()

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
    if (!form.value.email || !form.value.nom || !form.value.prenom || !form.value.numero_telephone || !form.value.mot_de_passe || !form.value.mot_de_passe_confirmation) {
      showMessage('Veuillez remplir tous les champs obligatoires.', 'error')
      return
    }

    if (form.value.mot_de_passe !== form.value.mot_de_passe_confirmation) {
      showMessage('Les mots de passe ne correspondent pas.', 'error')
      return
    }

    if (form.value.mot_de_passe.length < 6) {
      showMessage('Le mot de passe doit contenir au moins 6 caractères.', 'error')
      return
    }

    // Validation du format de téléphone (accepter les espaces)
    const cleanPhone = form.value.numero_telephone.replace(/\s/g, '') // Retirer les espaces
    if (!/^\+\d{1,4}\d{6,15}$/.test(cleanPhone)) {
      showMessage('Le numéro de téléphone doit être au format international (+33123456789)', 'error')
      return
    }

    loading.value = true

    try {
      console.log('🚀 [INSCRIPTION] Envoi des données:', form.value)
      const response: any = await $fetch(`${config.public.apiBase}/guests/register`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: {
          email: form.value.email,
          nom: form.value.nom,
          prenom: form.value.prenom,
          numero_telephone: form.value.numero_telephone,
          mot_de_passe: form.value.mot_de_passe,
          mot_de_passe_confirmation: form.value.mot_de_passe_confirmation
        }
      })

      console.log('✅ [INSCRIPTION] Réponse API:', response)

      const token = response.token || response.access_token;
      if (response.success && token && response.user) {
        showMessage('Inscription réussie ! Redirection en cours...', 'success')
        // Stocker les informations de l'utilisateur dans le store
        const authStore = useAuthStore()
        authStore.setAuth(token, response.user)
        // Forcer le rendu du message avant de rediriger
        await nextTick()
        // Délai plus long pour laisser le temps de voir le message
        await new Promise(resolve => setTimeout(resolve, 1800))
        // Redirection vers les messages
        await navigateTo('/messages')
      } else {
        showMessage(response.message || 'Erreur inattendue lors de l\'inscription', 'error')
      }
    } catch (error: any) {
      console.error('❌ [INSCRIPTION] Erreur:', error)
      
      if (error.status === 422) {
        // Erreurs de validation
        console.log('Erreurs de validation détaillées:', error.data)
        if (error.data?.errors) {
          // Afficher la première erreur trouvée
          const firstErrorKey = Object.keys(error.data.errors)[0]
          const firstError = error.data.errors[firstErrorKey]
          const errorMessage = Array.isArray(firstError) ? firstError[0] : firstError
          showMessage(errorMessage, 'error')
        } else {
          showMessage(error.data?.message || 'Données invalides', 'error')
        }
      } else if (error.status === 409 || error.message?.includes('unique')) {
        showMessage('Un compte avec cette adresse email existe déjà', 'error')
      } else {
        showMessage(error.data?.message || 'Une erreur est survenue lors de l\'inscription', 'error')
      }
    } finally {
      loading.value = false
    }
  }
</script>


