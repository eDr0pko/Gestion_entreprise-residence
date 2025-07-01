<template>
  <div class="min-h-screen bg-white flex flex-col">
    <!-- Contenu principal -->
    <div class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full space-y-8">
        <!-- En-tête minimaliste -->
        <div class="text-center">
          <div class="mx-auto h-16 w-16 bg-[#0097b2] rounded-2xl flex items-center justify-center mb-8 shadow-sm">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
          </div>
          <h1 class="text-3xl font-light text-gray-900 mb-2">
            Gestion Résidence
          </h1>
          <p class="text-gray-500 text-base">
            Connectez-vous à votre espace
          </p>
        </div>

        <!-- Formulaire épuré -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
          <form @submit.prevent="handleLogin" class="space-y-6">
            <!-- Champ email épuré -->
            <div class="space-y-2">
              <label for="email" class="block text-sm font-medium text-gray-700">
                Adresse email
              </label>
              <div class="relative">
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-1 focus:ring-[#0097b2] focus:border-[#0097b2] transition-colors duration-200 bg-white text-gray-900 placeholder-gray-400"
                  :class="{ 
                    'border-red-300 focus:ring-red-500 focus:border-red-500': errors.email || errors.general,
                    'border-gray-200': !errors.email && !errors.general
                  }"
                  placeholder="votre.email@exemple.com"
                />
              </div>
              <p v-if="errors.email" class="text-sm text-red-500">
                {{ errors.email }}
              </p>
            </div>

            <!-- Champ mot de passe épuré -->
            <div class="space-y-2">
              <label for="password" class="block text-sm font-medium text-gray-700">
                Mot de passe
              </label>
              <div class="relative">
                <input
                  id="password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-1 focus:ring-[#0097b2] focus:border-[#0097b2] transition-colors duration-200 bg-white text-gray-900 placeholder-gray-400 pr-12"
                  :class="{ 
                    'border-red-300 focus:ring-red-500 focus:border-red-500': errors.password || errors.general,
                    'border-gray-200': !errors.password && !errors.general
                  }"
                  placeholder="••••••••"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-[#0097b2] transition-colors duration-200"
                >
                  <svg v-if="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                  </svg>
                </button>
              </div>
              <!-- Message d'erreur spécifique au mot de passe ou général -->
              <p v-if="errors.password" class="text-sm text-red-500">
                {{ errors.password }}
              </p>
              <p v-else-if="errors.general" class="text-sm text-red-500">
                Adresse mail ou mot de passe incorrect
              </p>
            </div>

            <!-- Bouton épuré -->
            <button
              type="submit"
              :disabled="authStore.isLoading"
              class="w-full py-3 px-4 bg-[#0097b2] hover:bg-[#007a94] text-white rounded-lg font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-[#0097b2] focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            >
              <svg v-if="authStore.isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ authStore.isLoading ? 'Connexion...' : 'Se connecter' }}
            </button>
          </form>

          <!-- Aide épurée -->
          <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
              Besoin d'aide ? 
              <a href="#" class="text-[#0097b2] hover:text-[#007a94] font-medium transition-colors duration-200">
                Contactez l'administrateur
              </a>
            </p>
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
    layout: false,
    auth: false
  })

  useHead({
    title: 'Connexion - Gestion Entreprise de Résidence'
  })

  const authStore = useAuthStore()
  const router = useRouter()

  // État du formulaire
  const form = reactive({
    email: '',
    password: ''
  })

  const showPassword = ref(false)

  // Gestion des erreurs
  const errors = reactive({
    email: '',
    password: '',
    general: ''
  })

  // Nettoyer les erreurs
  const clearErrors = () => {
    errors.email = ''
    errors.password = ''
    errors.general = ''
  }

  // Gérer la soumission du formulaire
  const handleLogin = async () => {
    clearErrors()

    // Validation côté client
    if (!form.email) {
      errors.email = 'L\'adresse email est requise'
      return
    }

    if (!form.password) {
      errors.password = 'Le mot de passe est requis'
      return
    }

    try {
      console.log('Tentative de connexion avec:', form.email)
      
      const result = await authStore.login(form.email, form.password)
      
      console.log('Connexion réussie:', result)
      
      // Attendre un peu pour s'assurer que le store est bien mis à jour
      await nextTick()
      
      // Redirection selon le rôle
      const user = authStore.user
      if (user) {
        console.log('Redirection pour le rôle:', user.role)
        switch (user.role) {
          case 'admin':
            await router.push('/admin/dashboard')
            break
          case 'gardien':
            await router.push('/gardien/dashboard')
            break
          case 'resident':
            await router.push('/messages')
            break
          default:
            await router.push('/messages')
        }
      } else {
        console.error('Utilisateur non défini après connexion')
        await router.push('/messages')
      }
    } catch (error: any) {
      console.error('Erreur de connexion:', error)
      
      if (error.status === 422 || error.status === 401) {
        errors.general = 'auth_failed'
      } else {
        errors.general = 'Une erreur est survenue. Veuillez réessayer.'
      }
    }
  }

  // Rediriger si déjà connecté
  onMounted(() => {
    authStore.initAuth()
    if (authStore.isAuthenticated) {
      console.log('Déjà connecté, redirection vers /messages')
      router.push('/messages')
    }
  })
</script>


