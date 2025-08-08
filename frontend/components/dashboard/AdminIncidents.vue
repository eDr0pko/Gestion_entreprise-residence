
<template>
  <div class="p-4 sm:p-6">
    <h2 class="text-2xl font-bold mb-4">{{ $t('adminIncidents.title') }}</h2>
    <p class="mb-4 text-gray-500">{{ $t('adminIncidents.subtitle') }}</p>
    <div v-if="loading" class="text-center py-8 text-gray-400">{{ $t('adminIncidents.loading') }}</div>
    <div v-else-if="error" class="text-center py-8 text-red-500">{{ error }}</div>
    <div v-else>
      <div class="mb-4">
        <div class="flex flex-wrap justify-end gap-2 mb-2 items-end">
          <div class="flex-1 min-w-[180px] max-w-xs flex flex-col">
            <label class="block text-xs text-gray-500 mb-1">{{ $t('adminIncidents.globalSearch') }}</label>
            <input v-model="searchText" type="text" :placeholder="$t('adminIncidents.searchPlaceholder')" class="border rounded px-2 py-1 text-sm w-full" />
          </div>
          <button @click="refreshIncidents" class="px-2 py-1 rounded bg-blue-50 text-blue-700 text-xs font-medium border border-blue-200 hover:bg-blue-100 transition w-full sm:w-auto">{{ $t('adminIncidents.refresh') }}</button>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-2">
          <div>
            <label class="block text-xs text-gray-500 mb-1">{{ $t('adminIncidents.filterDate') }}</label>
            <input v-model="filterDate" type="date" class="border rounded px-2 py-1 text-sm w-full" />
          </div>
          <div class="hidden md:block"></div>
          <div>
            <label class="block text-xs text-gray-500 mb-1">{{ $t('adminIncidents.filterMail') }}</label>
            <select v-model="filterMail" class="border rounded px-2 py-1 text-sm w-full">
              <option value="">{{ $t('adminIncidents.allMails') }}</option>
              <option v-for="mail in mailsList" :key="mail" :value="mail">{{ mail }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-500 mb-1">{{ $t('adminIncidents.filterStatus') }}</label>
            <select v-model="filterStatut" class="border rounded px-2 py-1 text-sm w-full">
              <option value="">{{ $t('adminIncidents.allStatus') }}</option>
              <option value="en_cours">{{ $t('adminIncidents.statusEnCours') }}</option>
              <option value="resolu">{{ $t('adminIncidents.statusResolu') }}</option>
              <option value="a_venir">{{ $t('adminIncidents.statusAVenir') }}</option>
            </select>
          </div>
        </div>
      </div>
      <div v-if="filteredIncidents.length === 0" class="text-gray-400 italic">{{ $t('adminIncidents.noIncidents') }}</div>
      <ul class="divide-y divide-gray-100">
        <li v-for="incident in filteredIncidents" :key="incident.id" class="py-2 flex flex-col gap-1 md:gap-4">
          <div class="grid grid-cols-2 md:grid-cols-12 gap-2 md:gap-4 items-center">
            <span class="text-xs text-gray-400 md:col-span-2">{{ formatDate(incident.details?.datetime) }}</span>
            <span class="text-sm md:col-span-6 col-span-1">{{ incident.details?.object }}</span>
            <span class="text-xs text-blue-600 md:col-span-2 col-span-1">
              {{ incident.details?.email_signaleur || incident.email_signaleur || incident.email || '' }}
            </span>
            <span class="text-xs text-gray-500 md:col-span-2 col-span-2">{{ incident.details?.statut }}</span>
          </div>
          <div v-if="incident.details?.pieces_jointes && incident.details.pieces_jointes.length" class="mt-1 flex flex-wrap gap-2">
            <span class="text-xs text-gray-400">{{ $t('adminIncidents.attachments') }}</span>
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
import { onMounted, computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

  // Utilisation du nouveau composable
  const {
    incidents: rawIncidents,
    loadingIncidents: loading,
    errorIncidents: error,
    fetchIncidents
  } = useAdminData()

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
    const incidentsArray = Array.isArray(rawIncidents.value) ? rawIncidents.value : []
    return incidentsArray.map((inc: any) => {
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
      
      // Si pas de details, utiliser les propriétés de niveau racine
      if (!detailsObj.datetime && inc.datetime) detailsObj.datetime = inc.datetime
      if (!detailsObj.object && inc.object) detailsObj.object = inc.object
      if (!detailsObj.statut && inc.statut) detailsObj.statut = inc.statut
      if (!detailsObj.email_signaleur && inc.email_signaleur) detailsObj.email_signaleur = inc.email_signaleur
      if (!detailsObj.pieces_jointes && inc.pieces_jointes) detailsObj.pieces_jointes = inc.pieces_jointes
      
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
  // Correction : retire tout /api à la fin de l'apiBase pour servir les fichiers statiques
  let apiBase = useRuntimeConfig().public.apiBase || 'http://localhost:8000'
  apiBase = apiBase.replace(/\/api\/?$/, '')
  function getPieceUrl(piece: string) {
    if (!piece) return ''
    if (piece.startsWith('http')) return piece
    let cleanPiece = piece.trim()
    // Retire tous les '/api/' au début (même plusieurs)
    cleanPiece = cleanPiece.replace(/^\/api\//, '/').replace(/^\/api\//, '/').replace(/^\/api\//, '/')
    // Force le préfixe /storage/ si le chemin contient 'incidents/' mais ne commence pas par /storage
    if (cleanPiece.includes('incidents/') && !cleanPiece.startsWith('/storage')) {
      cleanPiece = '/storage/' + cleanPiece.split('incidents/').pop()
    }
    // Si le chemin commence par /storage, on le préfixe
    if (cleanPiece.startsWith('/storage')) {
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
    return new Date(datetime).toLocaleString()
  }

  function refreshIncidents() {
    fetchIncidents()
  }
  onMounted(() => {
    console.log('[AdminIncidents] Component mounted')
    console.log('[AdminIncidents] Initial rawIncidents:', rawIncidents.value)
    console.log('[AdminIncidents] Initial loading:', loading.value)
    console.log('[AdminIncidents] Initial error:', error.value)
    fetchIncidents()
  })

  // Watcher pour debugger les changements
  watch([rawIncidents, loading, error], ([newIncidents, newLoading, newError]) => {
    console.log('[AdminIncidents] State changed:')
    console.log('  - rawIncidents:', newIncidents)
    console.log('  - rawIncidents length:', Array.isArray(newIncidents) ? newIncidents.length : 'not array')
    console.log('  - loading:', newLoading)
    console.log('  - error:', newError)
  }, { immediate: true })
</script>


