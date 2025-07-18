
<template>
  <transition name="fade">
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-gradient-to-br from-emerald-100/80 via-cyan-100/80 to-blue-200/80 backdrop-blur-sm">
      <div class="relative w-full max-w-lg mx-auto rounded-3xl shadow-2xl border border-white/30 bg-white/95 p-0 overflow-hidden animate-fadeInUp">
        <!-- Header stylisé -->
        <div class="flex items-center justify-between px-8 pt-8 pb-4 bg-gradient-to-r from-emerald-500 to-cyan-500 rounded-t-3xl shadow">
          <div class="flex items-center gap-3">
            <div class="bg-white/30 rounded-xl p-2 flex items-center justify-center">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-white drop-shadow">Contactez l'administration</h2>
          </div>
          <button @click="$emit('close')" class="text-white hover:text-red-200 transition">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <!-- Formulaire -->
        <form @submit.prevent="handleSubmit" class="px-8 pt-6 pb-8 space-y-5">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Votre email</label>
            <input v-model="form.email" type="email" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white text-gray-900" placeholder="votre@email.com" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Message</label>
            <textarea v-model="form.message" required rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition-all duration-200 bg-gray-50 focus:bg-white text-gray-900" placeholder="Décrivez votre problème ou question..."></textarea>
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Pièces jointes</label>
            <input type="file" multiple @change="handleFiles" class="block w-full text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100" />
            <div v-if="fileNames.length" class="mt-1 text-xs text-gray-500 flex flex-wrap gap-2">
              <span v-for="name in fileNames" :key="name" class="bg-gray-100 rounded px-2 py-0.5">{{ name }}</span>
            </div>
          </div>
          <div v-if="error" class="flex items-center gap-2 text-red-600 text-sm bg-red-50 border border-red-200 rounded-xl px-3 py-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" /></svg>
            <span>{{ error }}</span>
          </div>
          <div v-if="success" class="flex items-center gap-2 text-green-700 text-sm bg-emerald-50 border border-emerald-200 rounded-xl px-3 py-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            <span>Votre message a bien été envoyé !</span>
          </div>
          <button type="submit" :disabled="loading" class="w-full py-3.5 px-6 bg-gradient-to-r from-emerald-500 to-cyan-500 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-cyan-600 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-[1.02] disabled:transform-none">
            <span v-if="loading" class="flex items-center justify-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Envoi en cours...
            </span>
            <span v-else>Envoyer</span>
          </button>
        </form>
      </div>
    </div>
  </transition>
</template>


<script setup lang="ts">
import { ref, watch } from 'vue'
const config = useRuntimeConfig()

const props = defineProps<{ show: boolean }>()
const emit = defineEmits(['close'])

const form = ref({
  email: '',
  message: ''
})
const files = ref<File[]>([])
const fileNames = ref<string[]>([])
const loading = ref(false)
const error = ref('')
const success = ref(false)

function handleFiles(e: Event) {
  const input = e.target as HTMLInputElement
  if (input.files) {
    files.value = Array.from(input.files)
    fileNames.value = files.value.map(f => f.name)
  }
}

async function handleSubmit() {
  error.value = ''
  success.value = false
  loading.value = true
  try {
    // 1. Upload files if any
    let piecesJointes: string[] = []
    if (files.value.length) {
      const formData = new FormData()
      files.value.forEach(f => formData.append('files', f))
      let uploadRes: any = null
      try {
        uploadRes = await $fetch<{ urls?: string[] }>(`${config.public.apiBase.replace(/\/?api\/?$/, '')}/api/incidents/upload`, {
          method: 'POST',
          body: formData
        })
      } catch (err: any) {
        error.value = err?.data?.message || 'Erreur lors de l\'upload des fichiers.'
        loading.value = false
        return
      }
      if (uploadRes && Array.isArray(uploadRes.urls)) {
        piecesJointes = uploadRes.urls
      } else {
        error.value = 'Erreur lors de l\'upload des fichiers.'
        loading.value = false
        return
      }
    }
    // 2. Insert incident
    await $fetch(`${config.public.apiBase.replace(/\/?api\/?$/, '')}/api/incidents`, {
      method: 'POST',
      body: {
        email: form.value.email,
        message: form.value.message,
        pieces_jointes: piecesJointes
      }
    })
    success.value = true
    setTimeout(() => {
      emit('close')
    }, 1200)
  } catch (e: any) {
    error.value = e?.data?.message || 'Erreur lors de l\'envoi.'
  } finally {
    loading.value = false
  }
}

watch(() => props.show, (val) => {
  if (val) {
    form.value.email = ''
    form.value.message = ''
    files.value = []
    fileNames.value = []
    error.value = ''
    success.value = false
  }
})
</script>
