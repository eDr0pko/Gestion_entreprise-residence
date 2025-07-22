<template>
  <!--
    =====================================================================
    Vue de gestion des administrateurs, gardiens et résidents
    ---------------------------------------------------------------------
    - Permet d'afficher, rechercher, filtrer et modifier les utilisateurs
    - Ajout du switch carte/liste, barre de recherche globale, filtres avancés
    - Structure et commentaires professionnels pour une meilleure maintenabilité
    =====================================================================
  -->
  <div class="p-6">
    <!-- Header et switch vue -->
    <div class="flex flex-col gap-4 mb-6">
      <div class="flex items-center justify-between gap-4 flex-wrap">
        <h2 class="text-2xl font-bold">Gestion des administrateurs, gardiens et résidents</h2>
        <button @click="toggleView" class="px-3 py-1 rounded bg-gray-100 text-gray-700 border border-gray-300 hover:bg-gray-200 text-sm">
          {{ isListView ? 'Affichage par cartes' : 'Affichage par liste' }}
        </button>
      </div>
      <!-- Filtres avancés -->
      <div class="grid grid-cols-12 gap-4 items-end">
        <div class="col-span-4">
          <label class="block text-xs text-gray-500 mb-1">Recherche globale</label>
          <input v-model="searchQuery" @input="filterPersons" type="text" placeholder="Recherche..." class="input w-full" />
        </div>
        <div class="col-span-3">
          <label class="block text-xs text-gray-500 mb-1">Filtrer par rôle</label>
          <select v-model="filterRole" @change="filterPersons" class="input w-full">
            <option value="">Tous</option>
            <option value="admin">Administrateur</option>
            <option value="gardien">Gardien</option>
            <option value="resident">Résident</option>
          </select>
        </div>
      </div>
    </div>
  <!-- Modal ajout utilisateur - étape 1 : choix des rôles -->
  <div v-if="addUserStep === 1" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md relative">
      <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-600" @click="closeAddUser">✕</button>
      <h3 class="text-lg font-bold mb-4">Ajouter un utilisateur</h3>
      <div class="mb-3">
        <label class="block text-sm font-medium mb-1">Rôles <span class="text-red-500">*</span></label>
        <div class="flex gap-4">
          <label class="flex items-center gap-1">
            <input type="checkbox" value="admin" v-model="addUserForm.roles" />
            <span>Administrateur</span>
          </label>
          <label class="flex items-center gap-1">
            <input type="checkbox" value="gardien" v-model="addUserForm.roles" />
            <span>Gardien</span>
          </label>
          <label class="flex items-center gap-1">
            <input type="checkbox" value="resident" v-model="addUserForm.roles" />
            <span>Résident</span>
          </label>
        </div>
        <div v-if="addUserForm.roles.length === 0" class="text-xs text-red-500 mt-1">Au moins un rôle doit être sélectionné.</div>
      </div>
      <div class="flex justify-end gap-2 mt-4">
        <button type="button" @click="closeAddUser" class="px-3 py-1 rounded bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200 text-sm">Annuler</button>
        <button type="button" @click="proceedAddUserStep2" :disabled="addUserForm.roles.length === 0" class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700 text-sm">Suivant</button>
      </div>
    </div>
  </div>

  <!-- Modal ajout utilisateur - étape 2 : formulaire -->
  <div v-if="addUserStep === 2" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md relative">
      <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-600" @click="closeAddUser">✕</button>
      <h3 class="text-lg font-bold mb-4">Informations utilisateur</h3>
      <form @submit.prevent="submitAddUser">
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Nom</label>
          <input v-model="addUserForm.nom" class="input w-full" required />
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Prénom</label>
          <input v-model="addUserForm.prenom" class="input w-full" required />
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Email</label>
          <input v-model="addUserForm.email" class="input w-full" required type="email" />
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Téléphone</label>
          <input v-model="addUserForm.numero_telephone" class="input w-full" required />
        </div>
        <div class="mb-3" v-if="addUserForm.roles.includes('resident')">
          <label class="block text-sm font-medium mb-1">Adresse logement</label>
          <input v-model="addUserForm.adresse_logement" class="input w-full" />
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Mot de passe</label>
          <input v-model="addUserForm.password" class="input w-full" type="password" required />
        </div>
        <div class="flex justify-end gap-2 mt-4">
          <button type="button" @click="closeAddUser" class="px-3 py-1 rounded bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200 text-sm">Annuler</button>
          <button type="submit" class="px-3 py-1 rounded bg-green-600 text-white hover:bg-green-700 text-sm">Créer</button>
        </div>
      </form>
    </div>
  </div>
    <div v-if="loading" class="text-center py-8 text-gray-500">Chargement...</div>
    <div v-else>
      <!-- Affichage par cartes -->
      <div v-if="!isListView">
        <div v-for="cat in categories" :key="cat.key" class="mb-10">
          <h3 class="text-xl font-semibold mb-3 capitalize">{{ cat.label }}</h3>
          <div v-if="filteredPersons[cat.key].length === 0" class="text-gray-400 italic mb-4">Aucun {{ cat.label.toLowerCase() }}</div>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-for="person in filteredPersons[cat.key]" :key="person.id" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex flex-col gap-2 relative group hover:shadow-md transition">
              <!-- Carte utilisateur -->
              <div class="flex items-center gap-3">
                <img v-if="person.photo_profil" :src="getAvatarUrl(person.photo_profil)" class="w-12 h-12 rounded-full object-cover border" alt="avatar" />
                <div v-else class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-lg font-bold text-gray-500">{{ person.prenom[0] }}{{ person.nom[0] }}</div>
                <div>
                  <div class="font-bold text-lg">{{ person.prenom }} {{ person.nom }}</div>
                  <div class="text-xs text-gray-500">{{ person.email }}</div>
                  <div class="text-xs text-gray-400 mt-1 flex flex-wrap gap-1">
                    <span v-for="role in person.roles" :key="role" :class="roleBadgeClass(role)">
                      {{ roleLabel(role) }}
                    </span>
                  </div>
                  <div v-if="person.niveau_acces" class="text-xs text-blue-500">Niveau accès : {{ person.niveau_acces }}</div>
                  <div v-if="person.adresse_logement" class="text-xs text-green-500">Logement : {{ person.adresse_logement }}</div>
                </div>
              </div>
              <div class="flex flex-col gap-1 mt-2">
                <div class="text-sm"><span class="font-medium">Téléphone :</span> {{ formatPhoneNumber(person.numero_telephone) }}</div>
                <div v-if="person.date_nomination" class="text-xs text-gray-400">Nommé le : {{ formatDate(person.date_nomination) }}</div>
              </div>
              <div class="flex gap-2 mt-3">
                <button @click="editPerson(person)" class="px-3 py-1 rounded bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 text-sm">Modifier</button>
                <button @click="deletePerson(person)" class="px-3 py-1 rounded bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 text-sm">Supprimer</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Affichage par liste -->
      <div v-else>
        <table class="min-w-full rounded-xl overflow-hidden shadow-lg bg-white">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Prénom</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rôles</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date nomination</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Adresse logement</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="person in filteredList" :key="person.id">
              <td class="px-4 py-2">{{ person.nom }}</td>
              <td class="px-4 py-2">{{ person.prenom }}</td>
              <td class="px-4 py-2">{{ person.email }}</td>
              <td class="px-4 py-2">{{ formatPhoneNumber(person.numero_telephone) }}</td>
              <td class="px-4 py-2">
                <span v-for="role in person.roles" :key="role" :class="roleBadgeClass(role)">{{ roleLabel(role) }}</span>
              </td>
              <td class="px-4 py-2">{{ formatDate(person.date_nomination ?? '') }}</td>
              <td class="px-4 py-2">{{ person.adresse_logement }}</td>
              <td class="px-4 py-2">
                <button @click="editPerson(person)" class="px-2 py-1 rounded bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 text-xs">Modifier</button>
                <button @click="deletePerson(person)" class="px-2 py-1 rounded bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 text-xs">Supprimer</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal édition -->
    <div v-if="editModal" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md relative">
        <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-600" @click="editModal = false">✕</button>
        <h3 class="text-lg font-bold mb-4">Modifier {{ editingPerson?.prenom }} {{ editingPerson?.nom }}</h3>
        <form @submit.prevent="submitEdit">
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">Nom</label>
            <input v-model="editForm.nom" class="input w-full" required />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">Prénom</label>
            <input v-model="editForm.prenom" class="input w-full" required />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">Email</label>
            <input v-model="editForm.email" class="input w-full" required type="email" />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">Téléphone</label>
            <input v-model="editForm.numero_telephone" class="input w-full" required />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">Rôles <span class="text-red-500">*</span></label>
            <div class="flex gap-4">
              <label class="flex items-center gap-1">
                <input type="checkbox" value="admin" v-model="editForm.roles" :disabled="editForm.roles.length === 1 && editForm.roles.includes('admin')" />
                <span>Administrateur</span>
              </label>
              <label class="flex items-center gap-1">
                <input type="checkbox" value="gardien" v-model="editForm.roles" :disabled="editForm.roles.length === 1 && editForm.roles.includes('gardien')" />
                <span>Gardien</span>
              </label>
              <label class="flex items-center gap-1">
                <input type="checkbox" value="resident" v-model="editForm.roles" :disabled="editForm.roles.length === 1 && editForm.roles.includes('resident')" />
                <span>Résident</span>
              </label>
            </div>
            <div v-if="editForm.roles.length === 0" class="text-xs text-red-500 mt-1">Au moins un rôle doit être sélectionné.</div>
          </div>
          <div class="mb-3" v-if="editForm.roles.includes('resident')">
            <label class="block text-sm font-medium mb-1">Adresse logement</label>
            <input v-model="editForm.adresse_logement" class="input w-full" />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">Nouveau mot de passe</label>
            <input v-model="editForm.new_password" class="input w-full" type="password" placeholder="Laisser vide pour ne pas changer" />
          </div>
          <div class="mb-3" v-if="editingPerson?.photo_profil">
            <label class="block text-sm font-medium mb-1">Photo de profil</label>
            <div class="flex items-center gap-2">
              <img :src="getAvatarUrl(editingPerson.photo_profil)" class="w-10 h-10 rounded-full object-cover border" alt="avatar" />
              <button type="button" @click="deleteAvatar(editingPerson)" class="px-2 py-1 rounded bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 text-xs">Supprimer la photo</button>
            </div>
          </div>
          <div class="flex justify-end gap-2 mt-4">
            <button type="button" @click="editModal = false" class="px-3 py-1 rounded bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200 text-sm">Annuler</button>
            <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700 text-sm">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
