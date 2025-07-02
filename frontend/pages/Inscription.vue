<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Inscription Invité</h1>
        <p class="text-gray-600">Créez votre compte pour accéder à la résidence</p>
      </div>

      <!-- Formulaire d'inscription -->
      <form @submit.prevent="handleSubmit" v-if="step === 'register'">
        <div class="space-y-6">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
              Adresse email *
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              placeholder="votre@email.com"
              :disabled="loading"
            />
          </div>

          <div>
            <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">
              Nom *
            </label>
            <input
              id="nom"
              v-model="form.nom"
              type="text"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              placeholder="Votre nom"
              :disabled="loading"
            />
          </div>

          <div>
            <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1">
              Prénom *
            </label>
            <input
              id="prenom"
              v-model="form.prenom"
              type="text"
              required
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              placeholder="Votre prénom"
              :disabled="loading"
            />
          </div>

          <div>
            <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">
              Téléphone
            </label>
            <input
              id="telephone"
              v-model="form.telephone"
              type="tel"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
              placeholder="+33 1 23 45 67 89"
              :disabled="loading"
            />
          </div>

          <button
            type="submit"
            :disabled="loading || !isFormValid"
            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200"
          >
            <span v-if="loading" class="flex items-center justify-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Inscription en cours...
            </span>
            <span v-else>S'inscrire</span>
          </button>
        </div>
      </form>

      <!-- Formulaire de vérification -->
      <form @submit.prevent="handleVerification" v-if="step === 'verify'">
        <div class="text-center mb-6">
          <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
          </div>
          <h2 class="text-xl font-semibold text-gray-900 mb-2">Vérifiez votre email</h2>
          <p class="text-gray-600 text-sm">
            Un code de vérification a été envoyé à<br>
            <strong>{{ form.email }}</strong>
          </p>
        </div>

        <div class="space-y-6">
          <div>
            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
              Code de vérification
            </label>
            <input
              id="code"
              v-model="verificationCode"
              type="text"
              required
              maxlength="6"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-center text-2xl tracking-widest"
              placeholder="123456"
              :disabled="loading"
            />
          </div>

          <button
            type="submit"
            :disabled="loading || verificationCode.length !== 6"
            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200"
          >
            <span v-if="loading" class="flex items-center justify-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Vérification...
            </span>
            <span v-else>Vérifier</span>
          </button>

          <button
            type="button"
            @click="resendCode"
            :disabled="loading || !canResend"
            class="w-full text-blue-600 hover:text-blue-700 disabled:text-gray-400 disabled:cursor-not-allowed font-medium py-2 transition-colors duration-200"
          >
            <span v-if="canResend">Renvoyer le code</span>
            <span v-else>Renvoyer le code ({{ resendTimer }}s)</span>
          </button>
        </div>
      </form>

      <!-- Message de succès -->
      <div v-if="step === 'success'" class="text-center">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Inscription réussie !</h2>
        <p class="text-gray-600 mb-6">
          Votre compte a été créé avec succès. Vous pouvez maintenant accéder à la résidence.
        </p>
        <NuxtLink
          to="/"
          class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200"
        >
          Accéder à l'accueil
        </NuxtLink>
      </div>

      <!-- Messages d'erreur/succès -->
      <div v-if="message" class="mt-4 p-4 rounded-lg" :class="messageType === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200'">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg v-if="messageType === 'error'" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <svg v-else class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium">{{ message }}</p>
          </div>
        </div>
      </div>

      <!-- Lien de retour -->
      <div class="mt-6 text-center">
        <NuxtLink
          to="/"
          class="text-blue-600 hover:text-blue-700 text-sm font-medium transition-colors duration-200"
        >
          ← Retour à l'accueil
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: false
})

// État réactif
const step = ref('register') // 'register', 'verify', 'success'
const loading = ref(false)
const message = ref('')
const messageType = ref('error')
const verificationCode = ref('')
const canResend = ref(true)
const resendTimer = ref(0)

