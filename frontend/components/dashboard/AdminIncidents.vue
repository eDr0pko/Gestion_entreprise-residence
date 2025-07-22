
<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Incidents signalés</h2>
    <p class="mb-4 text-gray-500">Liste des incidents déclarés dans la résidence.</p>
    <div v-if="loading" class="text-center py-8 text-gray-400">Chargement des incidents...</div>
    <div v-else>
      <div class="mb-4">
        <div class="flex justify-end gap-2 mb-2 items-end">
          <div style="width:40%" class="flex flex-col">
            <label class="block text-xs text-gray-500 mb-1">Recherche globale</label>
            <input v-model="searchText" type="text" placeholder="Recherche..." class="border rounded px-2 py-1 text-sm w-full" />
          </div>
          <button @click="refreshIncidents" class="px-2 py-1 rounded bg-blue-50 text-blue-700 text-xs font-medium border border-blue-200 hover:bg-blue-100 transition">Rafraîchir</button>
        </div>
        <div class="grid grid-cols-12 gap-4 mb-2">
          <div class="col-span-2">
            <label class="block text-xs text-gray-500 mb-1">Filtrer par date</label>
            <input v-model="filterDate" type="date" class="border rounded px-2 py-1 text-sm w-full" />
          </div>
          <div class="col-span-6">
            <label class="block text-xs text-gray-500 mb-1"></label>
            <!-- Rien ici, juste l'intitulé -->
          </div>
          <div class="col-span-2">
            <label class="block text-xs text-gray-500 mb-1">Filtrer par mail</label>
            <select v-model="filterMail" class="border rounded px-2 py-1 text-sm w-full">
              <option value="">Tous les mails</option>
              <option v-for="mail in mailsList" :key="mail" :value="mail">{{ mail }}</option>
            </select>
          </div>
          <div class="col-span-2">
            <label class="block text-xs text-gray-500 mb-1">Filtrer par statut</label>
            <select v-model="filterStatut" class="border rounded px-2 py-1 text-sm w-full">
              <option value="">Tous statuts</option>
              <option value="en_cours">En cours</option>
              <option value="resolu">Résolu</option>
              <option value="a_venir">À venir</option>
            </select>
          </div>
        </div>
      </div>
      <div v-if="filteredIncidents.length === 0" class="text-gray-400 italic">Aucun incident signalé.</div>
      <ul class="divide-y divide-gray-100">
        <li v-for="incident in filteredIncidents" :key="incident.id" class="py-2 flex flex-col gap-1 md:gap-4">
          <div class="grid grid-cols-12 gap-4 items-center">
            <span class="text-xs text-gray-400 col-span-2">{{ formatDate(incident.details?.datetime) }}</span>
            <span class="text-sm col-span-6">{{ incident.details?.object }}</span>
            <span class="text-xs text-blue-600 col-span-2">
              {{ incident.details?.email_signaleur || incident.email_signaleur || incident.email || '' }}
            </span>
            <span class="text-xs text-gray-500 col-span-2">{{ incident.details?.statut }}</span>
          </div>
          <div v-if="incident.details?.pieces_jointes && incident.details.pieces_jointes.length" class="mt-1 flex flex-wrap gap-2">
            <span class="text-xs text-gray-400">Pièces jointes :</span>
            <div class="flex flex-wrap gap-2">
              <a
                v-for="(piece, idx) in incident.details.pieces_jointes"
                :key="idx"
                :href="getPieceUrl(piece)"
                target="_blank"
                class="inline-flex items-center px-2 py-1 rounded bg-orange-50 text-orange-700 text-xs font-medium border border-orange-200 hover:bg-orange-100 transition"
              >
                <svg class="w-4 h-4 mr-1 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ getFileName(piece) }}
              </a>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { onMounted } from 'vue'
  import { incidents as rawIncidents, loading, error, fetchIncidents } from '@/composables/useAdminIncidents'

  // Typage local pour inclure les propriétés manquantes
  type Incident = {
    id: number
    id_signaleur?: number | null
    details: {
      datetime: string
      object: string
      statut: string
      email_signaleur?: string
      pieces_jointes?: string[]
      [key: string]: any
    }
    email_signaleur?: string
    email?: string // fallback mail
  }

  // Conversion typée et parsing des pièces jointes
  const incidents = computed<Incident[]>(() => {
    return (rawIncidents.value as any[]).map(inc => {
      // Ensure details is parsed and present
      let detailsObj: any = {};
      if (typeof inc.details === 'string') {
        try {
          detailsObj = JSON.parse(inc.details)
        } catch {
          detailsObj = {}
        }
      } else if (typeof inc.details === 'object' && inc.details !== null) {
        detailsObj = inc.details
      }
      return {
        id: inc.id,
        id_signaleur: inc.id_signaleur,
        details: detailsObj,
        email_signaleur: inc.email_signaleur,
        email: inc.email
      }
    })
  })

  // Affichage du nom de fichier à partir de l'URL
  function getFileName(url: string) {
    try {
      return decodeURIComponent(url.split('/').pop() || url)
    } catch {
      return url
    }
  }

  // Génère l'URL publique du backend pour les pièces jointes
  const apiBase = useRuntimeConfig().public.apiBase || 'http://localhost:8000'
  function getPieceUrl(piece: string) {
    if (!piece) return ''
    if (piece.startsWith('http')) return piece
    let cleanPiece = piece.trim()
    // Correction : retire tout '/api/' au début
    if (cleanPiece.startsWith('/api/')) {
      cleanPiece = cleanPiece.replace(/^\/api\//, '/')
    }
    // Si le chemin commence par /storage ou /incidents, on le préfixe
    if (cleanPiece.startsWith('/storage') || cleanPiece.startsWith('/incidents')) {
      return apiBase.replace(/\/$/, '') + cleanPiece
    }
    // Si le chemin commence par /, on le préfixe
    if (cleanPiece.startsWith('/')) {
      return apiBase.replace(/\/$/, '') + cleanPiece
    }
    // Sinon, on le préfixe avec /storage/
    return apiBase.replace(/\/$/, '') + '/storage/' + cleanPiece
  }

  const filterStatut = ref('')
  const filterDate = ref('')
  const filterMail = ref('')
  const searchText = ref('')

  // Liste des mails distincts présents dans les incidents

  // Liste des mails distincts présents dans les incidents
  const mailsList = computed(() => {
    const set = new Set<string>()
    incidents.value.forEach(inc => {
      const mail = inc.details?.email_signaleur || inc.email_signaleur || inc.email
      if (mail) set.add(mail)
    })
    return Array.from(set)
  })

  // Liste des signaleurs distincts (id + nom/prénom)
  const signaleursList = computed(() => {
    // On ne dispose que de l'id du signaleur dans les incidents
    const map = new Map<string, { id: string, nomPrenom: string }>()
    incidents.value.forEach(inc => {
      if (inc.id_signaleur) {
        let nomPrenom = `ID ${inc.id_signaleur}`
        map.set(String(inc.id_signaleur), { id: String(inc.id_signaleur), nomPrenom })
      }
    })
    return Array.from(map.values())
  })

  const filteredIncidents = computed(() => {
    return incidents.value.filter(incident => {
      let match = true
      // Filtre statut
      if (filterStatut.value && incident.details?.statut !== filterStatut.value) match = false
      // Filtre date
      if (filterDate.value) {
        const dateStr = incident.details?.datetime?.slice(0, 10)
        if (dateStr !== filterDate.value) match = false
      }
      // Filtre mail (email_signaleur ou email)
      if (filterMail.value) {
        const mail = (incident.details?.email_signaleur || incident.email_signaleur || incident.email || '').toLowerCase()
        if (!mail.includes(filterMail.value.toLowerCase())) match = false
      }
      // Barre de recherche (object, statut, mail)
      if (searchText.value) {
        const txt = searchText.value.toLowerCase()
        const fields = [incident.details?.object, incident.details?.statut, incident.details?.email_signaleur, incident.email_signaleur, incident.email].map(f => (f || '').toLowerCase())
        if (!fields.some(f => f.includes(txt))) match = false
      }
      return match
    })
  })

  function formatDate(datetime: string) {
    return new Date(datetime).toLocaleString('fr-FR')
  }

  function refreshIncidents() {
    fetchIncidents()
  }
  onMounted(fetchIncidents)
</script>