/*
  =====================================================================
  Script principal de gestion des administrateurs, gardiens et résidents
  ---------------------------------------------------------------------
  - Ajout du switch carte/liste, filtres avancés, recherche globale
  - Toutes les fonctions sont commentées pour une meilleure compréhension
  =====================================================================
*/
import { ref, reactive, computed, onMounted } from 'vue'
import type { AdminPerson } from '~/types/adminPerson'
import { useAuthStore } from '~/stores/auth'

// Store d'authentification et base API
const authStore = useAuthStore()
const apiBase = useRuntimeConfig().public.apiBase

// Etats principaux
const loading = ref(true)
const searchQuery = ref('')
const filterRole = ref('')
const isListView = ref(false)

// Liste complète des personnes
let allPersons: AdminPerson[] = []

// Structure des catégories pour affichage par cartes
type PersonCategory = 'admins' | 'gardiens' | 'residents';
const categories: { key: PersonCategory; label: string }[] = [
  { key: 'admins', label: 'Administrateurs' },
  { key: 'gardiens', label: 'Gardiens' },
  { key: 'residents', label: 'Résidents' }
]

// Données filtrées pour affichage par cartes
const filteredPersons = reactive<Record<PersonCategory, AdminPerson[]>>({ admins: [], gardiens: [], residents: [] })

