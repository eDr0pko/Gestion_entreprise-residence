<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm">
    <div class="bg-white/90 p-0 rounded-2xl shadow-2xl w-full max-w-lg border border-cyan-100 relative animate-fadein">
      <div class="flex items-center justify-between px-6 pt-6 pb-2 border-b border-cyan-100">
        <h2 class="text-2xl font-bold text-cyan-700 flex items-center gap-2">
          <svg width="28" height="28" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="#00b4d8" opacity=".15"/><path d="M12 7v5l3 3" stroke="#0097b2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Détails de la visite
        </h2>
        <button @click="$emit('close')" class="text-cyan-500 hover:text-cyan-700 text-2xl font-bold px-2 py-1 rounded-full transition-colors focus:outline-none">×</button>
      </div>
      <div v-if="visite" class="px-6 py-4 flex flex-col gap-3">
        <div class="flex items-center gap-2 mb-2">
          <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-cyan-100 text-cyan-700 uppercase tracking-wide">
            {{ getStatutLabel(visite.statut_visite) }}
          </span>
          <span v-if="visite.id_visite" class="text-xs text-gray-400 ml-2">#{{ visite.id_visite }}</span>
        </div>
        <div class="mb-2">
          <div class="text-lg font-semibold text-gray-800 flex items-center gap-2">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2" fill="#00b4d8" opacity=".15"/><path d="M8 7V5a4 4 0 1 1 8 0v2" stroke="#0097b2" stroke-width="2" stroke-linecap="round"/></svg>
            {{ visite.motif_visite || '—' }}
          </div>
        </div>
        <div class="flex flex-col md:flex-row gap-4 mb-2">
          <div class="flex-1 flex flex-col gap-1">
            <span class="text-xs text-gray-500">Début</span>
            <span class="font-mono text-sm text-cyan-700">{{ formatDate(visite.date_visite_start) }}</span>
          </div>
          <div class="flex-1 flex flex-col gap-1">
            <span class="text-xs text-gray-500">Fin</span>
            <span class="font-mono text-sm text-cyan-700">{{ formatDate(visite.date_visite_end) }}</span>
          </div>
        </div>
        <div class="flex flex-col md:flex-row gap-4 mb-2">
          <div class="flex-1 flex flex-col gap-1">
            <span class="text-xs text-gray-500">Visiteur</span>
            <span class="font-medium text-gray-700">{{ visite.email_visiteur || '—' }}</span>
          </div>
          <div class="flex-1 flex flex-col gap-1">
            <span class="text-xs text-gray-500">ID Invité</span>
            <span class="font-medium text-gray-700">{{ visite.id_invite || '—' }}</span>
          </div>
        </div>
        <div class="flex flex-col gap-1">
          <span class="text-xs text-gray-500">Durée</span>
          <span class="font-mono text-sm text-gray-700">{{ getDuree(visite.date_visite_start, visite.date_visite_end) }}</span>
        </div>
        <div v-if="visite.commentaire" class="flex flex-col gap-1">
          <span class="text-xs text-gray-500">Commentaire</span>
          <span class="text-gray-700">{{ visite.commentaire }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  visite: Object
})

function formatDate(str) {
  if (!str) return '—'
  const d = new Date(str)
  return d.toLocaleString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function getDuree(start, end) {
  if (!start || !end) return '—'
  const s = new Date(start)
  const e = new Date(end)
  const min = Math.round((e - s) / 60000)
  if (min < 60) return min + ' min'
  const h = Math.floor(min / 60)
  const m = min % 60
  return h + 'h' + (m > 0 ? ' ' + m + 'min' : '')
}

function getStatutLabel(statut) {
  switch (statut) {
    case 'programmee': return 'Programmée'
    case 'en_cours': return 'En cours'
    case 'terminee': return 'Terminée'
    case 'annule':
    case 'annulee': return 'Annulée'
    default: return statut || '—'
  }
}
</script>
