
<template>
  <div class="p-4 sm:p-6">
    <div class="mb-4 flex flex-wrap items-center gap-2">
      <input v-model="search" type="text" :placeholder="$t('adminConversations.searchPlaceholder')" class="border px-3 py-2 rounded w-full sm:w-64" />
      <select v-model="selectedAuteur" class="border px-3 py-2 rounded w-full sm:w-auto">
        <option value="">{{ $t('adminConversations.allAuthors') }}</option>
        <option v-for="a in auteurs" :key="a.id_auteur" :value="a.id_auteur">{{ a.auteur_nom }}</option>
      </select>
      <select v-model="selectedGroupe" class="border px-3 py-2 rounded w-full sm:w-auto">
        <option value="">{{ $t('adminConversations.allGroups') }}</option>
        <option v-for="g in groupes" :key="g.id_groupe_message" :value="g.id_groupe_message">{{ g.nom_groupe }}</option>
      </select>
      <select v-model="sortOrder" class="border px-3 py-2 rounded w-full sm:w-auto">
        <option value="desc">{{ $t('adminConversations.sortRecent') }}</option>
        <option value="asc">{{ $t('adminConversations.sortOld') }}</option>
      </select>
      <button @click="refresh" :disabled="loading" class="flex items-center gap-2 bg-[#0097b2] text-white px-4 py-2 rounded hover:bg-[#007a8a] transition disabled:opacity-50 w-full sm:w-auto">
        <svg v-if="loading" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="#fff" d="M4 12a8 8 0 018-8v8z"></path></svg>
        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        {{ $t('adminConversations.refresh') }}
      </button>
    </div>
    <div v-if="loading" class="text-gray-400">{{ $t('adminConversations.loading') }}</div>
    <div v-else-if="error" class="text-red-500">{{ error }}</div>
    <div v-else>
      <!-- Section 1 : Liste des groupes/conversations avec suppression -->
      <h3 class="text-lg font-semibold mb-2">{{ $t('adminConversations.groupsTitle') }}</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow overflow-hidden mb-8 text-sm">
          <thead>
            <tr class="bg-gray-100 text-xs text-gray-600">
              <th class="px-3 py-2 text-left">{{ $t('adminConversations.groupName') }}</th>
              <th class="px-3 py-2 text-left">{{ $t('adminConversations.groupId') }}</th>
              <th class="px-3 py-2 text-left">{{ $t('adminConversations.groupMembers') }}</th>
              <th class="px-3 py-2 text-left">{{ $t('adminConversations.groupMessages') }}</th>
              <th class="px-3 py-2 text-left">{{ $t('adminConversations.groupActions') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="g in groupes" :key="g.id_groupe_message" class="border-b hover:bg-red-50">
              <td class="px-3 py-2">{{ g.nom_groupe }}</td>
              <td class="px-3 py-2">{{ g.id_groupe_message }}</td>
              <td class="px-3 py-2">{{ getGroupMembersCount(g.id_groupe_message) }}</td>
              <td class="px-3 py-2">{{ getGroupMessagesCount(g.id_groupe_message) }}</td>
              <td class="px-3 py-2">
                <button @click="deleteGroup(g)" class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded hover:bg-red-200">{{ $t('adminConversations.deleteGroup') }}</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Section 2 : Liste des messages (lecture seule) -->
      <h3 class="text-lg font-semibold mb-2 mt-4">{{ $t('adminConversations.allMessagesTitle') }}</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow overflow-hidden text-sm">
          <thead>
            <tr class="bg-gray-100 text-xs text-gray-600">
              <th class="px-3 py-2 text-left">{{ $t('adminConversations.date') }}</th>
              <th class="px-3 py-2 text-left">{{ $t('adminConversations.author') }}</th>
              <th class="px-3 py-2 text-left">{{ $t('adminConversations.groupName') }}</th>
              <th class="px-3 py-2 text-left">{{ $t('adminConversations.content') }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="msg in filteredMessages" :key="msg.id_message" class="border-b hover:bg-blue-50">
              <td class="px-3 py-2">{{ formatDate(msg.date_envoi) }}</td>
              <td class="px-3 py-2">{{ msg.auteur_nom }}</td>
              <td class="px-3 py-2">{{ msg.nom_groupe }}</td>
              <td class="px-3 py-2">{{ msg.contenu_message }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>


<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

// Utilisation du nouveau composable
const {
  conversations,
  loadingConversations: loading,
  errorConversations: error,
  fetchConversations
} = useAdminData()

const search = ref('');
const selectedAuteur = ref('');
const selectedGroupe = ref('');
const sortOrder = ref<'asc' | 'desc'>('desc');

// Alias pour la compatibilité avec les conversations
const messages = computed(() => {
  const conversationsArray = Array.isArray(conversations.value) ? conversations.value : []
  return conversationsArray as any[]
})

const auteurs = computed(() => {
  const map = new Map();
  messages.value.forEach(m => {
    if (!map.has(m.id_auteur)) {
      map.set(m.id_auteur, { id_auteur: m.id_auteur, auteur_nom: m.auteur_nom });
    }
  });
  return Array.from(map.values());
});
// Regroupe les groupes avec leur nom
const groupes = computed(() => {
  const map = new Map();
  messages.value.forEach(m => {
    if (!map.has(m.id_groupe_message)) {
      map.set(m.id_groupe_message, { id_groupe_message: m.id_groupe_message, nom_groupe: m.nom_groupe });
    }
  });
  return Array.from(map.values());
});

// Calcule le nombre de membres pour un groupe donné (d'après les messages)
function getGroupMembersCount(groupId: number) {
  // On prend tous les auteurs distincts ayant posté dans ce groupe
  const auteurs = new Set(messages.value.filter(m => m.id_groupe_message === groupId).map(m => m.id_auteur));
  return auteurs.size;
}

// Calcule le nombre de messages (y compris ceux avec pièces jointes) pour un groupe donné
function getGroupMessagesCount(groupId: number) {
  // On compte tous les messages du groupe
  return messages.value.filter(m => m.id_groupe_message === groupId).length;
}

const filteredMessages = computed(() => {
  let arr = messages.value
    .filter(m => {
      const auteurId = selectedAuteur.value ? Number(selectedAuteur.value) : null;
      const groupeId = selectedGroupe.value ? Number(selectedGroupe.value) : null;
      return (
        (!search.value || m.contenu_message.toLowerCase().includes(search.value.toLowerCase())) &&
        (!auteurId || m.id_auteur === auteurId) &&
        (!groupeId || m.id_groupe_message === groupeId)
      );
    });
  arr = arr.sort((a, b) => sortOrder.value === 'asc'
    ? new Date(a.date_envoi).getTime() - new Date(b.date_envoi).getTime()
    : new Date(b.date_envoi).getTime() - new Date(a.date_envoi).getTime()
  );
  return arr;
});

function refresh() {
  fetchConversations();
}


// Suppression d'un groupe/conversation (et tous ses messages)
async function deleteGroup(groupe: any) {
  if (confirm(t('adminConversations.deleteConfirm', { group: groupe.nom_groupe, id: groupe.id_groupe_message }))) {
    // TODO: Implémenter deleteAdminConversation
    console.log('Suppression de la conversation:', groupe.id_groupe_message);
    await fetchConversations(); // Recharger après suppression
  }
}

function formatDate(date: string) {
  if (!date) return t('adminConversations.noDate');
  // Utilise la locale courante
  return new Date(date).toLocaleString();
}

onMounted(() => {
  fetchConversations();
});
</script>