// Données filtrées pour affichage par liste
const filteredList = computed(() => {
  return allPersons.filter(p => {
    let match = true
    // Recherche globale
    const q = searchQuery.value.trim().toLowerCase()
    if (q && !(
      p.nom.toLowerCase().includes(q) ||
      p.prenom.toLowerCase().includes(q) ||
      p.email.toLowerCase().includes(q) ||
      p.numero_telephone.toLowerCase().includes(q)
    )) match = false
    // Filtre rôle
    if (filterRole.value && !p.roles.includes(filterRole.value as 'admin' | 'gardien' | 'resident')) match = false
    return match
  })
})

// Switch entre vue carte/liste
function toggleView() {
  isListView.value = !isListView.value
}

// Récupère l'URL de l'avatar
function getAvatarUrl(photo: string) {
  return `${apiBase}/avatars/${photo}`
}

// Formate la date en français
function formatDate(date: string) {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR')
}
// Formate le numéro selon l'indicatif international
function formatPhoneNumber(num: string) {
  if (!num) return ''
  // Si le numéro commence par +
  if (num.startsWith('+')) {
    const match = num.match(/^\+(\d{1,3})(\d+)$/)
    if (match) {
      const indicatif = '+' + match[1]
      const reste = match[2]
      // Format FR (+33)
      if (indicatif === '+33' && reste.length === 9) {
        return `${indicatif}  ${reste[0]} ${reste.slice(1,3)} ${reste.slice(3,5)} ${reste.slice(5,7)} ${reste.slice(7,9)}`
      }
      // Format Togo (+228)
      if (indicatif === '+228' && reste.length === 8) {
        return `${indicatif}  ${reste.slice(0,2)} ${reste.slice(2,4)} ${reste.slice(4,6)} ${reste.slice(6,8)}`
      }
      // Format générique (groupe de 2)
      return indicatif + '  ' + reste.replace(/(\d{2})/g, '$1 ').trim()
    }
  }
  // Format FR local (0X XX XX XX XX)
  if (num.startsWith('0') && num.length === 10) {
    return `${num.slice(0,2)} ${num.slice(2,4)} ${num.slice(4,6)} ${num.slice(6,8)} ${num.slice(8,10)}`
  }
  return num
}

