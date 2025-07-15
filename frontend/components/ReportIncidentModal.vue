<template>
  <div class="fixed inset-0 z-[2000] flex items-center justify-center min-h-screen min-w-full bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative animate-fade-in flex flex-col items-center justify-center">
      <button @click="$emit('close')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-xl font-bold">&times;</button>
      <h3 class="text-xl font-bold mb-2 text-orange-600 flex items-center gap-2">
        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 20H3a2 2 0 01-2-2V6a2 2 0 012-2h18a2 2 0 012 2v12a2 2 0 01-2 2z" />
        </svg>
        Signaler un incident
      </h3>
      <form @submit.prevent="submitIncident" class="flex flex-col gap-4 mt-4">
        <div>
          <label class="block text-sm font-semibold mb-1">Description de l'incident <span class="text-red-500">*</span></label>
          <textarea v-model="description" required rows="3" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400"></textarea>
        </div>
        <div>
          <label class="block text-sm font-semibold mb-1">Statut initial</label>
          <select v-model="status" class="w-full border rounded-lg px-3 py-2">
            <option value="a_venir">À venir</option>
            <option value="en_cours">En cours</option>
            <option value="resolu">Résolu</option>
          </select>
        </div>
        <div class="flex justify-end gap-2 mt-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 rounded-lg border border-gray-300 bg-gray-100 hover:bg-gray-200 text-gray-700">Annuler</button>
          <button type="submit" :disabled="loading" class="px-4 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white font-bold shadow disabled:opacity-60">Envoyer</button>
        </div>
        <div v-if="error" class="text-red-500 text-sm mt-2">{{ error }}</div>
        <div v-if="success" class="text-green-600 text-sm mt-2">Incident signalé avec succès !</div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '~/stores/auth'

const emit = defineEmits(['close'])
const description = ref('')
const status = ref('en_cours')
const loading = ref(false)
const error = ref('')
const success = ref(false)

const authStore = useAuthStore()
const apiBase = useRuntimeConfig().public.apiBase

async function submitIncident() {
  error.value = ''
  success.value = false
  if (!description.value.trim()) {
    error.value = 'La description est obligatoire.'
    return
  }
  loading.value = true
  try {
    // Correction : forcer l'id à être un nombre et log pour debug
    const signaleurId = authStore.user && typeof authStore.user === 'object' && 'id_personne' in authStore.user
      ? (authStore.user as any).id_personne
      : undefined;
    // console.log('Signaleur envoyé:', signaleurId);
    await $fetch(`${apiBase}/admin/incidents`, {
      method: 'POST',
      body: {
        datetime: new Date().toISOString(),
        object: description.value,
        statut: status.value,
        id_signaleur: signaleurId
      },
      headers: { Authorization: `Bearer ${authStore.token}` },
    })
    success.value = true
    description.value = ''
    status.value = 'en_cours'
    // Dispatch event global pour rafraîchir la liste des incidents
    window.dispatchEvent(new CustomEvent('incident-reported'))
    setTimeout(() => {
      emit('close')
    }, 1200)
  } catch (e: any) {
    error.value = e?.data?.message || 'Erreur lors de l\'envoi du signalement.'
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
