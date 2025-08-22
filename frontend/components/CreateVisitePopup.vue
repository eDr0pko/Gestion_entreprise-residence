<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-800/95 backdrop-blur-lg rounded-3xl shadow-2xl w-full max-w-4xl border border-white/20 animate-fadeIn overflow-hidden">
      <!-- Header -->
      <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 pt-6 pb-4">
        <div class="flex items-center justify-between text-white">
          <h2 class="text-2xl font-bold flex items-center gap-3">
            <div class="w-8 h-8 bg-white dark:bg-gray-800/20 rounded-xl flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
            </div>
            {{ t('planning.createVisit.title', 'Créer une visite') }}
          </h2>
          <button 
            @click="$emit('close')" 
            class="text-white/80 hover:text-white text-2xl font-bold p-2 rounded-xl hover:bg-white dark:bg-gray-800/10 transition-colors focus:outline-none"
          >
            ×
          </button>
        </div>
      </div>

      <!-- Form content -->
      <div class="px-6 py-6 max-h-[70vh] overflow-y-auto">
        <form @submit.prevent="createVisite" class="space-y-6">
          <!-- Role-based sections -->
          
          <!-- Resident search (for guests/guards/admins) -->
          <div v-if="showResidentSearch" class="space-y-4">
            <div class="bg-blue-50 rounded-2xl p-4 border border-blue-100">
              <label class="block text-sm font-semibold text-blue-900 mb-3">
                {{ t('planning.createVisit.residentToVisit', 'Résident à visiter') }}
              </label>
              <div class="relative">
                <input 
                  v-model="residentQuery" 
                  @input="searchResidents" 
                  type="text" 
                  class="w-full bg-white dark:bg-gray-800 border border-blue-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                  :placeholder="t('planning.createVisit.searchPlaceholder', 'Nom, prénom ou email...')" 
                  autocomplete="off" 
                />
                <svg class="absolute right-3 top-3 w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
              
              <ul v-if="residentResults.length > 0" class="mt-2 bg-white dark:bg-gray-800 border border-blue-200 rounded-xl shadow-lg max-h-32 overflow-auto">
                <li 
                  v-for="person in residentResults" 
                  :key="person.id_personne" 
                  @click="selectResident(person)" 
                  class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-blue-100 last:border-b-0 transition-colors"
                >
                  <div class="font-medium text-gray-900 dark:text-white">{{ person.prenom }} {{ person.nom }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ person.email }}</div>
                </li>
              </ul>
              
              <div v-if="selectedResident" class="mt-3 p-3 bg-green-50 border border-green-200 rounded-xl">
                <div class="flex items-center gap-2 text-green-800">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  <span class="text-sm font-medium">Résident sélectionné :</span>
                </div>
                <div class="mt-1 text-sm text-green-700">
                  {{ selectedResident.prenom }} {{ selectedResident.nom }} ({{ selectedResident.email }})
                </div>
              </div>
            </div>
          </div>

          <!-- Visitor search (for guards/admins) -->
          <div v-if="showVisitorSearch" class="space-y-4">
            <div class="bg-green-50 rounded-2xl p-4 border border-green-100">
              <label class="block text-sm font-semibold text-green-900 mb-3">
                {{ t('planning.createVisit.visitor', 'Visiteur (optionnel, si compte)') }}
              </label>
              <div class="relative">
                <input 
                  v-model="visitorQuery" 
                  @input="searchVisitors" 
                  type="text" 
                  class="w-full bg-white dark:bg-gray-800 border border-green-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                  :placeholder="t('planning.createVisit.searchPlaceholder', 'Nom, prénom ou email...')" 
                  autocomplete="off" 
                />
                <svg class="absolute right-3 top-3 w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
              
              <ul v-if="visitorResults.length > 0" class="mt-2 bg-white dark:bg-gray-800 border border-green-200 rounded-xl shadow-lg max-h-32 overflow-auto">
                <li 
                  v-for="person in visitorResults" 
                  :key="person.id_personne" 
                  @click="selectVisitor(person)" 
                  class="px-4 py-3 hover:bg-green-50 cursor-pointer border-b border-green-100 last:border-b-0 transition-colors"
                >
                  <div class="font-medium text-gray-900 dark:text-white">{{ person.prenom }} {{ person.nom }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ person.email }}</div>
                </li>
              </ul>
              
              <div v-if="selectedVisitor" class="mt-3 p-3 bg-green-100 border border-green-300 rounded-xl">
                <div class="flex items-center gap-2 text-green-800">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  <span class="text-sm font-medium">Visiteur sélectionné :</span>
                </div>
                <div class="mt-1 text-sm text-green-700">
                  {{ selectedVisitor.prenom }} {{ selectedVisitor.nom }} ({{ selectedVisitor.email }})
                </div>
              </div>
            </div>
          </div>

          <!-- Guest information fields -->
          <div v-if="showInviteFields" class="space-y-4">
            <div class="bg-purple-50 rounded-2xl p-4 border border-purple-100">
              <h3 class="text-sm font-semibold text-purple-900 mb-4">
                {{ t('planning.createVisit.guestInfo', 'Informations de l\'invité') }}
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-purple-800 mb-2">
                    {{ t('planning.createVisit.lastName', 'Nom de l\'invité') }}
                  </label>
                  <input 
                    v-model="inviteNom" 
                    type="text" 
                    class="w-full bg-white dark:bg-gray-800 border border-purple-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                    :required="showInviteFields" 
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-purple-800 mb-2">
                    {{ t('planning.createVisit.firstName', 'Prénom de l\'invité') }}
                  </label>
                  <input 
                    v-model="invitePrenom" 
                    type="text" 
                    class="w-full bg-white dark:bg-gray-800 border border-purple-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                    :required="showInviteFields" 
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-purple-800 mb-2">
                    {{ t('planning.createVisit.email', 'Email de l\'invité') }}
                  </label>
                  <input 
                    v-model="inviteEmail" 
                    type="email" 
                    class="w-full bg-white dark:bg-gray-800 border border-purple-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                    :required="showInviteFields" 
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-purple-800 mb-2">
                    {{ t('planning.createVisit.phone', 'Téléphone de l\'invité') }}
                  </label>
                  <input 
                    v-model="inviteTel" 
                    type="tel"
                    placeholder="+33 6 12 34 56 78"
                    class="w-full bg-white dark:bg-gray-800 border border-purple-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Visit details -->
          <div class="space-y-4">
            <div class="bg-gray-50 dark:bg-gray-900 rounded-2xl p-4 border border-gray-200 dark:border-gray-700">
              <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                {{ t('planning.createVisit.visitDetails', 'Détails de la visite') }}
              </h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-1">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ t('planning.createVisit.purpose', 'Motif') }}
                  </label>
                  <input 
                    v-model="motif" 
                    type="text" 
                    class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    required 
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ t('planning.createVisit.startTime', 'Début') }}
                  </label>
                  <input 
                    v-model="start" 
                    type="datetime-local" 
                    class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    required 
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ t('planning.createVisit.endTime', 'Fin') }}
                  </label>
                  <input 
                    v-model="end" 
                    type="datetime-local" 
                    class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                    required 
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Action buttons -->
          <div class="flex justify-end gap-3 pt-4">
            <button 
              type="button" 
              @click="$emit('close')" 
              class="px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500"
            >
              {{ t('planning.createVisit.cancel', 'Annuler') }}
            </button>
            <button 
              type="submit" 
              class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              {{ t('planning.createVisit.create', 'Créer') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { onMounted, ref, computed } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useAuthStore } from '@/stores/auth'

// Import du système de thème
  const { initTheme } = useTheme()
  const { t } = useI18n()
  const authStore = useAuthStore()
  const config = useRuntimeConfig()

  const props = defineProps({
    defaultStart: String,
    defaultEnd: String,
  })

  const emit = defineEmits(['close', 'refresh'])

  const motif = ref('')
  const start = ref(props.defaultStart || '')
  const end = ref(props.defaultEnd || '')

  // Recherche de résident (pour invité/gardien)
  const residentQuery = ref('')
  const residentResults = ref([])
  const selectedResident = ref(null)

  async function searchResidents() {
    if (residentQuery.value.length < 2) { 
      residentResults.value = []
      return
    }
    
    try {
      const res = await fetch(`${config.public.apiBase}/residents/search?q=${encodeURIComponent(residentQuery.value)}`, {
        headers: { 'Authorization': `Bearer ${authStore.token}` }
      })
      const data = await res.json()
      residentResults.value = data.residents || []
    } catch (error) {
      console.error('Error searching residents:', error)
      residentResults.value = []
    }
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
  const showResidentSearch = computed(() => 
    userRole.value === 'invite' || userRole.value === 'gardien' || userRole.value === 'admin'
  )
  const showInviteFields = computed(() => 
    userRole.value === 'resident' || userRole.value === 'gardien' || userRole.value === 'admin'
  )
  const showVisitorSearch = computed(() => 
    userRole.value === 'gardien' || userRole.value === 'admin'
  )

  // Recherche de visiteur (pour admin/gardien)
  const visitorQuery = ref('')
  const visitorResults = ref([])
  const selectedVisitor = ref(null)

  async function searchVisitors() {
    if (visitorQuery.value.length < 2) { 
      visitorResults.value = []
      return
    }
    
    try {
      const response = await fetch(`${runtimeConfig.public.apiBase}/visitors/search?q=${visitorQuery.value}`, {
        headers: { 'Authorization': `Bearer ${authStore.token}` }
      })
      const data = await response.json()
      visitorResults.value = data.visitors || []
    } catch (error) {
      console.error('Error searching visitors:', error)
      visitorResults.value = []
    }
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
        if (!selectedResident.value) { 
          alert(t('planning.createVisit.errors.selectResident', 'Veuillez sélectionner un résident à visiter.'))
          return
        }
        body.id_invite = selectedResident.value.id_personne
        body.email_visiteur = authStore.user?.email || ''
        body.statut_visite = 'en_attente'
      } else if (userRole.value === 'resident') {
        // Saisie d'un invité
        if (!inviteNom.value || !invitePrenom.value || !inviteEmail.value) { 
          alert(t('planning.createVisit.errors.fillGuestInfo', 'Veuillez remplir toutes les infos de l\'invité.'))
          return
        }
        body.id_invite = authStore.user?.id_personne
        body.email_visiteur = inviteEmail.value
        body.nom_invite = inviteNom.value
        body.prenom_invite = invitePrenom.value
        body.tel_invite = inviteTel.value
        body.statut_visite = 'programmee'
      } else if (userRole.value === 'gardien' || userRole.value === 'admin') {
        // Recherche résident + saisie ou recherche visiteur
        if (!selectedResident.value) { 
          alert(t('planning.createVisit.errors.selectResident', 'Veuillez sélectionner un résident à visiter.'))
          return
        }
        if (!inviteNom.value || !invitePrenom.value || !inviteEmail.value) { 
          alert(t('planning.createVisit.errors.fillVisitorInfo', 'Veuillez remplir toutes les infos du visiteur.'))
          return
        }
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
      try { 
        data = JSON.parse(text) 
      } catch (e) { 
        alert(t('planning.createVisit.errors.serverError', 'Erreur serveur: ') + text)
        return 
      }
      
      if (response.ok) {
        emit('refresh')
        emit('close')
      } else {
        alert(data.message || t('planning.createVisit.errors.creationFailed', 'Erreur lors de la création'))
      }
    } catch (e) {
      console.error(e)
      alert(t('planning.createVisit.errors.requestFailed', 'Erreur lors de la requête'))
    }
  }

  // Initialisation du thème
  onMounted(() => {
    initTheme()
  })
</script>

<style scoped>
  .animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(20px) scale(0.95);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  /* Custom scrollbar for form */
  .overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.3) transparent;
  }

  .overflow-y-auto::-webkit-scrollbar {
    width: 6px;
  }

  .overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.3);
    border-radius: 3px;
  }

  .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.5);
  }
</style>


