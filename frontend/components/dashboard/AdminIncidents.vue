
<template>
  <div class="p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
      <div>
        <h2 class="text-2xl font-bold">Incidents signalés</h2>
        <p class="text-gray-500">Liste des incidents déclarés dans la résidence.</p>
      </div>
      <div class="flex gap-2 items-end">
        <div>
          <label class="block text-xs font-semibold text-gray-500 mb-1">Statut</label>
          <select v-model="filterStatut" class="border rounded px-2 py-1 text-sm">
            <option value="">Tous</option>
            <option value="a_venir">À venir</option>
            <option value="en_cours">En cours</option>
            <option value="resolu">Résolu</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-500 mb-1">Date</label>
          <input type="date" v-model="filterDate" class="border rounded px-2 py-1 text-sm" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-500 mb-1">Signaleur</label>
          <select v-model="filterSignaleur" class="border rounded px-2 py-1 text-sm min-w-[120px]">
            <option value="">Tous</option>
            <option v-for="person in signaleursList" :key="person.id" :value="person.id">
              {{ person.nomPrenom }}
            </option>
          </select>
        </div>
        <button @click="refreshIncidents" :disabled="loading" class="ml-2 px-3 py-2 rounded bg-blue-500 hover:bg-blue-600 text-white font-bold text-sm shadow disabled:opacity-60 flex items-center gap-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582M20 20v-5h-.581M5.635 19A9 9 0 003 12c0-5 4-9 9-9s9 4 9 9a9 9 0 01-1.357 4.735M19 19l-3-3m0 0l-3 3m3-3v6"/></svg>
          Actualiser
        </button>
      </div>
    </div>
    <div v-if="loading" class="text-center py-8 text-gray-400">Chargement des incidents...</div>
    <div v-else>
      <div v-if="filteredIncidents.length === 0" class="text-gray-400 italic">Aucun incident signalé.</div>
      <ul class="divide-y divide-gray-100">
        <li v-for="incident in filteredIncidents" :key="incident.id" class="py-2 flex flex-col gap-1 md:flex-row md:items-center md:gap-4">
          <div class="flex flex-col flex-1">
            <div class="flex items-center gap-2">
              <span class="text-xs text-gray-400 w-32">{{ formatDate(incident.datetime) }}</span>
              <span class="flex-1 text-sm">{{ incident.object }}</span>
              <span class="text-xs text-gray-500">{{ incident.statut }}</span>
              <span class="text-xs text-gray-700 italic min-w-[120px]">{{ getSignaleurName(incident.id_signaleur) }}</span>
            </div>
            <div v-if="parsePiecesJointes(incident.pieces_jointes).length" class="flex flex-wrap gap-2 mt-1 ml-2">
              <span class="text-xs text-gray-500">Pièces jointes :</span>
              <template v-for="(url, idx) in parsePiecesJointes(incident.pieces_jointes)" :key="url">
                <a :href="url" target="_blank" rel="noopener" class="text-blue-600 underline text-xs flex items-center gap-1">
                  <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586a4 4 0 10-5.656-5.656l-6.586 6.586"/></svg>
                  {{ getFileName(url) }}
                </a>
              </template>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
// Récupère l'URL du backend depuis la config Nuxt
const config = useRuntimeConfig()
const backendBaseUrl = config.public.apiBase?.replace(/\/api$/, '') || ''
// Utilitaire pour parser et corriger les URLs des pièces jointes
function parsePiecesJointes(pieces: any): string[] {
  let arr: string[] = []
  if (!pieces) return arr
  if (Array.isArray(pieces)) arr = pieces
  else if (typeof pieces === 'string') {
    try {
      const parsed = JSON.parse(pieces)
      if (Array.isArray(parsed)) arr = parsed
      else if (typeof parsed === 'string') arr = [parsed]
      else arr = []
    } catch {
      arr = [pieces]
    }
  }
  // Correction des chemins pour garantir /storage/incidents/...
  return arr.map(url => {
    if (!url) return ''
    // Nettoie les éventuels backslashes Windows en slashs Unix
    let cleanUrl = url.replace(/\\/g, '/')
    // Si le chemin contient déjà /storage/incidents, on garde
    if (cleanUrl.startsWith('/storage/incidents/')) {
      // Préfixe avec l'URL du backend si besoin
      return backendBaseUrl + cleanUrl
    }
    // Si le chemin contient /incidents/ mais pas /storage, on ajoute /storage devant
    if (cleanUrl.includes('/incidents/')) {
      const idx = cleanUrl.indexOf('/incidents/')
      return backendBaseUrl + '/storage' + cleanUrl.slice(idx)
    }
    // Si le chemin commence par incidents/ ou /incidents/
    if (cleanUrl.startsWith('incidents/')) return backendBaseUrl + '/storage/' + cleanUrl
    if (cleanUrl.startsWith('/incidents/')) return backendBaseUrl + '/storage' + cleanUrl
    // Sinon, retourne tel quel
    return cleanUrl
  })
}
  import { ref, computed, onMounted } from 'vue'
  import { incidents, loading, error, fetchIncidents } from '@/composables/useAdminIncidents'

  // Affichage du nom de fichier à partir de l'URL
  function getFileName(url: string) {
    try {
      return decodeURIComponent(url.split('/').pop() || url)
    } catch {
      return url
    }
  }

  const filterStatut = ref('')
  const filterDate = ref('')
  const filterSignaleur = ref('')

  // Liste des signaleurs distincts (id + nom/prénom)
  const signaleursList = computed(() => {
    // On ne dispose que de l'id du signaleur dans les incidents
    const map = new Map<string, { id: string, nomPrenom: string }>()
    incidents.value.forEach(inc => {
      if (inc.id_signaleur) {
        let nomPrenom = inc.prenom_signaleur && inc.nom_signaleur
          ? `${inc.prenom_signaleur} ${inc.nom_signaleur}`
          : `ID ${inc.id_signaleur}`
        map.set(String(inc.id_signaleur), { id: String(inc.id_signaleur), nomPrenom })
      }
    })
    return Array.from(map.values())
  })

  const filteredIncidents = computed(() => {
    return incidents.value.filter(incident => {
      let match = true
      if (filterStatut.value && incident.statut !== filterStatut.value) match = false
      if (filterDate.value) {
        const dateStr = incident.datetime?.slice(0, 10)
        if (dateStr !== filterDate.value) match = false
      }
      if (filterSignaleur.value && String(incident.id_signaleur) !== filterSignaleur.value) match = false
      return match
    })
  })

  function formatDate(datetime: string) {
    return new Date(datetime).toLocaleString('fr-FR')
  }

  function getSignaleurName(id: number | null | undefined) {
    if (!id) return '—'
    const found = incidents.value.find(inc => String(inc.id_signaleur) === String(id))
    if (found && found.prenom_signaleur && found.nom_signaleur) {
      return `${found.prenom_signaleur} ${found.nom_signaleur}`
    }
    return `ID ${id}`
  }

  function refreshIncidents() {
    fetchIncidents()
  }

  onMounted(() => {
    fetchIncidents()
  })
</script>
