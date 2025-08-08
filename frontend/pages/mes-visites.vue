<template>
  <div class="min-h-screen bg-gradient-to-br from-white to-teal-50 py-10 px-4">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-2xl font-bold text-center text-teal-700 mb-6">Mes visites</h1>

      <div v-if="loading" class="text-center text-gray-500">Chargement...</div>
      <div v-else-if="error" class="text-center text-red-500">{{ error }}</div>
      <div v-else-if="visites.length === 0" class="text-center text-gray-500">Aucune visite trouvée</div>

      <ul class="space-y-4" v-else>
        <li
          v-for="visite in visites"
          :key="visite.id_visite"
          class="bg-white shadow rounded-lg p-4 border-l-4 transition-all"
          :class="{
            'border-green-500': visite.statut_visite === 'terminee',
            'border-yellow-500': visite.statut_visite === 'en_cours',
            'border-blue-500': visite.statut_visite === 'programmee',
            'border-red-500': visite.statut_visite === 'annule',
            'border-gray-500': !['terminee', 'en_cours', 'programmee', 'annule'].includes(visite.statut_visite)
          }"
        >
          <h3 class="font-semibold text-lg">{{ visite.motif_visite }}</h3>
          <p class="text-sm text-gray-500">
            Du {{ formatDate(visite.date_visite_start) }} au {{ formatDate(visite.date_visite_end) }}
          </p>
          <span
            class="inline-block mt-2 px-2 py-1 text-xs font-semibold rounded-full"
            :class="{
              'bg-green-100 text-green-700': visite.statut_visite === 'terminee',
              'bg-yellow-100 text-yellow-700': visite.statut_visite === 'en_cours',
              'bg-blue-100 text-blue-700': visite.statut_visite === 'programmee',
              'bg-red-100 text-red-700': visite.statut_visite === 'annule',
              'bg-gray-200 text-gray-800': !['terminee', 'en_cours', 'programmee', 'annule'].includes(visite.statut_visite)
            }"
          >
            {{ visite.statut_visite || 'non défini' }}
          </span>

          <!-- Boutons d'action si le statut est inconnu -->
          <div
            v-if="!['terminee', 'en_cours', 'annule'].includes(visite.statut_visite)"
            class="mt-4 flex gap-2 flex-wrap"
          >
            <button
              @click="changerStatut(visite.id_visite, 'en_cours')"
              class="bg-green-100 text-green-800 px-3 py-1 rounded hover:bg-green-200 text-sm"
            >
              ✅ Accepter
            </button>
            <button
              @click="changerStatut(visite.id_visite, 'annulee')"
              class="bg-red-100 text-red-800 px-3 py-1 rounded hover:bg-red-200 text-sm"
            >
              ❌ Refuser
            </button>
            <button
              @click="changerStatut(visite.id_visite, 'banni')"
              class="bg-gray-200 text-gray-800 px-3 py-1 rounded hover:bg-gray-300 text-sm"
            >
              🚫 Bannir
            </button>
            <button
              @click="showReportMessage"
              class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded text-sm opacity-50 cursor-not-allowed"
              disabled
              :title="'Fonctionnalité non disponible'"
            >
              📅 Reporter
            </button>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth' // Protéger la page via Sanctum
})

const authStore = useAuthStore()
const config = useRuntimeConfig()

const visites = ref<any[]>([])
const loading = ref(true)
const error = ref('')

// Charger les visites
const loadVisites = async () => {
  try {
    error.value = ''
    loading.value = true

    const response = await fetch(`${config.public.apiBase}/visites`, {
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json'
      }
    })

    const data = await response.json()

    if (!response.ok || !data.success) {
      throw new Error(data.message || 'Erreur lors du chargement')
    }

    visites.value = data.visites
  } catch (err: any) {
    console.error(err)
    error.value = 'Erreur lors du chargement des visites.'
  } finally {
    loading.value = false
  }
}

// Mettre à jour le statut d'une visite
const changerStatut = async (id: number, statut: string) => {
  try {
    const response = await fetch(`${config.public.apiBase}/visites/${id}/statut`, {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${authStore.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({ statut })
    })

    const data = await response.json()

    if (!response.ok || !data.success) {
      throw new Error(data.message || 'Erreur lors du changement de statut')
    }

    await loadVisites()
  } catch (err: any) {
    console.error(err)
    error.value = 'Erreur lors du changement de statut.'
  }
}

// Formatage des dates
const formatDate = (str: string) => {
  const date = new Date(str)
  return date.toLocaleString('fr-FR', {
    day: '2-digit', month: '2-digit',
    hour: '2-digit', minute: '2-digit'
  })
}

// Fonction pour afficher un message d'information
const showReportMessage = () => {
  // Fonction temporaire - la fonctionnalité de report sera implémentée plus tard
}

onMounted(() => {
  loadVisites()
})
</script>
