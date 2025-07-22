<template>
  <div v-if="show" class="fixed inset-0 z-[2000] flex items-center justify-center min-h-screen min-w-full bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative animate-fade-in flex flex-col items-center justify-center">
      <button @click="$emit('close')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-xl font-bold">&times;</button>
      <h3 class="text-xl font-bold mb-2 text-emerald-600 flex items-center gap-2">
        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
        </svg>
        Contacter l'administrateur
      </h3>
      <form @submit.prevent="submitContact" class="flex flex-col gap-4 mt-4" enctype="multipart/form-data">
        <div>
          <label class="block text-sm font-semibold mb-1">Votre email</label>
          <input v-model="email" type="email" required class="w-full border rounded-lg px-3 py-2" />
        </div>
        <div>
          <label class="block text-sm font-semibold mb-1">Message <span class="text-red-500">*</span></label>
          <textarea v-model="message" required rows="3" class="w-full border rounded-lg px-3 py-2"></textarea>
        </div>
        <div>
          <label class="block text-sm font-semibold mb-1">Pièces jointes</label>
          <input type="file" multiple @change="handleFiles" class="w-full border rounded-lg px-3 py-2" />
          <div v-if="files.length" class="mt-1 text-xs text-gray-600">
            <div v-for="(file, idx) in files" :key="idx">{{ file.name }}</div>
          </div>
        </div>
        <div class="flex justify-end gap-2 mt-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 rounded-lg border border-gray-300 bg-gray-100 hover:bg-gray-200 text-gray-700">Annuler</button>
          <button type="submit" :disabled="loading" class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white font-bold shadow disabled:opacity-60">Envoyer</button>
        </div>
        <div v-if="error" class="text-red-500 text-sm mt-2">{{ error }}</div>
        <div v-if="success" class="text-green-600 text-sm mt-2">Message envoyé avec succès !</div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import { useAuthStore } from '~/stores/auth'

const props = defineProps<{ show: boolean }>()
const emit = defineEmits(['close'])
const authStore = useAuthStore()

const email = ref('')
const message = ref('')
const files = ref<File[]>([])
const loading = ref(false)
const error = ref('')
const success = ref(false)

watch(() => props.show, (val) => {
  if (val) {
    email.value = (authStore.user as any)?.email || ''
    message.value = ''
    files.value = []
    error.value = ''
    success.value = false
  }
})

function handleFiles(e: Event) {
  const input = e.target as HTMLInputElement
  if (input.files) files.value = Array.from(input.files)
}

async function submitContact() {
  error.value = ''
  success.value = false
  if (!message.value.trim()) {
    error.value = 'Le message est obligatoire.'
    return
  }
  loading.value = true
  try {
    const formData = new FormData()
    // Préparer le JSON details
    const details = {
      datetime: new Date().toISOString().slice(0, 19).replace('T', ' '),
      object: `[Contact Admin] ${message.value}`,
      statut: 'a_venir',
      email_signaleur: email.value
    }
    formData.append('details', JSON.stringify(details))
    files.value.forEach(f => formData.append('pieces_jointes[]', f))

    const apiBase = useRuntimeConfig().public.apiBase
    await $fetch(`${apiBase}/contact-admin`, {
      method: 'POST',
      body: formData
    })
    success.value = true
    message.value = ''
    files.value = []
    setTimeout(() => {
      emit('close')
    }, 1200)
  } catch (e: any) {
    console.error('Erreur backend:', e)
    error.value = e?.data?.message || e?.message || 'Erreur lors de l\'envoi du message.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.2s;
}
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
</style>
