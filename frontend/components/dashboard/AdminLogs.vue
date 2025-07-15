<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Logs d'activité</h2>
    <p class="mb-4 text-gray-500">Historique des actions importantes et événements du système.</p>

    <!-- Barre de recherche et filtres -->
    <div class="mb-4 flex flex-col md:flex-row md:items-center gap-2 md:gap-4">
      <input
        v-model="search"
        type="text"
        placeholder="Rechercher (action, message, email, IP...)"
        class="w-full md:w-80 px-3 py-2 border border-gray-200 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
      />
      <select v-model="actionFilter" class="px-2 py-2 border border-gray-200 rounded bg-white">
        <option value="">Toutes les actions</option>
        <option v-for="action in uniqueActions" :key="action" :value="action">{{ action }}</option>
      </select>
      <select v-model="userFilter" class="px-2 py-2 border border-gray-200 rounded bg-white">
        <option value="">Tous les utilisateurs</option>
        <option v-for="user in uniqueUsers" :key="user" :value="user">{{ user }}</option>
      </select>
      <div class="flex-1"></div>
      <div class="flex items-center gap-2 self-end md:self-auto">
        <input type="date" v-model="deleteBeforeDate" class="px-2 py-2 border border-gray-200 rounded bg-white text-xs h-[40px] md:h-auto" style="height: 40px; min-width: 0;" />
        <button @click="deleteLogsBefore" :disabled="!deleteBeforeDate || deletingLogs" class="px-2 py-2 rounded bg-red-500 text-white text-xs font-semibold hover:bg-red-600 transition disabled:opacity-50 disabled:cursor-not-allowed h-[40px] md:h-auto" style="height: 40px; min-width: 0;">
          Effacer les logs avant cette date
        </button>
      </div>
    </div>

    <div v-if="loading" class="text-center py-8 text-gray-400">Chargement des logs...</div>
    <div v-else>
      <div v-if="filteredLogs.length === 0" class="text-gray-400 italic">Aucun log à afficher.</div>
      <ul class="divide-y divide-gray-100">
        <li v-for="log in filteredLogs" :key="log.id" class="py-2 flex flex-col md:flex-row md:items-center gap-1 md:gap-4">
          <span class="text-xs text-gray-400 w-32">{{ formatDate(log.created_at) }}</span>
          <span class="flex-1 text-sm">
            <span class="inline-block px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 text-xs mr-2">{{ log.action }}</span>
            {{ log.message }}
          </span>
          <span class="text-xs text-gray-500">{{ log.user_email ?? 'Système' }}</span>
          <span v-if="log.ip_address" class="text-xs text-gray-300 ml-2">{{ log.ip_address }}</span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, onMounted } from 'vue'
  import { useAuthStore } from '~/stores/auth'

  interface LogEntry {
    id: number
    action: string
    message: string
    created_at: string
    user_email?: string
    ip_address?: string
  }

  const loading = ref(true)
  const logs = ref<LogEntry[]>([])
  const search = ref('')
  const actionFilter = ref('')
  const userFilter = ref('')
  const deleteBeforeDate = ref('')
  const deletingLogs = ref(false)
  const authStore = useAuthStore()
  const apiBase = useRuntimeConfig().public.apiBase
  async function deleteLogsBefore() {
    if (!deleteBeforeDate.value) return;
    if (!confirm(`Supprimer tous les logs avant le ${deleteBeforeDate.value} ?`)) return;
    deletingLogs.value = true;
    try {
      await $fetch(`${apiBase}/admin/logs/delete-before`, {
        method: 'POST',
        body: { date: deleteBeforeDate.value },
        headers: { Authorization: `Bearer ${authStore.token}` }
      });
      await fetchLogs();
      deleteBeforeDate.value = '';
    } catch (e) {
      alert('Erreur lors de la suppression des logs.');
    }
    deletingLogs.value = false;
  }

  function formatDate(date: string) {
    return new Date(date).toLocaleString('fr-FR')
  }


  // Actions uniques pour le filtre
  const uniqueActions = computed(() => {
    const actions = logs.value.map(l => l.action)
    return [...new Set(actions)].sort()
  })

  // Utilisateurs uniques pour le filtre
  const uniqueUsers = computed(() => {
    const users = logs.value.map(l => l.user_email ?? 'Système')
    return [...new Set(users)].sort((a, b) => a.localeCompare(b, 'fr'))
  })

  // Filtrage des logs
  const filteredLogs = computed(() => {
    let result = logs.value
    if (actionFilter.value) {
      result = result.filter(l => l.action === actionFilter.value)
    }
    if (userFilter.value) {
      result = result.filter(l => (l.user_email ?? 'Système') === userFilter.value)
    }
    if (search.value.trim()) {
      const s = search.value.trim().toLowerCase()
      result = result.filter(l =>
        l.action.toLowerCase().includes(s) ||
        l.message.toLowerCase().includes(s) ||
        (l.user_email && l.user_email.toLowerCase().includes(s)) ||
        (l.ip_address && l.ip_address.toLowerCase().includes(s))
      )
    }
    return result
  })

  async function fetchLogs() {
    loading.value = true
    try {
      const res = await $fetch<{ success: boolean, data: { data: LogEntry[] } }>(
        `${apiBase}/admin/logs`,
        {
          headers: { Authorization: `Bearer ${authStore.token}` }
        }
      )
      logs.value = res.data.data
    } catch (e) {
      logs.value = []
    }
    loading.value = false
  }

  onMounted(fetchLogs)
</script>


