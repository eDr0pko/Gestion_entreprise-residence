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
  <div class="space-y-8 animate-fadeInUp">
    <!-- Header moderne -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 card-shadow">
      <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
        <div class="animate-slideInLeft">
          <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $t('adminResidents.title') }}</h2>
          <p class="text-gray-500">Gérez les utilisateurs de votre résidence en toute simplicité</p>
        </div>
        <div class="flex items-center gap-3">
          <button @click="toggleView" class="px-4 py-2.5 rounded-xl bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100 hover:border-gray-300 transition-all duration-200 font-medium hover:card-shadow">
            {{ isListView ? $t('adminResidents.cardView') : $t('adminResidents.listView') }}
          </button>
          <button @click="openAddUserStep1" class="px-6 py-2.5 rounded-xl gradient-modern text-white hover:opacity-90 transition-all duration-200 font-medium shadow-lg hover:card-shadow-hover">
            Ajouter un utilisateur
          </button>
        </div>
      </div>
      
      <!-- Filtres modernes -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">{{ $t('adminResidents.globalSearch') }}</label>
          <div class="relative">
            <input v-model="searchQuery" @input="filterPersons" type="text" :placeholder="$t('adminResidents.searchPlaceholder')" 
              class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white" />
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
        </div>
        <div class="space-y-2">
          <label class="block text-sm font-medium text-gray-700">{{ $t('adminResidents.filterRole') }}</label>
          <select v-model="filterRole" @change="filterPersons" 
            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all duration-200 bg-gray-50 focus:bg-white">
            <option value="">{{ $t('adminResidents.allRoles') }}</option>
            <option value="admin">{{ $t('profile.role.admin') }}</option>
            <option value="gardien">{{ $t('profile.role.gardien') }}</option>
            <option value="resident">{{ $t('profile.role.resident') }}</option>
          </select>
        </div>
      </div>
    </div>
  <!-- Modal ajout utilisateur - étape 1 : choix des rôles -->
  <div v-if="addUserStep === 1" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md relative">
      <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-600" @click="closeAddUser">✕</button>
      <h3 class="text-lg font-bold mb-4">{{ $t('adminResidents.addUserTitle') }}</h3>
      <div class="mb-3">
        <label class="block text-sm font-medium mb-1">{{ $t('adminResidents.roles') }} <span class="text-red-500">*</span></label>
        <div class="flex gap-4">
          <label class="flex items-center gap-1">
            <input type="checkbox" value="admin" v-model="addUserForm.roles" />
            <span>{{ $t('profile.role.admin') }}</span>
          </label>
          <label class="flex items-center gap-1">
            <input type="checkbox" value="gardien" v-model="addUserForm.roles" />
            <span>{{ $t('profile.role.gardien') }}</span>
          </label>
          <label class="flex items-center gap-1">
            <input type="checkbox" value="resident" v-model="addUserForm.roles" />
            <span>{{ $t('profile.role.resident') }}</span>
          </label>
        </div>
        <div v-if="addUserForm.roles.length === 0" class="text-xs text-red-500 mt-1">{{ $t('adminResidents.roleRequired') }}</div>
      </div>
      <div class="flex justify-end gap-2 mt-4">
        <button type="button" @click="closeAddUser" class="px-3 py-1 rounded bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200 text-sm">{{ $t('components.button.cancel') }}</button>
        <button type="button" @click="proceedAddUserStep2" :disabled="addUserForm.roles.length === 0" class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700 text-sm">{{ $t('adminResidents.next') }}</button>
      </div>
    </div>
  </div>

  <!-- Modal ajout utilisateur - étape 2 : formulaire -->
  <div v-if="addUserStep === 2" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md relative">
      <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-600" @click="closeAddUser">✕</button>
      <h3 class="text-lg font-bold mb-4">{{ $t('adminResidents.userInfoTitle') }}</h3>
      <form @submit.prevent="submitAddUser">
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">{{ $t('profile.fields.lastName') }}</label>
          <input v-model="addUserForm.nom" class="input w-full" required />
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">{{ $t('profile.fields.firstName') }}</label>
          <input v-model="addUserForm.prenom" class="input w-full" required />
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">{{ $t('login.email') }}</label>
          <input v-model="addUserForm.email" class="input w-full" required type="email" />
        </div>
        <div class="mb-3">
          <PhoneInput 
            v-model="addUserForm.numero_telephone"
            :label="$t('profile.fields.phone')"
            :placeholder="$t('profile.fields.phonePlaceholder')"
          />
        </div>
        <div class="mb-3" v-if="addUserForm.roles.includes('resident')">
          <label class="block text-sm font-medium mb-1">{{ $t('adminResidents.address') }}</label>
          <input v-model="addUserForm.adresse_logement" class="input w-full" />
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">{{ $t('profile.fields.newPassword') }}</label>
          <input v-model="addUserForm.password" class="input w-full" type="password" required />
        </div>
        <div class="flex justify-end gap-2 mt-4">
        <button type="button" @click="closeAddUser" class="px-3 py-1 rounded bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200 text-sm">{{ $t('components.button.cancel') }}</button>
        <button type="submit" class="px-3 py-1 rounded bg-green-600 text-white hover:bg-green-700 text-sm">{{ $t('components.button.add') }}</button>
        </div>
      </form>
    </div>
  </div>
    <div v-if="adminPersonsLoading" class="text-center py-8 text-gray-500">{{ $t('components.table.loading') }}</div>
    <div v-else>
      <!-- Affichage par cartes modernes -->
      <div v-if="!isListView">
        <div v-for="cat in categories" :key="cat.key" class="mb-12">
          <div class="flex items-center gap-3 mb-6">
            <h3 class="text-2xl font-bold text-gray-900">{{ $t('adminResidents.categories.' + cat.key) }}</h3>
            <div class="h-px flex-1 bg-gradient-to-r from-gray-200 to-transparent"></div>
          </div>
          <div v-if="filteredPersons[cat.key].length === 0" class="text-center py-12 bg-white rounded-2xl border border-gray-100">
            <div class="text-gray-400 text-lg font-medium">{{ $t('adminResidents.noPerson', { type: $t('adminResidents.categories.' + cat.key).toLowerCase() }) }}</div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="person in filteredPersons[cat.key]" :key="person.id_personne" 
              class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-gray-200 transition-all duration-300 p-6 relative overflow-hidden card-shadow hover:card-shadow-hover animate-fadeInUp">
              <!-- Indicateur de rôle -->
              <div class="absolute top-0 right-0 w-20 h-20 opacity-5">
                <div class="w-full h-full gradient-modern rotate-45 transform translate-x-8 -translate-y-8"></div>
              </div>
              
              <!-- Header de la carte -->
              <div class="flex items-start gap-4 mb-4">
                <div class="relative">
                  <img v-if="person.photo_profil" :src="getAvatarUrl(person.photo_profil)" 
                    class="w-16 h-16 rounded-2xl object-cover border-2 border-gray-100 group-hover:border-blue-200 transition-colors duration-300" alt="avatar" />
                  <div v-else class="w-16 h-16 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-xl font-bold text-gray-600 border-2 border-gray-100 group-hover:border-blue-200 transition-colors duration-300">
                    {{ person.prenom[0] }}{{ person.nom[0] }}
                  </div>
                  <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-2 border-white"></div>
                </div>
                <div class="flex-1 min-w-0">
                  <h4 class="font-bold text-xl text-gray-900 mb-1 truncate">{{ person.prenom }} {{ person.nom }}</h4>
                  <p class="text-sm text-gray-500 mb-2 truncate">{{ person.email }}</p>
                  <div class="flex flex-wrap gap-1.5">
                    <span v-for="role in person.roles" :key="role" :class="roleBadgeClass(role)" 
                      class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium">
                      {{ roleLabel(role) }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Informations supplémentaires -->
              <div class="space-y-3 mb-6">
                <div v-if="person.niveau_acces" class="flex items-center gap-2 text-sm">
                  <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3a1 1 0 011-1h2.586l6.414-6.414A6 6 0 0119 9z"/>
                  </svg>
                  <span class="text-gray-600">Niveau accès : <span class="font-semibold text-blue-600">{{ person.niveau_acces }}</span></span>
                </div>
                
                <div v-if="person.adresse_logement" class="flex items-center gap-2 text-sm">
                  <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                  </svg>
                  <span class="text-gray-600">Logement : <span class="font-semibold text-green-600">{{ person.adresse_logement }}</span></span>
                </div>
                
                <div class="flex items-center gap-2 text-sm">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                  </svg>
                  <span class="text-gray-600">{{ formatPhoneNumber(person.numero_telephone || '') }}</span>
                </div>
                
                <div v-if="person.date_nomination" class="flex items-center gap-2 text-sm">
                  <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  <span class="text-gray-500">Nommé le {{ formatDate(person.date_nomination) }}</span>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex gap-3">
                <button @click="editPerson(person)" 
                  class="flex-1 px-4 py-2.5 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 hover:border-blue-300 transition-all duration-200 font-medium text-sm">
                  {{ $t('components.button.edit') }}
                </button>
                <button @click="handleDeletePerson(person)" 
                  class="flex-1 px-4 py-2.5 rounded-xl bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 hover:border-red-300 transition-all duration-200 font-medium text-sm">
                  {{ $t('components.button.delete') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Affichage par liste moderne -->
      <div v-else class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-x-auto">
        <table class="min-w-full w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">{{ $t('profile.fields.lastName') }}</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">{{ $t('profile.fields.firstName') }}</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">{{ $t('login.email') }}</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">{{ $t('profile.fields.phone') }}</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">{{ $t('adminResidents.roles') }}</th>
              <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-100">
            <tr v-for="person in filteredList" :key="person.id_personne" class="hover:bg-gray-50 transition-colors duration-200">
              <td class="px-4 py-3 font-medium text-gray-900 break-words truncate min-w-[120px]">{{ person.nom }}</td>
              <td class="px-4 py-3 text-gray-700 break-words truncate min-w-[120px]">{{ person.prenom }}</td>
              <td class="px-4 py-3 text-gray-700 break-words truncate min-w-[180px]">{{ person.email }}</td>
              <td class="px-4 py-3 text-gray-700 break-words truncate min-w-[140px]">{{ formatPhoneNumber(person.numero_telephone || '') }}</td>
              <td class="px-4 py-3 min-w-[120px]">
                <div class="flex flex-wrap gap-1">
                  <span v-if="person.role" :class="roleBadgeClass(person.role)" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium">{{ roleLabel(person.role) }}</span>
                  <span v-else-if="person.roles && person.roles.length" v-for="role in person.roles" :key="role" :class="roleBadgeClass(role)" class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium">{{ roleLabel(role) }}</span>
                  <span v-else class="text-gray-500">{{ $t('adminResidents.noRole') }}</span>
                </div>
              </td>
              <td class="px-4 py-3 min-w-[120px]">
                <div class="flex gap-2 flex-wrap">
                  <button @click="editPerson(person)" class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 hover:border-blue-300 text-xs font-medium transition-all duration-200">{{ $t('components.button.edit') }}</button>
                  <button @click="handleDeletePerson(person)" class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 hover:border-red-300 text-xs font-medium transition-all duration-200">{{ $t('components.button.delete') }}</button>
                </div>
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
        <h3 class="text-lg font-bold mb-4">{{ $t('adminResidents.editUserTitle', { name: editingPerson?.prenom + ' ' + editingPerson?.nom }) }}</h3>
        <form @submit.prevent="submitEdit">
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">{{ $t('profile.fields.lastName') }}</label>
            <input v-model="editForm.nom" class="input w-full" required />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">{{ $t('profile.fields.firstName') }}</label>
            <input v-model="editForm.prenom" class="input w-full" required />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">{{ $t('login.email') }}</label>
            <input v-model="editForm.email" class="input w-full" required type="email" />
          </div>
          <div class="mb-3">
            <PhoneInput 
              v-model="editForm.numero_telephone"
              :label="$t('profile.fields.phone')"
              :placeholder="$t('profile.fields.phonePlaceholder')"
            />
          </div>
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">{{ $t('adminResidents.roles') }} <span class="text-red-500">*</span></label>
            <div class="flex gap-4">
              <label class="flex items-center gap-1">
                <input type="checkbox" value="admin" v-model="editForm.roles" :disabled="editForm.roles.length === 1 && editForm.roles.includes('admin')" />
                <span>{{ $t('profile.role.admin') }}</span>
              </label>
              <label class="flex items-center gap-1">
                <input type="checkbox" value="gardien" v-model="editForm.roles" :disabled="editForm.roles.length === 1 && editForm.roles.includes('gardien')" />
                <span>{{ $t('profile.role.gardien') }}</span>
              </label>
              <label class="flex items-center gap-1">
                <input type="checkbox" value="resident" v-model="editForm.roles" :disabled="editForm.roles.length === 1 && editForm.roles.includes('resident')" />
                <span>{{ $t('profile.role.resident') }}</span>
              </label>
            </div>
            <div v-if="editForm.roles.length === 0" class="text-xs text-red-500 mt-1">{{ $t('adminResidents.roleRequired') }}</div>
          </div>
          <div class="mb-3" v-if="editForm.roles.includes('resident')">
          <label class="block text-sm font-medium mb-1">{{ $t('adminResidents.address') }}</label>
            <input v-model="editForm.adresse_logement" class="input w-full" />
          </div>
          <div class="mb-3">
          <label class="block text-sm font-medium mb-1">{{ $t('profile.fields.newPassword') }}</label>
            <input v-model="editForm.new_password" class="input w-full" type="password" placeholder="Laisser vide pour ne pas changer" />
          </div>
          <div class="mb-3" v-if="editingPerson?.photo_profil">
            <label class="block text-sm font-medium mb-1">{{ $t('profile.avatar') }}</label>
            <div class="flex items-center gap-2">
              <img :src="getAvatarUrl(editingPerson.photo_profil)" class="w-10 h-10 rounded-full object-cover border" alt="avatar" />
              <button type="button" @click="handleDeleteAvatar(editingPerson)" class="px-2 py-1 rounded bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 text-xs">{{ $t('components.button.delete') }}</button>
            </div>
          </div>
          <div class="flex justify-end gap-2 mt-4">
            <button type="button" @click="editModal = false" class="px-3 py-1 rounded bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200 text-sm">{{ $t('components.button.cancel') }}</button>
            <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-700 text-sm">{{ $t('components.button.save') }}</button>
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
  import { ref, reactive, computed, onMounted, watch } from 'vue'
  import type { PersonData } from '~/composables/useAdminData'
  
  // Utilisation du composable unifié
  const { residents, loadingResidents, errorResidents, fetchResidents, addPerson, updatePersonData, deletePersonData, deleteAvatar } = useAdminData()
  
  // Alias pour compatibilité
  const allPersons = residents
  const adminPersonsLoading = loadingResidents
  const adminPersonsError = errorResidents
  const fetchAdminPersons = fetchResidents

  // Etats principaux
  const searchQuery = ref('')
  const filterRole = ref('')
  const isListView = ref(false)

  // Structure des catégories pour affichage par cartes
  type PersonCategory = 'admins' | 'gardiens' | 'residents';
  const categories: { key: PersonCategory; label: string }[] = [
    { key: 'admins', label: 'Administrateurs' },
    { key: 'gardiens', label: 'Gardiens' },
    { key: 'residents', label: 'Résidents' }
  ]

  // Données filtrées pour affichage par cartes
  const filteredPersons = reactive<Record<PersonCategory, PersonData[]>>({ admins: [], gardiens: [], residents: [] })

  // Données filtrées pour affichage par liste
  const filteredList = computed(() => {
    return allPersons.value.filter((p: PersonData) => {
      let match = true
      // Recherche globale
      const q = searchQuery.value.trim().toLowerCase()
      if (q && !(
        p.nom.toLowerCase().includes(q) ||
        p.prenom.toLowerCase().includes(q) ||
        p.email.toLowerCase().includes(q) ||
        (p.numero_telephone && p.numero_telephone.toLowerCase().includes(q))
      )) match = false
      // Filtre rôle - vérifier à la fois p.role et p.roles
      if (filterRole.value) {
        const hasRole = (p.role === filterRole.value) || 
                       (p.roles && p.roles.includes(filterRole.value as any))
        if (!hasRole) match = false
      }
      return match
    })
  })

  // Switch entre vue carte/liste
  function toggleView() {
    isListView.value = !isListView.value
  }

  // Récupère l'URL de l'avatar
  function getAvatarUrl(photo: string) {
    const config = useRuntimeConfig();
    const base = config.public.apiBase.replace(/\/api$/, '');
    if (!photo) return '';
    if (photo.startsWith('http')) return photo;
    if (photo.startsWith('avatars/')) return `${base}/storage/${photo}`;
    if (photo.startsWith('public/avatars/')) return `${base}/storage/${photo.replace('public/', '')}`;
    return `${base}/avatars/${photo.split('/').pop()}`;
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
    const filtered = allPersons.value.filter((p: PersonData) => {
      let match = true
      // Recherche globale
      if (q && !(
        p.nom.toLowerCase().includes(q) ||
        p.prenom.toLowerCase().includes(q) ||
        p.email.toLowerCase().includes(q) ||
        (p.numero_telephone && p.numero_telephone.toLowerCase().includes(q))
      )) match = false
      // Filtre rôle
      if (filterRole.value) {
        const hasRole = (p.role === filterRole.value) || 
                       (p.roles && p.roles.includes(filterRole.value as any))
        if (!hasRole) match = false
      }
      return match
    })
    filteredPersons.admins = filtered.filter((p: PersonData) => (p.role === 'admin') || (p.roles && p.roles.includes('admin')))
    filteredPersons.gardiens = filtered.filter((p: PersonData) => (p.role === 'gardien') || (p.roles && p.roles.includes('gardien')))
    filteredPersons.residents = filtered.filter((p: PersonData) => (p.role === 'resident') || (p.roles && p.roles.includes('resident')))
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
      await addPerson(body);
      closeAddUser();
    } catch (e) {
      alert('Erreur lors de l\'ajout de l\'utilisateur.');
    }
  }

  // --- MODAL EDITION UTILISATEUR ---
  const editModal = ref(false)
  const editingPerson = ref<PersonData|null>(null)
  const editForm = reactive({ nom: '', prenom: '', email: '', numero_telephone: '', adresse_logement: '', new_password: '', roles: [] as string[] })
  function editPerson(person: PersonData) {
    editingPerson.value = person
    editForm.nom = person.nom
    editForm.prenom = person.prenom
    editForm.email = person.email
    editForm.numero_telephone = person.numero_telephone || ''
    editForm.adresse_logement = person.adresse_logement || ''
    editForm.new_password = ''
    editForm.roles = person.roles ? [...person.roles] : []
    editModal.value = true
  }
  async function submitEdit() {
    if (!editingPerson.value) return
    if (!editingPerson.value.id_personne) {
      console.error('ID personne manquant:', editingPerson.value)
      alert('Erreur: ID personne manquant')
      return
    }
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
      console.log('Submitting edit for person ID:', editingPerson.value.id_personne, 'with data:', body)
      await updatePersonData(editingPerson.value.id_personne, body);
      editModal.value = false;
    } catch (e) {
      console.error('Erreur détaillée:', e)
      alert('Erreur lors de la modification.');
    }
  }

  // Suppression de l'avatar  
  async function handleDeleteAvatar(person: PersonData) {
    if (!person.photo_profil) return
    if (!confirm('Supprimer la photo de profil ?')) return
    try {
      await deleteAvatar(person.id_personne);
      if (editingPerson.value) editingPerson.value.photo_profil = ''
    } catch (e) {
      alert('Erreur lors de la suppression de l\'avatar.');
    }
  }

  // Suppression d'un utilisateur
  async function handleDeletePerson(person: PersonData) {
    if (!confirm(`Supprimer ${person.prenom} ${person.nom} ?`)) return
    try {
      await deletePersonData(person.id_personne);
    } catch (e) {
      alert('Erreur lors de la suppression.');
    }
  }

  // Récupération des personnes à l'initialisation
  onMounted(async () => {
    await fetchResidents()
    filterPersons()
  })
  
  // Observer les changements et filtrer
  watch([searchQuery, filterRole, allPersons], filterPersons)
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