// Formulaire d'inscription
const form = reactive({
  email: '',
  nom: '',
  prenom: '',
  telephone: ''
})

// Validation du formulaire
const isFormValid = computed(() => {
  return form.email && 
         form.nom && 
         form.prenom && 
         form.email.includes('@') &&
         form.nom.length >= 2 &&
         form.prenom.length >= 2
})

// Configuration API
const config = useRuntimeConfig()
const apiBase = config.public.apiUrl || 'http://localhost:8000/api'

// Gestion de l'inscription
const handleSubmit = async () => {
  if (!isFormValid.value) return

  loading.value = true
  message.value = ''

  try {
    const response = await $fetch(`${apiBase}/guest/register`, {
      method: 'POST',
      body: form
    })

    if (response.success) {
      // Envoyer automatiquement le code de vérification
      await sendVerificationCode()
      step.value = 'verify'
      showMessage('Inscription réussie ! Un code de vérification a été envoyé.', 'success')
    } else {
      showMessage(response.message || 'Erreur lors de l\'inscription', 'error')
    }
  } catch (error) {
    console.error('Erreur inscription:', error)
    if (error.data?.errors) {
      const firstError = Object.values(error.data.errors)[0]
      showMessage(Array.isArray(firstError) ? firstError[0] : firstError, 'error')
    } else {
      showMessage(error.data?.message || 'Erreur lors de l\'inscription', 'error')
    }
  } finally {
    loading.value = false
  }
}

// Envoi du code de vérification
const sendVerificationCode = async () => {
  try {
    const response = await $fetch(`${apiBase}/guest/send-verification-code`, {
      method: 'POST',
      body: { email: form.email }
    })

    if (!response.success) {
      throw new Error(response.message || 'Erreur lors de l\'envoi du code')
    }

    // Démarrer le timer de renvoie
    startResendTimer()
  } catch (error) {
    console.error('Erreur envoi code:', error)
    throw error
  }
}

// Vérification du code
const handleVerification = async () => {
  if (verificationCode.value.length !== 6) return

  loading.value = true
  message.value = ''

  try {
    const response = await $fetch(`${apiBase}/guest/verify-code`, {
      method: 'POST',
      body: {
        email: form.email,
        code: verificationCode.value
      }
    })

    if (response.success) {
      step.value = 'success'
      showMessage('Email vérifié avec succès !', 'success')
    } else {
      showMessage(response.message || 'Code de vérification invalide', 'error')
    }
  } catch (error) {
    console.error('Erreur vérification:', error)
    showMessage(error.data?.message || 'Erreur lors de la vérification', 'error')
  } finally {
    loading.value = false
  }
}

// Renvoyer le code
const resendCode = async () => {
  if (!canResend.value) return

  loading.value = true
  try {
    await sendVerificationCode()
    showMessage('Code renvoyé avec succès', 'success')
    verificationCode.value = ''
  } catch (error) {
    showMessage('Erreur lors du renvoi du code', 'error')
  } finally {
    loading.value = false
  }
}

// Timer pour le renvoie de code
const startResendTimer = () => {
  canResend.value = false
  resendTimer.value = 60

  const interval = setInterval(() => {
    resendTimer.value--
    if (resendTimer.value <= 0) {
      canResend.value = true
      clearInterval(interval)
    }
  }, 1000)
}

// Affichage des messages
const showMessage = (msg, type = 'error') => {
  message.value = msg
  messageType.value = type
  
  // Effacer le message après 5 secondes pour les succès
  if (type === 'success') {
    setTimeout(() => {
      message.value = ''
    }, 5000)
  }
}

// Meta tags
useHead({
  title: 'Inscription - Gestion Résidence',
  meta: [
    { name: 'description', content: 'Inscription des invités à la résidence' }
  ]
})
</script>

<style scoped>
/* Animation pour les transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Style pour le champ code de vérification */
input[type="text"][maxlength="6"] {
  letter-spacing: 0.2em;
}
</style>
