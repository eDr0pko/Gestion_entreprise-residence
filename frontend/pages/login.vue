<template>
  <div class="min-h-screen bg-gradient-to-br from-teal-50 via-white to-cyan-50 flex flex-col relative overflow-hidden">
    <!-- Éléments décoratifs de fond -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-cyan-100 to-transparent rounded-full -translate-y-48 translate-x-48 opacity-60"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-teal-100 to-transparent rounded-full translate-y-40 -translate-x-40 opacity-40"></div>
    <div class="absolute top-1/3 left-1/4 w-32 h-32 bg-gradient-to-br from-[#0097b2] to-transparent rounded-full opacity-10 animate-pulse"></div>
    
    <!-- Contenu principal -->
    <div class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="max-w-md w-full space-y-8">
        <!-- En-tête moderne avec effet glassmorphism -->
        <div class="text-center">
          <div class="mx-auto h-20 w-20 bg-gradient-to-br from-[#0097b2] to-[#008699] rounded-3xl flex items-center justify-center mb-8 shadow-2xl transform hover:scale-105 transition-all duration-300"
               style="box-shadow: 0 25px 50px -12px rgba(0, 151, 178, 0.4);">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
          </div>
          <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-[#0097b2] bg-clip-text text-transparent mb-3">
            Gestion Résidence
          </h1>
          <p class="text-gray-600 text-lg font-medium">
            Révolutionnez votre gestion résidentielle
          </p>
          <p class="text-gray-500 text-base mt-2">
            Connectez-vous à votre espace personnel
          </p>
        </div>

        <!-- Formulaire moderne avec effet glassmorphism -->
        <div class="relative group">
          <div class="absolute -inset-1 bg-gradient-to-r from-[#0097b2] to-[#008699] rounded-3xl opacity-20 group-hover:opacity-30 transition-opacity duration-500 blur"></div>
          <div class="relative bg-white bg-opacity-80 backdrop-blur-sm rounded-3xl shadow-2xl border border-white border-opacity-50 p-8 hover:shadow-3xl transition-all duration-500">
            <!-- Gradient décoratif interne -->
            <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-cyan-100 to-transparent rounded-full -translate-y-12 translate-x-12"></div>
            
            <form @submit.prevent="handleLogin" class="space-y-6 relative">
              <!-- Champ email moderne -->
              <div class="space-y-3">
                <label for="email" class="block text-sm font-semibold text-gray-800">
                  Adresse email
                </label>
                <div class="relative group/input">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within/input:text-[#0097b2] transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                  </div>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full pl-10 pr-4 py-4 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-[#0097b2] focus:border-[#0097b2] transition-all duration-300 bg-white bg-opacity-70 backdrop-blur-sm text-gray-900 placeholder-gray-500 font-medium"
                    :class="{ 
                      'border-red-400 focus:ring-red-500 focus:border-red-500 bg-red-50 bg-opacity-50': errors.email || errors.general,
                      'border-gray-200 hover:border-[#0097b2] hover:border-opacity-50': !errors.email && !errors.general
                    }"
                    placeholder="votre.email@exemple.com"
                  />
                </div>
                <Transition name="error" mode="out-in">
                  <p v-if="errors.email" class="text-sm text-red-600 font-medium flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ errors.email }}
                  </p>
                </Transition>
              </div>

              <!-- Champ mot de passe moderne -->
              <div class="space-y-3">
                <label for="password" class="block text-sm font-semibold text-gray-800">
                  Mot de passe
                </label>
                <div class="relative group/input">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within/input:text-[#0097b2] transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                  </div>
                  <input
                    id="password"
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    required
                    class="w-full pl-10 pr-12 py-4 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-[#0097b2] focus:border-[#0097b2] transition-all duration-300 bg-white bg-opacity-70 backdrop-blur-sm text-gray-900 placeholder-gray-500 font-medium"
                    :class="{ 
                      'border-red-400 focus:ring-red-500 focus:border-red-500 bg-red-50 bg-opacity-50': errors.password || errors.general,
                      'border-gray-200 hover:border-[#0097b2] hover:border-opacity-50': !errors.password && !errors.general
                    }"
                    placeholder="••••••••••••"
                  />
                  <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-[#0097b2] transition-colors duration-200 group"
                  >
                    <svg v-if="showPassword" class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <svg v-else class="h-5 w-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                    </svg>
                  </button>
                </div>
                <Transition name="error" mode="out-in">
                  <div v-if="errors.password || errors.general" class="space-y-1">
                    <p v-if="errors.password" class="text-sm text-red-600 font-medium flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      {{ errors.password }}
                    </p>
                    <p v-else-if="errors.general" class="text-sm text-red-600 font-medium flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      Adresse mail ou mot de passe incorrect
                    </p>
                  </div>
                </Transition>
              </div>

              <!-- Bouton de connexion moderne -->
              <button
                type="submit"
                :disabled="authStore.isLoading"
                class="group relative w-full py-4 px-6 bg-gradient-to-r from-[#0097b2] to-[#008699] hover:from-[#008699] hover:to-[#007a94] text-white rounded-2xl font-semibold text-lg transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#0097b2] focus:ring-opacity-50 disabled:opacity-60 disabled:cursor-not-allowed transform hover:scale-105 hover:shadow-2xl"
                style="box-shadow: 0 10px 30px -10px rgba(0, 151, 178, 0.4);"
              >
                <span class="relative z-10 flex items-center justify-center">
                  <svg v-if="authStore.isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="mr-3 h-5 w-5 text-white group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                  </svg>
                  {{ authStore.isLoading ? 'Connexion en cours...' : 'Se connecter' }}
                </span>
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-0 group-hover:opacity-20 transition-opacity duration-300 rounded-2xl"></div>
              </button>
            </form>

            <!-- Aide moderne -->
            <div class="mt-8 text-center">
              <div class="relative">
                <div class="absolute inset-0 flex items-center">
                  <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                  <span class="px-4 bg-white bg-opacity-80 text-gray-500 font-medium">Besoin d'assistance ?</span>
                </div>
              </div>
              <div class="mt-4 space-y-3">
                <div>
                  <a href="#" class="inline-flex items-center text-[#0097b2] hover:text-[#007a94] font-semibold transition-colors duration-200 group">
                    <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    Contactez l'administrateur
                  </a>
                </div>
                <div>
                  <NuxtLink to="/Inscription" class="inline-flex items-center text-gray-600 hover:text-[#0097b2] font-medium transition-colors duration-200 group">
                    <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    S'inscrire comme invité
                  </NuxtLink>
                </div>
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
        console.log('Redirection pour le rôle:', (user as any).role)
        switch ((user as any).role) {
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


