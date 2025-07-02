<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white rounded-lg shadow-lg p-6 min-w-[300px] relative">
      <button @click="close" class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">×</button>
      <h3 class="text-lg font-bold mb-2">Détail de la visite</h3>
      <div v-if="visite">
        <div><b>Date :</b> {{ slotDate ? slotDate.toLocaleDateString() : '' }}</div>
        <div><b>Heure :</b> {{ slotDate ? slotDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '' }}</div>
        <div><b>Email visiteur :</b> {{ visite.email_visiteur }}</div>
        <div><b>Motif :</b> {{ visite.motif_visite }}</div>
        <div><b>Statut :</b> {{ visite.statut_visite }}</div>
        <div><b>Début :</b> {{ visite.date_visite_start }}</div>
        <div><b>Fin :</b> {{ visite.date_visite_end }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  show: Boolean,
  slotDate: Date,
  visite: Object
})
const emit = defineEmits(['close'])
function close() {
  emit('close')
}
</script>