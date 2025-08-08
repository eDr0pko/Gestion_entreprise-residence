<template>
  <div class="p-4 sm:p-6">
    <h2 class="text-2xl font-bold mb-4">{{ $t('adminLogs.title') }}</h2>
    <p class="mb-4 text-gray-500">{{ $t('adminLogs.subtitle') }}</p>

    <!-- Barre de recherche et filtres -->
    <div class="mb-4 flex flex-col md:flex-row md:items-center gap-2 md:gap-4">
      <input
        v-model="search"
        type="text"
        :placeholder="$t('adminLogs.searchPlaceholder')"
        class="w-full md:w-80 px-3 py-2 border border-gray-200 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-200"
      />
      <select v-model="actionFilter" class="px-2 py-2 border border-gray-200 rounded bg-white w-full md:w-auto">
        <option value="">{{ $t('adminLogs.allActions') }}</option>
        <option v-for="action in uniqueActions" :key="action" :value="action">{{ action }}</option>
      </select>
      <select v-model="userFilter" class="px-2 py-2 border border-gray-200 rounded bg-white w-full md:w-auto">
        <option value="">{{ $t('adminLogs.allUsers') }}</option>
        <option v-for="user in uniqueUsers" :key="user" :value="user">{{ user }}</option>
      </select>
      <div class="flex-1"></div>
      <div class="flex items-center gap-2 self-end md:self-auto w-full md:w-auto">
        <input type="date" v-model="deleteBeforeDate" class="px-2 py-2 border border-gray-200 rounded bg-white text-xs h-[40px] md:h-auto w-full md:w-auto" style="height: 40px; min-width: 0;" />
        <button @click="handleDeleteLogsBefore" :disabled="!deleteBeforeDate || deletingLogs" class="px-2 py-2 rounded bg-red-500 text-white text-xs font-semibold hover:bg-red-600 transition disabled:opacity-50 disabled:cursor-not-allowed h-[40px] md:h-auto w-full md:w-auto" style="height: 40px; min-width: 0;">
          {{ $t('adminLogs.deleteBefore') }}
        </button>
      </div>
    </div>

    <div v-if="loading" class="text-center py-8 text-gray-400">{{ $t('adminLogs.loading') }}</div>
    <div v-else-if="error" class="text-red-500 text-center py-8">{{ error }}</div>
    <div v-else>
      <div v-if="filteredLogs.length === 0" class="text-gray-400 italic">{{ $t('adminLogs.noLogs') }}</div>
      <ul class="divide-y divide-gray-100">
        <li v-for="log in filteredLogs" :key="log.id" class="py-2 flex flex-col md:flex-row md:items-center gap-1 md:gap-4">
          <span class="text-xs text-gray-400 w-32">{{ formatDate(log.created_at) }}</span>
          <span class="flex-1 text-sm">
            <span class="inline-block px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200 text-xs mr-2">{{ log.action }}</span>
            {{ log.message }}
          </span>
          <span class="text-xs text-gray-500">{{ log.user_email ?? $t('adminLogs.system') }}</span>
          <span v-if="log.ip_address" class="text-xs text-gray-300 ml-2">{{ log.ip_address }}</span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, computed, onMounted, watch } from 'vue';
  import { useI18n } from 'vue-i18n';

  const { t } = useI18n();

  // Utilisation du nouveau composable
  const {
    logs,
    loadingLogs: loading,
    errorLogs: error,
    fetchLogs
  } = useAdminData()

  const search = ref('');
  const actionFilter = ref('');
  const userFilter = ref('');
  const deleteBeforeDate = ref('');
  const deletingLogs = ref(false);

  // Calcul des actions et utilisateurs uniques
  const uniqueActions = computed(() => {
    const actions = new Set<string>()
    logs.value.forEach((log: any) => {
      if (log.action) actions.add(log.action)
    })
    return Array.from(actions)
  })

  const uniqueUsers = computed(() => {
    const users = new Set<string>()
    logs.value.forEach((log: any) => {
      const user = log.user_email ?? 'Système'
      users.add(user)
    })
    return Array.from(users)
  })

  // Filtrage des logs
  const filteredLogs = computed(() => {
    const logsArray = Array.isArray(logs.value) ? logs.value : []
    let result = logsArray as any[];
    if (actionFilter.value) {
      result = result.filter((l: any) => l.action === actionFilter.value);
    }
    if (userFilter.value) {
      result = result.filter((l: any) => (l.user_email ?? 'Système') === userFilter.value);
    }
    if (search.value.trim()) {
      const s = search.value.trim().toLowerCase();
      result = result.filter((l: any) =>
        l.action?.toLowerCase().includes(s) ||
        l.message?.toLowerCase().includes(s) ||
        (l.user_email && l.user_email.toLowerCase().includes(s)) ||
        (l.ip_address && l.ip_address.toLowerCase().includes(s))
      );
    }
    return result;
  });

  async function handleDeleteLogsBefore() {
    if (!deleteBeforeDate.value) return;
    if (!confirm(t('adminLogs.deleteConfirm', { date: deleteBeforeDate.value }))) return;
    
    try {
      deletingLogs.value = true;
      // TODO: Implémenter la suppression des logs
      console.log('Suppression des logs avant:', deleteBeforeDate.value);
      deleteBeforeDate.value = '';
      // Recharger les logs après suppression
      await fetchLogs();
    } catch (error) {
      console.error('Erreur lors de la suppression des logs:', error);
    } finally {
      deletingLogs.value = false;
    }
  }

  function formatDate(date: string) {
    return new Date(date).toLocaleString();
  }

  // Charger les logs au montage du composant
  onMounted(() => {
    console.log('[AdminLogs] Component mounted, fetching logs...')
    
    // Debug auth state
    const authStore = useAuthStore()
    console.log('[AdminLogs] Auth state:', {
      isAuthenticated: authStore.isAuthenticated,
      hasToken: !!authStore.token,
      user: authStore.user
    })
    
    fetchLogs()
  })

  // Debug: watcher pour voir les changements
  watch([logs, loading, error], ([newLogs, newLoading, newError]) => {
    console.log('[AdminLogs] State changed:', {
      logs: newLogs,
      logsLength: Array.isArray(newLogs) ? newLogs.length : 'not array',
      loading: newLoading,
      error: newError
    })
  }, { immediate: true })
</script>


