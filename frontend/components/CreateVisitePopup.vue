<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-2xl">
      <h2 class="text-xl font-bold mb-4">Créer une visite</h2>
      <form @submit.prevent="createVisite">
        <!-- Recherche de résident (pour gardien/admin/invité) -->
        <div v-if="showResidentSearch" class="mb-4 md:col-span-2">
          <label class="block text-sm font-medium">Résident à visiter</label>
          <input v-model="residentQuery" @input="searchResidents" type="text" class="w-full border rounded px-3 py-2 mt-1" placeholder="Nom, prénom ou email..." autocomplete="off" />
          <ul v-if="residentResults.length > 0" class="bg-white border rounded shadow mt-1 max-h-32 overflow-auto">
            <li v-for="person in residentResults" :key="person.id_personne" @click="selectResident(person)" class="px-3 py-1 hover:bg-blue-100 cursor-pointer">{{ person.prenom }} {{ person.nom }} ({{ person.email }})</li>
          </ul>
          <div v-if="selectedResident" class="text-xs text-gray-600 mt-1">Résident sélectionné : {{ selectedResident.prenom }} {{ selectedResident.nom }} ({{ selectedResident.email }})</div>
        </div>
        <!-- Recherche de visiteur (optionnel, pour admin/gardien) -->
        <div v-if="showVisitorSearch" class="mb-4 md:col-span-2">
          <label class="block text-sm font-medium">Visiteur (optionnel, si compte)</label>
          <input v-model="visitorQuery" @input="searchVisitors" type="text" class="w-full border rounded px-3 py-2 mt-1" placeholder="Nom, prénom ou email..." autocomplete="off" />
          <ul v-if="visitorResults.length > 0" class="bg-white border rounded shadow mt-1 max-h-32 overflow-auto">
            <li v-for="person in visitorResults" :key="person.id_personne" @click="selectVisitor(person)" class="px-3 py-1 hover:bg-green-100 cursor-pointer">{{ person.prenom }} {{ person.nom }} ({{ person.email }})</li>
          </ul>
          <div v-if="selectedVisitor" class="text-xs text-gray-600 mt-1">Visiteur sélectionné : {{ selectedVisitor.prenom }} {{ selectedVisitor.nom }} ({{ selectedVisitor.email }})</div>
        </div>
        <!-- Saisie des infos invité (pour résident/gardien) ou invité qui s'auto-remplit -->
        <div v-if="showInviteFields" class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-2">
          <div>
            <label class="block text-sm font-medium">Nom de l'invité</label>
            <input v-model="inviteNom" type="text" class="w-full border rounded px-3 py-2 mt-1" :required="showInviteFields" />
          </div>
          <div>
            <label class="block text-sm font-medium">Prénom de l'invité</label>
            <input v-model="invitePrenom" type="text" class="w-full border rounded px-3 py-2 mt-1" :required="showInviteFields" />
          </div>
          <div>
            <label class="block text-sm font-medium">Email de l'invité</label>
            <input v-model="inviteEmail" type="email" class="w-full border rounded px-3 py-2 mt-1" :required="showInviteFields" />
          </div>
          <div>
            <label class="block text-sm font-medium">Téléphone de l'invité</label>
            <input v-model="inviteTel" type="text" class="w-full border rounded px-3 py-2 mt-1" />
          </div>
        </div>
        <!-- Motif -->
        <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-2">
          <div>
            <label class="block text-sm font-medium">Motif</label>
            <input v-model="motif" type="text" class="w-full border rounded px-3 py-2 mt-1" required />
          </div>
          <div>
            <label class="block text-sm font-medium">Début</label>
            <input v-model="start" type="datetime-local" class="w-full border rounded px-3 py-2 mt-1" required />
          </div>
          <div>
            <label class="block text-sm font-medium">Fin</label>
            <input v-model="end" type="datetime-local" class="w-full border rounded px-3 py-2 mt-1" required />
          </div>
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
  const start = ref(props.defaultStart || '')
  const end = ref('')

  // Recherche de résident (pour invité/gardien)
  const residentQuery = ref('')
  const residentResults = ref([])
  const selectedResident = ref(null)
  async function searchResidents() {
    if (residentQuery.value.length < 2) { residentResults.value = []; return; }
    // Appel API pour rechercher les résidents (à adapter côté backend si besoin)
    const res = await fetch(`${config.public.apiBase}/residents/search?q=${encodeURIComponent(residentQuery.value)}`, {
      headers: { 'Authorization': `Bearer ${authStore.token}` }
    })
    const data = await res.json()
    residentResults.value = data.residents || []
  }
  function selectResident(person) {
    selectedResident.value = person
    residentResults.value = []
    residentQuery.value = `${person.prenom} ${person.nom}`
  }

  // Saisie invité (pour résident/gardien)
  const inviteNom = ref('')
  const invitePrenom = ref('')
  const inviteEmail = ref('')
  const inviteTel = ref('')

  // Détection du rôle utilisateur
  const userRole = computed(() => authStore.userRole)
  const showResidentSearch = computed(() => userRole.value === 'invite' || userRole.value === 'gardien' || userRole.value === 'admin')
  const showInviteFields = computed(() => userRole.value === 'resident' || userRole.value === 'gardien' || userRole.value === 'admin')
  const showVisitorSearch = computed(() => userRole.value === 'gardien' || userRole.value === 'admin')
  // Recherche de visiteur (pour admin/gardien)
  const visitorQuery = ref('')
  const visitorResults = ref([])
  const selectedVisitor = ref(null)
  async function searchVisitors() {
    if (visitorQuery.value.length < 2) { visitorResults.value = []; return; }
    // Appel API pour rechercher les visiteurs (tous utilisateurs sauf résidents)
    const res = await fetch(`${config.public.apiBase}/visitors/search?q=${encodeURIComponent(visitorQuery.value)}`, {
      headers: { 'Authorization': `Bearer ${authStore.token}` }
    })
    const data = await res.json()
    visitorResults.value = data.visitors || []
  }
  function selectVisitor(person) {
    selectedVisitor.value = person
    visitorResults.value = []
    visitorQuery.value = `${person.prenom} ${person.nom}`
    // Pré-remplir les champs invité
    inviteNom.value = person.nom
    invitePrenom.value = person.prenom
    inviteEmail.value = person.email
    inviteTel.value = person.numero_telephone || ''
  }

  async function createVisite() {
    try {
      // Construction du body selon le rôle
      let body = {
        motif_visite: motif.value,
        date_visite_start: start.value.length === 16 ? start.value + ':00' : start.value,
        date_visite_end: end.value.length === 16 ? end.value + ':00' : end.value,
      }
      if (userRole.value === 'invite') {
        if (!selectedResident.value) { alert('Veuillez sélectionner un résident à visiter.'); return; }
        body.id_invite = selectedResident.value.id_personne
        body.email_visiteur = authStore.user?.email || ''
        body.statut_visite = 'en_attente'
      } else if (userRole.value === 'resident') {
        // Saisie d'un invité
        if (!inviteNom.value || !invitePrenom.value || !inviteEmail.value) { alert('Veuillez remplir toutes les infos de l\'invité.'); return; }
        body.id_invite = authStore.user?.id_personne
        body.email_visiteur = inviteEmail.value
        body.nom_invite = inviteNom.value
        body.prenom_invite = invitePrenom.value
        body.tel_invite = inviteTel.value
        body.statut_visite = 'programmee'
      } else if (userRole.value === 'gardien' || userRole.value === 'admin') {
        // Recherche résident + saisie ou recherche visiteur
        if (!selectedResident.value) { alert('Veuillez sélectionner un résident à visiter.'); return; }
        if (!inviteNom.value || !invitePrenom.value || !inviteEmail.value) { alert('Veuillez remplir toutes les infos du visiteur.'); return; }
        body.id_invite = selectedResident.value.id_personne
        body.email_visiteur = inviteEmail.value
        body.nom_invite = inviteNom.value
        body.prenom_invite = invitePrenom.value
        body.tel_invite = inviteTel.value
        body.statut_visite = 'programmee'
      }
      const response = await fetch(`${config.public.apiBase}/visites`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(body),
      })
      const text = await response.text()
      let data = {}
      try { data = JSON.parse(text) } catch (e) { alert('Erreur serveur: ' + text); return }
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


