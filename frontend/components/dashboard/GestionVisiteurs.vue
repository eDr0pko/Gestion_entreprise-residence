<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-6 gap-4 flex-wrap">
      <h2 class="text-2xl font-bold">Gestion des visiteurs</h2>
      <div class="flex items-center gap-2 flex-wrap">
        <input v-model="searchQuery" @input="filterPersons" type="text" placeholder="Rechercher un visiteur..." class="input w-64" />
        <button @click="displayMode = displayMode === 'cards' ? 'list' : 'cards'" class="ml-2 px-3 py-1 rounded border text-sm font-semibold transition-all duration-150"
          :class="displayMode === 'cards' ? 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100'">
          <span v-if="displayMode === 'cards'">Affichage liste</span>
          <span v-else>Affichage cartes</span>
        </button>
      </div>
    </div>
    <div v-if="loading" class="text-center py-8 text-gray-500">Chargement...</div>
    <div v-else>
      <div>
        <h3 class="text-xl font-semibold mb-3 capitalize">Visiteurs</h3>
        <div v-if="persons && persons.length === 0" class="text-gray-400 italic mb-4">Aucun visiteur</div>
        <div v-if="displayMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="person in (persons ?? [])" :key="person.id" class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex flex-col gap-2 relative group hover:shadow-md transition">
            <!-- ...existing card content... -->
            <div class="flex items-center gap-3">
              <img v-if="person.photo_profil" :src="getAvatarUrl(person.photo_profil)" class="w-12 h-12 rounded-full object-cover border" alt="avatar" />
              <div v-else class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-lg font-bold text-gray-500">{{ person.prenom[0] }}{{ person.nom[0] }}</div>
              <div>
                <div class="font-bold text-lg flex items-center gap-2">
                  {{ person.prenom }} {{ person.nom }}
                  <span v-if="!person.actif" class="ml-2 px-2 py-0.5 rounded bg-red-600 text-white text-xs font-semibold">Banni</span>
                </div>
                <div class="text-xs text-gray-500">{{ person.email }}</div>
                <div class="text-xs text-gray-400 mt-1 flex flex-wrap gap-1">
                  <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded text-xs border border-purple-200">Visiteur</span>
                </div>
                <div class="text-xs mt-1">
                  <span class="font-semibold">Actif :</span> <span :class="person.actif ? 'text-green-600' : 'text-red-600'">{{ person.actif ? 'Oui' : 'Non' }}</span>
                </div>
                <div v-if="person.date_inscription" class="text-xs text-blue-500">Inscrit le : {{ formatDate(person.date_inscription) }}</div>
                <div v-if="person.date_expiration" class="text-xs text-red-500">Expire le : {{ formatDate(person.date_expiration) }}</div>
                <div v-if="person.invite_par && getInviterName(person.invite_par)" class="text-xs text-gray-500">
                  Invité par : {{ getInviterName(person.invite_par) }}
                </div>
                <div v-if="person.commentaire" class="text-xs text-gray-500 mt-1">Commentaire : {{ person.commentaire }}</div>
              </div>
            </div>
            <div class="flex flex-col gap-1 mt-2">
              <div class="text-sm"><span class="font-medium">Téléphone :</span> {{ person.numero_telephone }}</div>
            </div>
            <div class="flex gap-2 mt-3">
              <template v-if="person.actif">
                <button @click="banPerson(person)" :disabled="!person.actif" class="px-3 py-1 rounded bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 text-sm">Bannir</button>
                <button @click="editPerson(person)" class="px-3 py-1 rounded bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 text-sm">Modifier</button>
              </template>
              <template v-else>
                <span class="px-3 py-1 rounded bg-gray-100 text-gray-400 border border-gray-200 text-sm">Banni</span>
              </template>
            </div>
          </div>
        </div>
        <table v-else class="min-w-full divide-y divide-gray-200 bg-white rounded-xl border border-gray-200 shadow-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Nom</th>
              <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Email</th>
              <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Téléphone</th>
              <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Actif</th>
              <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Invité par</th>
              <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Inscription</th>
              <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Expiration</th>
              <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="person in (persons ?? [])" :key="person.id" class="hover:bg-gray-50">
              <td class="px-4 py-2 font-medium">{{ person.prenom }} {{ person.nom }}</td>
              <td class="px-4 py-2">{{ person.email }}</td>
              <td class="px-4 py-2">{{ person.numero_telephone }}</td>
              <td class="px-4 py-2">
                <span :class="person.actif ? 'text-green-600' : 'text-red-600'">{{ person.actif ? 'Oui' : 'Non' }}</span>
              </td>
              <td class="px-4 py-2">{{ getInviterName(person.invite_par) || '-' }}</td>
              <td class="px-4 py-2">{{ person.date_inscription ? formatDate(person.date_inscription) : '-' }}</td>
              <td class="px-4 py-2">{{ person.date_expiration ? formatDate(person.date_expiration) : '-' }}</td>
              <td class="px-4 py-2">
                <template v-if="person.actif">
                  <button @click="banPerson(person)" :disabled="!person.actif" class="px-2 py-1 rounded bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 text-xs">Bannir</button>
                  <button @click="editPerson(person)" class="px-2 py-1 rounded bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 text-xs ml-1">Modifier</button>
                </template>
                <template v-else>
                  <span class="px-2 py-1 rounded bg-gray-100 text-gray-400 border border-gray-200 text-xs">Banni</span>
                </template>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-10">
        <h3 class="text-xl font-semibold mb-3 capitalize text-red-700">Visiteurs bannis</h3>
        <div v-if="bannedPersons.length === 0" class="text-gray-400 italic mb-4">Aucun visiteur banni</div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div v-for="person in bannedPersons" :key="person.id" class="bg-gray-100 rounded-xl border border-gray-300 shadow-sm p-4 flex flex-col gap-2 relative group">
            <div class="flex items-center gap-3">
              <img v-if="person.photo_profil" :src="getAvatarUrl(person.photo_profil)" class="w-12 h-12 rounded-full object-cover border" alt="avatar" />
              <div v-else class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center text-lg font-bold text-gray-500">{{ person.prenom[0] }}{{ person.nom[0] }}</div>
              <div>
                <div class="font-bold text-lg">{{ person.prenom }} {{ person.nom }}</div>
                <div class="text-xs text-gray-500">{{ person.email }}</div>
                <div class="text-xs mt-1">
                  <span class="font-semibold">Actif :</span> <span class="text-red-600">Non</span>
                </div>
                <div v-if="person.date_inscription" class="text-xs text-blue-500">Inscrit le : {{ formatDate(person.date_inscription) }}</div>
                <div v-if="person.date_expiration" class="text-xs text-red-500">Expire le : {{ formatDate(person.date_expiration) }}</div>
                <div v-if="person.invite_par" class="text-xs text-gray-500">Invité par : {{ person.invite_par }}</div>
                <div v-if="person.commentaire" class="text-xs text-gray-500 mt-1">Commentaire : {{ person.commentaire }}</div>
              </div>
            </div>
            <div class="flex flex-col gap-1 mt-2">
              <div class="text-sm"><span class="font-medium">Téléphone :</span> {{ person.numero_telephone }}</div>
            </div>
            <div class="flex gap-2 mt-3">
              <span class="px-3 py-1 rounded bg-gray-200 text-gray-400 border border-gray-300 text-sm">Banni</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Place le modal ici, AVANT la fermeture du template -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 w-full max-w-lg shadow-lg relative">
        <h3 class="text-xl font-bold mb-4">Modifier le visiteur</h3>
        <form @submit.prevent="saveEditPerson">
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
            <label class="block text-sm font-medium mb-1">Commentaire</label>
            <textarea v-model="editForm.commentaire" class="input w-full" rows="2"></textarea>
          </div>
          <div class="flex gap-2 justify-end mt-4">
            <button type="button" @click="closeEditModal" class="px-4 py-1 rounded bg-gray-100 text-gray-600 border border-gray-300 hover:bg-gray-200">Annuler</button>
            <button type="submit" class="px-4 py-1 rounded bg-blue-600 text-white hover:bg-blue-700">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Fonction utilitaire pour afficher le nom complet de l'invitant à partir de son id (exposée au template)
