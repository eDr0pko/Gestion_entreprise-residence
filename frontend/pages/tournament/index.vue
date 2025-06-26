<template>
  <div>
    <h1 class="text-xl font-bold mb-4 text-black">Tournois :</h1>
    <div v-if="tournaments.length > 0" class="flex flex-col items-center gap-6">
      <div
        v-for="tournament in tournaments.filter(t => t.is_published == 1)"
        :key="tournament.id"
        class="w-4/5 bg-white rounded-xl shadow-lg p-6 flex flex-col gap-2 relative hover:shadow-2xl transition-shadow duration-300"
      >
        <!-- Date en haut à droite -->
        <div class="absolute top-4 right-6 flex items-center text-gray-500 text-sm">
          <svg class="w-5 h-5 mr-1 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          {{ tournament.start_date || 'Date à venir' }}
        </div>
        <div class="flex-1">
          <h2 class="text-2xl font-semibold text-indigo-700 mb-2">{{ tournament.name }}</h2>
          <div class="flex items-center text-gray-600 mb-1">
            <svg class="w-5 h-5 mr-2 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 12.414a4 4 0 10-5.657 5.657l4.243 4.243a8 8 0 1011.314-11.314l-4.243 4.243z"/>
            </svg>
            {{ tournament.location }}
          </div>
          <div class="flex items-center text-gray-600">
            <svg class="w-5 h-5 mr-2 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 0V4m0 7v7"/>
            </svg>
            {{ tournament.registration_fee ? tournament.registration_fee + ' €' : 'Gratuit' }}
          </div>
        </div>
        <!-- Bouton Voir plus en bas à droite -->
        <div class="flex justify-end mt-4">
          <NuxtLink :to="'/tournament/' + tournament.id">
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition-colors duration-200">
              Voir plus
            </button>
          </NuxtLink>
        </div>
      </div>
    </div>
    <p v-else>Aucun tournoi trouvé.</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const tournaments = ref([])

onMounted(async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/tournaments')
    tournaments.value = response.data
  } catch (error) {
    console.error('Erreur API :', error)
  }
})
</script>
