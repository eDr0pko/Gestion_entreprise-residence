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
          <div class="mx-auto h-20 w-20 bg-gradient-to-br from-[#0097b2] to-[#008699] rounded-3xl flex items-center justify-center mb-8 shadow-2xl">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 01-2 2m2-2h3m-3 0h-3m-2-7a2 2 0 00-2 2v1m2-3h3m-3 0h-3m-2 7a2 2 0 012 2v1m-2-3h3m-3 0h-3"></path>
            </svg>
          </div>
          <h1 class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-[#0097b2] bg-clip-text text-transparent mb-3">
            Mot de passe oublié
          </h1>
          <p class="text-gray-600 text-lg">
            Entrez votre adresse email pour recevoir un lien de réinitialisation
          </p>
        </div>

        <!-- Formulaire -->
        <div class="relative group">
          <div class="absolute -inset-1 bg-gradient-to-r from-[#0097b2] to-[#008699] rounded-3xl opacity-20 group-hover:opacity-30 transition-opacity duration-500 blur"></div>
          <div class="relative bg-white bg-opacity-80 backdrop-blur-sm rounded-3xl shadow-2xl border border-white border-opacity-50 p-8">
            
            <!-- Bouton retour -->
            <div class="mb-6 flex justify-start">
              <NuxtLink to="/login" class="inline-flex items-center px-4 py-2 rounded-xl bg-gray-50 text-[#0097b2] border border-gray-200 hover:bg-cyan-50 text-sm font-semibold shadow-sm transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour à la connexion
              </NuxtLink>
            </div>

            <form @submit.prevent="handleReset" class="space-y-6">
              <!-- Champ email -->
              <div class="space-y-3">
                <label for="email" class="block text-sm font-semibold text-gray-800">
                  Adresse email
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                  </div>
                  <input
                    id="email"
                    v-model="email"
                    type="email"
                    required
                    class="w-full pl-10 pr-4 py-4 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-[#0097b2] focus:border-[#0097b2] transition-all duration-300 bg-white bg-opacity-70 backdrop-blur-sm text-gray-900 placeholder-gray-500 font-medium"
                    placeholder="votre.email@exemple.com"
                  />
                </div>
              </div>

              <!-- Bouton d'envoi -->
              <button
                type="submit"
                :disabled="loading"
                class="group relative w-full py-4 px-6 bg-gradient-to-r from-[#0097b2] to-[#008699] hover:from-[#008699] hover:to-[#007a94] text-white rounded-2xl font-semibold text-lg transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#0097b2] focus:ring-opacity-50 disabled:opacity-60 disabled:cursor-not-allowed transform hover:scale-105 hover:shadow-2xl"
              >
                <span class="relative z-10 flex items-center justify-center">
                  <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <svg v-else class="mr-3 h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                  </svg>
                  {{ loading ? 'Envoi en cours...' : 'Envoyer le lien' }}
                </span>
              </button>
            </form>

            <!-- Message de succès/erreur -->
            <div v-if="message" class="mt-6 p-4 rounded-xl" :class="{
              'bg-green-50 text-green-800 border border-green-200': success,
              'bg-red-50 text-red-800 border border-red-200': !success
            }">
              <div class="flex">
                <svg v-if="success" class="w-5 h-5 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <svg v-else class="w-5 h-5 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p>{{ message }}</p>
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
  title: 'Mot de passe oublié - Gestion Entreprise de Résidence'
})

const email = ref('')
const loading = ref(false)
const message = ref('')
const success = ref(false)

const handleReset = async () => {
  loading.value = true
  message.value = ''
  
  try {
    // Simulation d'envoi d'email (à implémenter selon votre backend)
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    success.value = true
    message.value = 'Un lien de réinitialisation a été envoyé à votre adresse email.'
    email.value = ''
  } catch (error) {
    success.value = false
    message.value = 'Une erreur est survenue. Veuillez réessayer.'
  } finally {
    loading.value = false
  }
}
</script>