function getInviterName(inviteParId: string | number | null | undefined) {
  if (!inviteParId) return null
  // On tente d'abord de trouver par id exact (number ou string)
  let inviter = allPersons.value.find(p => String(p.id) === String(inviteParId))
  if (!inviter) {
    // Certains backends renvoient l'id sous forme de string ou number, on tente les deux
    inviter = allPersons.value.find(p => p.id == inviteParId)
  }
  if (inviter) return `${inviter.prenom} ${inviter.nom}`
  return null
}
  import { ref, onMounted } from 'vue'
  const displayMode = ref<'cards' | 'list'>('cards')
  import type { InvitePerson } from '~/types/invitePerson'
  import { useAuthStore } from '~/stores/auth'

  const authStore = useAuthStore()
  const apiBase = useRuntimeConfig().public.apiBase

  const loading = ref(true)
  const searchQuery = ref('')
  const allPersons = ref<InvitePerson[]>([])
  const persons = ref<InvitePerson[]>([])
  const bannedPersons = ref<InvitePerson[]>([])

  function getAvatarUrl(photo: string) {
    return `${apiBase}/avatars/${photo}`
  }

  function formatDate(date: string) {
    if (!date) return ''
    return new Date(date).toLocaleDateString('fr-FR')
  }

  async function fetchPersons() {
    loading.value = true
    try {
      const res = await $fetch<{ success: boolean; data: any[] }>(`${apiBase}/admin/guests`, {
        headers: { Authorization: `Bearer ${authStore.token}` }
      })
      if (res.success && Array.isArray(res.data)) {
        const mapped = res.data.map((p: any) => ({
          id: p.id_personne,
          email: p.email,
          nom: p.nom,
          prenom: p.prenom,
          numero_telephone: p.numero_telephone,
          photo_profil: p.photo_profil,
          actif: p.actif ?? false,
          date_inscription: p.date_inscription ?? '',
          date_expiration: p.date_expiration ?? '',
          invite_par: p.invite_par ?? '',
          commentaire: p.commentaire ?? '',
          created_at: p.created_at ?? '',
          updated_at: p.updated_at ?? ''
        }))
        allPersons.value = mapped
        filterPersons()
        bannedPersons.value = mapped.filter(p => !p.actif)
      } else {
        allPersons.value = []
        persons.value = []
        bannedPersons.value = []
      }
    } catch (e) {
      allPersons.value = []
      persons.value = []
      bannedPersons.value = []
      // TODO: gestion erreur
    } finally {
      loading.value = false
    }
  }

  function filterPersons() {
    const q = searchQuery.value.trim().toLowerCase()
    persons.value = !q
      ? allPersons.value
      : allPersons.value.filter(p =>
          p.nom.toLowerCase().includes(q) ||
          p.prenom.toLowerCase().includes(q) ||
          p.email.toLowerCase().includes(q) ||
          p.numero_telephone.toLowerCase().includes(q)
        )
  }

  async function banPerson(person: InvitePerson) {
    if (!confirm(`Bannir ${person.prenom} ${person.nom} ?`)) return
    try {
      await $fetch(`${apiBase}/ban`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${authStore.token}` },
        body: { id_personne: person.id }
      })
      await fetchPersons()
    } catch (e) {
      // TODO: gestion erreur
    }
  }

  // --- MODAL MODIFICATION ---
  const showEditModal = ref(false)
  const editForm = ref<Partial<InvitePerson & {id?: number}>>({})
  function editPerson(person: InvitePerson) {
    editForm.value = { ...person }
    showEditModal.value = true
  }

  async function saveEditPerson() {
    if (!editForm.value.id) return
    try {
      await $fetch(`${apiBase}/admin/guests/${editForm.value.id}`, {
        method: 'PUT',
        headers: {
          'Authorization': `Bearer ${authStore.token}`,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          nom: editForm.value.nom,
          prenom: editForm.value.prenom,
          email: editForm.value.email,
          numero_telephone: editForm.value.numero_telephone,
          commentaire: editForm.value.commentaire,
          // Ajoutez ici d'autres champs si besoin (date_expiration, actif...)
        })
      })
      showEditModal.value = false
      await fetchPersons()
    } catch (e: any) {
      if (e && e.data) {
        console.error('Erreur de validation backend:', e.data)
        alert('Erreur: ' + JSON.stringify(e.data))
      } else {
        console.error('Erreur inconnue lors de la mise à jour du visiteur', e)
        alert('Erreur inconnue lors de la mise à jour du visiteur')
      }
    }
  }
  function closeEditModal() {
    showEditModal.value = false
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