// Filtre les personnes selon les critères et remplit filteredPersons
function filterPersons() {
  const q = searchQuery.value.trim().toLowerCase()
  const filtered = allPersons.filter(p => {
    let match = true
    // Recherche globale
    if (q && !(
      p.nom.toLowerCase().includes(q) ||
      p.prenom.toLowerCase().includes(q) ||
      p.email.toLowerCase().includes(q) ||
      p.numero_telephone.toLowerCase().includes(q)
    )) match = false
    // Filtre rôle
    if (filterRole.value && !p.roles.includes(filterRole.value as 'admin' | 'gardien' | 'resident')) match = false
    return match
  })
  filteredPersons.admins = filtered.filter(p => p.roles.includes('admin'))
  filteredPersons.gardiens = filtered.filter(p => p.roles.includes('gardien'))
  filteredPersons.residents = filtered.filter(p => p.roles.includes('resident'))
}

// Libellé des rôles
function roleLabel(role: string) {
  if (role === 'admin') return 'Administrateur'
  if (role === 'gardien') return 'Gardien'
  if (role === 'resident') return 'Résident'
  return role
}

// Classes CSS pour les badges de rôle
function roleBadgeClass(role: string) {
  if (role === 'admin') return 'bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs border border-blue-200'
  if (role === 'gardien') return 'bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-xs border border-yellow-200'
  if (role === 'resident') return 'bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs border border-green-200'
  return 'bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs border border-gray-200'
}

