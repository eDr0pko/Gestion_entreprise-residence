<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
      <h2 class="text-xl font-bold mb-4">Créer une visite</h2>
      <form @submit.prevent="createVisite">
        <div class="mb-4">
          <label class="block text-sm font-medium">Motif</label>
          <input v-model="motif" type="text" class="w-full border rounded px-3 py-2 mt-1" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium">Adresse mail du visiteur</label>
          <input v-model="email" type="email" class="w-full border rounded px-3 py-2 mt-1" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium">Début</label>
          <input v-model="start" type="datetime-local" class="w-full border rounded px-3 py-2 mt-1" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium">Fin</label>
          <input v-model="end" type="datetime-local" class="w-full border rounded px-3 py-2 mt-1" required />
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium">ID Invité</label>
          <input v-model="id_invite" type="text" class="w-full border rounded px-3 py-2 mt-1" required />
        </div>
        <div class="flex justify-end gap-2">
          <button type="button" @click="$emit('close')" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
            Annuler
          </button>
          <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
            Créer
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
const authStore = useAuthStore()
const config = useRuntimeConfig()

const props = defineProps({
  defaultStart: String,
})

const emit = defineEmits(['close', 'refresh'])

const motif = ref('')
const email = ref('')
const start = ref(props.defaultStart || '')
const end = ref('')
const id_invite = ref('') // Ajoute ce champ si tu veux le saisir dans le formulaire

async function createVisite() {
  try {
    const body = {
      id_invite: id_invite.value,
      email_visiteur: email.value,
      motif_visite: motif.value,
      date_visite_start: start.value.length === 16 ? start.value + ':00' : start.value,
      date_visite_end: end.value.length === 16 ? end.value + ':00' : end.value,
      statut_visite: 'programmee',
    }
    console.log('Body envoyé:', body)
    const response = await fetch(`${config.public.apiBase}/visites`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(body),
    })

    const text = await response.text()
    console.log('Réponse brute:', text)
    let data = {}
    try {
      data = JSON.parse(text)
    } catch (e) {
      alert('Erreur serveur: ' + text)
      return
    }
    if (response.ok) {
      emit('refresh')
      emit('close')
    } else {
      alert(data.message || 'Erreur lors de la création')
    }
  } catch (e) {
    console.error(e)
    alert('Erreur lors de la requête')
  }
}
</script>
