<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
      <h2 class="text-xl font-bold mb-4">Détails de la visite</h2>
      <div v-if="visite">
        <p><strong>Motif :</strong> {{ visite.motif_visite }}</p>
        <p><strong>Début :</strong> {{ formatDate(visite.date_visite_start) }}</p>
        <p><strong>Fin :</strong> {{ formatDate(visite.date_visite_end) }}</p>
        <p><strong>Statut :</strong> {{ visite.statut_visite }}</p>
      </div>
      <div class="mt-4 text-right">
        <button @click="$emit('close')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">
          Fermer
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  visite: Object
})

function formatDate(str) {
  const d = new Date(str)
  return d.toLocaleString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