// --- MODAL AJOUT UTILISATEUR ---
const addUserStep = ref(0) // 0: rien, 1: choix rôles, 2: formulaire
const addUserForm = reactive({
  nom: '',
  prenom: '',
  email: '',
  numero_telephone: '',
  adresse_logement: '',
  password: '',
  roles: [] as string[]
})
function openAddUserStep1() {
  addUserStep.value = 1
  addUserForm.nom = ''
  addUserForm.prenom = ''
  addUserForm.email = ''
  addUserForm.numero_telephone = ''
  addUserForm.adresse_logement = ''
  addUserForm.password = ''
  addUserForm.roles = []
}
function closeAddUser() {
  addUserStep.value = 0
}
function proceedAddUserStep2() {
  if (addUserForm.roles.length > 0) addUserStep.value = 2
}
async function submitAddUser() {
  try {
    const body: any = {
      nom: addUserForm.nom,
      prenom: addUserForm.prenom,
      email: addUserForm.email,
      numero_telephone: addUserForm.numero_telephone,
      roles: addUserForm.roles,
      password: addUserForm.password
    }
    if (addUserForm.roles.includes('resident')) {
      body.adresse_logement = addUserForm.adresse_logement
    }
    await $fetch(`${apiBase}/admin/persons`, {
      method: 'POST',
      headers: { Authorization: `Bearer ${authStore.token}` },
      body
    })
    closeAddUser()
    await fetchPersons()
  } catch (e) {
    // TODO: gestion erreur
  }
}

// --- MODAL EDITION UTILISATEUR ---
const editModal = ref(false)
const editingPerson = ref<AdminPerson|null>(null)
const editForm = reactive({ nom: '', prenom: '', email: '', numero_telephone: '', adresse_logement: '', new_password: '', roles: [] as string[] })
function editPerson(person: AdminPerson) {
  editingPerson.value = person
  editForm.nom = person.nom
  editForm.prenom = person.prenom
  editForm.email = person.email
  editForm.numero_telephone = person.numero_telephone
  editForm.adresse_logement = person.adresse_logement || ''
  editForm.new_password = ''
  editForm.roles = [...person.roles]
  editModal.value = true
}
async function submitEdit() {
  if (!editingPerson.value) return
  if (!editForm.roles || editForm.roles.length === 0) return // Au moins un rôle
  try {
    const body: any = {
      nom: editForm.nom,
      prenom: editForm.prenom,
      email: editForm.email,
      numero_telephone: editForm.numero_telephone,
      roles: editForm.roles
    }
    if (editForm.roles.includes('resident')) {
      body.adresse_logement = editForm.adresse_logement
    }
    if (editForm.new_password) {
      body.new_password = editForm.new_password
    }
    await $fetch(`${apiBase}/admin/persons/${editingPerson.value.id}`, {
      method: 'PUT',
      headers: { Authorization: `Bearer ${authStore.token}` },
      body
    })
    editModal.value = false
    await fetchPersons()
  } catch (e) {
    // TODO: gestion erreur
  }
}

// Suppression de l'avatar
async function deleteAvatar(person: AdminPerson) {
  if (!person.photo_profil) return
  if (!confirm('Supprimer la photo de profil ?')) return
  try {
    await $fetch(`${apiBase}/avatars/${person.photo_profil}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    await fetchPersons()
    if (editingPerson.value) editingPerson.value.photo_profil = ''
  } catch (e) {
    // TODO: gestion erreur
  }
}

// Suppression d'un utilisateur
async function deletePerson(person: AdminPerson) {
  if (!confirm(`Supprimer ${person.prenom} ${person.nom} ?`)) return
  try {
    await $fetch(`${apiBase}/admin/persons/${person.id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    await fetchPersons()
  } catch (e) {
    // TODO: gestion erreur
  }
}

// Récupération des personnes à l'initialisation
async function fetchPersons() {
  loading.value = true
  try {
    const res = await $fetch<{ success: boolean; persons: AdminPerson[] }>(`${apiBase}/admin/persons`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    if (res.success) {
      allPersons = res.persons
      filterPersons()
    }
  } catch (e) {
    // TODO: gestion erreur
  } finally {
    loading.value = false
  }
}

onMounted(fetchPersons)
</script>

<style scoped>
.input {
  border: 1px solid #e0e7ef;
  border-radius: 0.6rem;
  padding: 0.5rem 0.8rem;
  font-size: 1rem;
  outline: none;
  transition: border 0.2s;
}
.input:focus {
  border-color: #0097b2;
}
</style>
