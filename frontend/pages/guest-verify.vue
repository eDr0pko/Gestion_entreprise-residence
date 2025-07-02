<template>
  <div class="min-h-screen bg-gradient-to-br from-teal-50 via-white to-cyan-50 flex flex-col relative overflow-hidden">
    <!-- Éléments décoratifs de fond -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-cyan-100 to-transparent rounded-full -translate-y-48 translate-x-48 opacity-60"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-teal-100 to-transparent rounded-full translate-y-40 -translate-x-40 opacity-40"></div>
    
    <!-- Contenu principal -->
    <div class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="max-w-md w-full space-y-8">
        <!-- En-tête -->
        <div class="text-center">
          <div class="mx-auto h-20 w-20 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-3xl flex items-center justify-center mb-8 shadow-2xl transform hover:scale-105 transition-all duration-300">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 7.89a4 4 0 005.66 0L24 8M3 8v8a2 2 0 002 2h14a2 2 0 002-2V8M3 8l7.89-7.89a4 4 0 015.66 0L24 8"></path>
            </svg>
          </div>
          <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-emerald-600 bg-clip-text text-transparent mb-3">
            Code de Vérification
          </h1>
          <p class="text-gray-600 text-lg font-medium">
            Un code a été envoyé à votre email
          </p>
          <p class="text-gray-500 text-base mt-2">
            {{ email }}
          </p>
        </div>

        <!-- Message de succès -->
        <Transition name="success" mode="out-in">
          <div v-if="verificationSuccess" class="relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-green-500 to-emerald-500 rounded-3xl opacity-20 group-hover:opacity-30 transition-opacity duration-500 blur"></div>
            <div class="relative bg-green-50 bg-opacity-80 backdrop-blur-sm rounded-3xl shadow-lg border border-green-200 border-opacity-50 p-6">
              <div class="flex items-center">
                <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                  <h3 class="text-lg font-semibold text-green-800">Vérification réussie !</h3>
                  <p class="text-green-700">Redirection en cours...</p>
                </div>
              </div>
            </div>
          </div>
        </Transition>

        <!-- Formulaire de vérification -->
        <div v-if="!verificationSuccess" class="relative group">
          <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-3xl opacity-20 group-hover:opacity-30 transition-opacity duration-500 blur"></div>
          <div class="relative bg-white bg-opacity-80 backdrop-blur-sm rounded-3xl shadow-2xl border border-white border-opacity-50 p-8">
            <form @submit.prevent="verifyCode" class="space-y-6">
              <!-- Champ code de vérification -->
              <div class="space-y-3">
                <label for="verificationCode" class="block text-sm font-semibold text-gray-800">
                  Code de vérification (6 chiffres)
                </label>
                <div class="relative group/input">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within/input:text-emerald-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 12H9v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.586l4.707-4.707A1 1 0 0111 3a1 1 0 011 1v4.586l4.707 4.707A1 1 0 0117 13z"></path>
                    </svg>
                  </div>
                  <input
                    id="verificationCode"
                    v-model="code"
                    type="text"
                    pattern="[0-9]{6}"
                    maxlength="6"
                    required
                    class="w-full pl-10 pr-4 py-4 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all duration-300 bg-white bg-opacity-70 backdrop-blur-sm text-gray-900 placeholder-gray-500 font-medium text-center text-2xl tracking-widest"
                    :class="{ 
                      'border-red-400 focus:ring-red-500 focus:border-red-500 bg-red-50 bg-opacity-50': errors.code,
                      'border-gray-200 hover:border-emerald-500 hover:border-opacity-50': !errors.code
                    }"
                    placeholder="123456"
                    @input="formatCode"
                  />
                </div>
                <Transition name="error" mode="out-in">
                  <p v-if="errors.code" class="text-sm text-red-600 font-medium flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ errors.code }}
                  </p>
                </Transition>
              </div>

              <!-- Message d'erreur général -->
              <Transition name="error" mode="out-in">
                <div v-if="errors.general" class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                  <div class="flex">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-red-700 font-medium">{{ errors.general }}</p>
                  </div>
                </div>
              </Transition>

              <!-- Bouton de vérification -->
              <button
                type="submit"
                :disabled="isLoading || code.length !== 6"
                class="group relative w-full py-4 px-6 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white rounded-2xl font-semibold text-lg transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-emerald-500 focus:ring-opacity-50 disabled:opacity-60 disabled:cursor-not-allowed transform hover:scale-105 hover:shadow-2xl"
                style="box-shadow: 0 10px 30px -10px rgba(16, 185, 129, 0.4);">
                <span class="relative z-10 flex items-center justify-center">
                  <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="mr-3 h-5 w-5 text-white group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  {{ isLoading ? 'Vérification...' : 'Vérifier le code' }}
                </span>
              </button>
            </form>

            <!-- Actions supplémentaires -->
            <div class="mt-6 text-center space-y-4">
              <div>
                <button @click="resendCode" 
                  :disabled="resendCooldown > 0"
                  class="text-emerald-600 hover:text-emerald-700 font-medium transition-colors duration-200 disabled:text-gray-400 disabled:cursor-not-allowed">
                  <span v-if="resendCooldown > 0">
                    Renvoyer le code dans {{ resendCooldown }}s
                  </span>
                  <span v-else>Renvoyer le code</span>
                </button>
              </div>
              
              <div>
                <NuxtLink to="/" class="inline-flex items-center text-gray-600 hover:text-emerald-600 font-medium transition-colors duration-200 group">
                  <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                  </svg>
                  Retour à l'accueil
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
    layout: false,
    auth: false
  })

  useHead({
    title: 'Vérification - Gestion Entreprise de Résidence'
  })

  const route = useRoute()
  const router = useRouter()

  // Récupérer l'email depuis les paramètres de la route ou le localStorage
  const email = ref('')
  
  // Initialiser l'email au montage
  onMounted(() => {
    // D'abord essayer les paramètres de route
    if (route.query.email) {
      email.value = route.query.email as string
    } else if (process.client) {
      // Sinon essayer le localStorage
      const guestUser = localStorage.getItem('guest_user')
      if (guestUser) {
        try {
          const userData = JSON.parse(guestUser)
          email.value = userData.email || ''
        } catch (e) {
          console.error('Erreur lors de la lecture des données invité:', e)
        }
      }
    }
    
    // Si toujours pas d'email, rediriger vers l'accueil
    if (!email.value) {
      router.push('/')
      return
    }
    
    // Démarrer le cooldown initial
    resendCooldown.value = 60
    const interval = setInterval(() => {
      resendCooldown.value--
      if (resendCooldown.value <= 0) {
        clearInterval(interval)
      }
    }, 1000)
  })

  // État du formulaire
  const code = ref('')
  const isLoading = ref(false)
  const verificationSuccess = ref(false)

  // Gestion du cooldown pour renvoyer le code
  const resendCooldown = ref(0)

  // Gestion des erreurs
  const errors = reactive({
    code: '',
    general: ''
  })

  // Nettoyer les erreurs
  const clearErrors = () => {
    errors.code = ''
    errors.general = ''
  }

  // Formater le code (garder seulement les chiffres)
  const formatCode = () => {
    code.value = code.value.replace(/[^0-9]/g, '').slice(0, 6)
    if (errors.code) {
      clearErrors()
    }
  }

  // Vérifier le code
  const verifyCode = async () => {
    clearErrors()

    if (code.value.length !== 6) {
      errors.code = 'Le code doit contenir 6 chiffres'
      return
    }

    try {
      isLoading.value = true
      const config = useRuntimeConfig()
      
      console.log('Vérification du code:', code.value, 'pour email:', email.value)
      
      const response: any = await $fetch(`${config.public.apiBase}/guest/verify-code`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: {
          email: email.value,
          code: code.value
        }
      })

      if (response.success && response.guest) {
        // Code correct, stocker les informations de l'invité
        if (process.client) {
          localStorage.setItem('guest_user', JSON.stringify(response.guest))
        }
        
        verificationSuccess.value = true
        
        // Redirection après un délai
        setTimeout(async () => {
          await router.push('/messages')
        }, 2000)
      } else {
        errors.general = response.message || 'Erreur lors de la vérification'
      }
    } catch (error: any) {
      console.error('Erreur de vérification:', error)
      
      if (error.status === 400) {
        if (error.data?.message?.includes('Code de vérification incorrect')) {
          errors.code = 'Code de vérification incorrect'
        } else {
          errors.general = error.data?.message || 'Code expiré ou invalide'
        }
      } else {
        errors.general = 'Une erreur est survenue lors de la vérification'
      }
    } finally {
      isLoading.value = false
    }
  }

  // Renvoyer le code
  const resendCode = async () => {
    try {
      const config = useRuntimeConfig()
      
      console.log('Renvoi du code pour:', email.value)
      
      const response: any = await $fetch(`${config.public.apiBase}/guest/send-verification-code`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: {
          email: email.value
        }
      })

      if (response.success) {
        // Démarrer le cooldown
        resendCooldown.value = 60
        const interval = setInterval(() => {
          resendCooldown.value--
          if (resendCooldown.value <= 0) {
            clearInterval(interval)
          }
        }, 1000)

        console.log('Code renvoyé avec succès')
        if (response.debug_code) {
          console.log('Nouveau code de débogage:', response.debug_code)
        }
      } else {
        errors.general = 'Erreur lors du renvoi du code'
      }
      
    } catch (error: any) {
      console.error('Erreur lors du renvoi du code:', error)
      errors.general = 'Erreur lors du renvoi du code'
    }
  }


</script>

<style scoped>
/* Animations pour les transitions */
.error-enter-active, .error-leave-active {
  transition: all 0.3s ease;
}
.error-enter-from, .error-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.success-enter-active, .success-leave-active {
  transition: all 0.5s ease;
}
.success-enter-from, .success-leave-to {
  opacity: 0;
  transform: translateY(-20px) scale(0.9);
}
</style>
