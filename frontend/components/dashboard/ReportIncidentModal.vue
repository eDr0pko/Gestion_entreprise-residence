<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 relative animate-fadeIn">
      <button @click="$emit('close')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-xl font-bold">&times;</button>
      <h3 class="text-xl font-bold mb-2 text-pink-600">Signaler un incident</h3>
      <form @submit.prevent="submit" class="flex flex-col gap-4">
        <div>
          <label class="block text-sm font-semibold mb-1">Description de l'incident <span class="text-red-500">*</span></label>
          <textarea v-model="description" required rows="3" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-pink-300"></textarea>
        </div>
        <div>
          <label class="block text-sm font-semibold mb-1">Statut initial</label>
          <select v-model="status" class="w-full border rounded-lg px-3 py-2">
            <option value="En cours">En cours</option>
            <option value="Signalé">Signalé</option>
          </select>
        </div>
        <div class="flex justify-end gap-2 mt-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold">Annuler</button>
          <button type="submit" :disabled="loading" class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-500 to-orange-400 text-white font-bold shadow hover:from-pink-600 hover:to-orange-500 disabled:opacity-60">
            <span v-if="loading">Envoi...</span>
            <span v-else>Envoyer</span>
          </button>
        </div>
        <div v-if="error" class="text-red-500 text-sm mt-2">{{ error }}</div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '~/stores/auth'

const emit = defineEmits(['close', 'submitted'])
const description = ref('')
const status = ref('En cours')
const loading = ref(false)
const error = ref('')

const apiBase = useRuntimeConfig().public.apiBase
const authStore = useAuthStore()

async function submit() {
  if (!description.value.trim()) {
    error.value = "La description est obligatoire."
    return
  }
  loading.value = true
  error.value = ''
  try {
    await $fetch(`${apiBase}/incidents`, {
      method: 'POST',
      body: { description: description.value, status: status.value },
      headers: { Authorization: `Bearer ${authStore.token}` },
    })
    emit('submitted')
    emit('close')
    description.value = ''
    status.value = 'En cours'
  } catch (e: any) {
    error.value = e?.data?.message || 'Erreur lors de l\'envoi.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.animate-fadeIn {
  animation: fadeIn 0.2s;
}
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
</style>
