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
        <div>
          <label class="block text-sm font-semibold mb-1">Pièces jointes</label>
          <input type="file" multiple @change="handleFiles" :disabled="loading" class="w-full border rounded-lg px-3 py-2" />
          <ul v-if="selectedFiles.length" class="mt-2 space-y-1">
            <li v-for="(file, idx) in selectedFiles" :key="file.name + file.size" class="flex items-center gap-2 text-sm">
              <span>{{ file.name }}</span>
              <button type="button" @click="removeFile(idx)" class="text-red-500 hover:underline">Supprimer</button>
            </li>
          </ul>
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

const selectedFiles = ref<File[]>([])

const authStore = useAuthStore()
const apiBase = useRuntimeConfig().public.apiBase

function handleFiles(e: Event) {
  const files = (e.target as HTMLInputElement).files
  if (files) {
    selectedFiles.value = Array.from(files)
  }
}

function removeFile(idx: number) {
  selectedFiles.value.splice(idx, 1)
}

async function submitIncident() {
  error.value = ''
  success.value = false
  if (!description.value.trim()) {
    error.value = 'La description est obligatoire.'
    return
  }
  loading.value = true
  try {
    const signaleurId = authStore.user && typeof authStore.user === 'object' && 'id_personne' in authStore.user
      ? (authStore.user as any).id_personne
      : undefined;

    let piecesJointesUrls: string[] = []
    if (selectedFiles.value.length) {
      // Envoi des fichiers un par un (à adapter selon l'API backend)
      const uploads = await Promise.all(selectedFiles.value.map(async (file) => {
        const formData = new FormData()
        formData.append('file', file)
        // À adapter: endpoint d'upload de fichier incident
        const res = await $fetch(`${apiBase}/admin/incidents/upload`, {
          method: 'POST',
          body: formData,
          headers: { Authorization: `Bearer ${authStore.token}` },
        })
        // On suppose que l'API retourne l'URL du fichier
        return (res as any).url || ''
      }))
      piecesJointesUrls = uploads.filter(Boolean)
    }

    await $fetch(`${apiBase}/admin/incidents`, {
      method: 'POST',
      body: {
        datetime: new Date().toISOString(),
        object: description.value,
        statut: status.value,
        id_signaleur: signaleurId,
        pieces_jointes: piecesJointesUrls.length ? JSON.stringify(piecesJointesUrls) : null
      },
      headers: { Authorization: `Bearer ${authStore.token}` },
    })
    success.value = true
    description.value = ''
    status.value = 'en_cours'
    selectedFiles.value = []
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
