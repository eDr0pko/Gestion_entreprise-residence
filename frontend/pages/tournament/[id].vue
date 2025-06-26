<template>
  <div v-if="tournament" class="min-h-screen bg-gray-50 py-12 px-4 flex flex-col items-center">
    <!-- Bouton retour en haut à droite -->
    <div class="w-full flex justify-end mb-8">
      <NuxtLink to="/tournament">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition-colors duration-200 font-semibold">
          Retour aux tournois
        </button>
      </NuxtLink>
    </div>

    <!-- Titre principal -->
    <h1 class="text-4xl font-extrabold text-indigo-700 mb-2 text-center">{{ tournament.name }}</h1>

    <!-- Infos principales -->
    <div class="flex flex-col md:flex-row md:justify-center gap-6 mb-8">
      <div class="flex items-center text-gray-700">
        <svg class="w-6 h-6 mr-2 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 12.414a4 4 0 10-5.657 5.657l4.243 4.243a8 8 0 1011.314-11.314l-4.243 4.243z"/>
        </svg>
        <span class="font-medium">{{ tournament.location }}</span>
      </div>
      <div class="flex items-center text-gray-700">
        <svg class="w-5 h-5 mr-2 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        {{ tournament.start_date || 'Date à venir' }}
      </div>
      <div class="flex items-center text-gray-700">
        <svg class="w-6 h-6 mr-2 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 7v7"/>
        </svg>
        <span class="font-medium">{{ tournament.registration_fee ? tournament.registration_fee + ' €' : 'Gratuit' }}</span>
      </div>
    </div>

    <!-- Boutons d'action -->
    <div class="flex flex-col md:flex-row gap-4 mb-8">
      <NuxtLink :to="`/tournament/${tournament.id}/tableaux`">
        <button class="px-6 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition-colors duration-200 font-semibold">
          Voir les tableaux
        </button>
      </NuxtLink>
      <NuxtLink :to="`/tournament/${tournament.id}/participants`">
        <button class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition-colors duration-200 font-semibold">
          Voir les inscrits
        </button>
      </NuxtLink>
    </div>

    <!-- Description -->
    <div v-if="tournament.description" class="max-w-2xl bg-white rounded-xl shadow p-6">
      <h2 class="text-lg font-semibold text-indigo-600 mb-2">Description</h2>
      <p class="text-gray-700">{{ tournament.description }}</p>
    </div>
  </div>
  <p v-else class="text-center text-red-500 mt-12">Erreur : ce tournoi n'est pas accessible.</p>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const tournaments = ref([])
const route = useRoute()

onMounted(async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/tournaments')
    tournaments.value = response.data
  } catch (error) {
    console.error('Erreur API :', error)
  }
})

const tournament = computed(() =>
  tournaments.value.find(
    t => t.is_published == 1 && t.id == route.params.id
  )
)
</script>