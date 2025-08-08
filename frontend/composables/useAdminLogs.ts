import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '~/stores/auth';

export interface LogEntry {
  id: number;
  action: string;
  message: string;
  created_at: string;
  user_email?: string;
  ip_address?: string;
}

const logs = ref<LogEntry[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);
const deletingLogs = ref(false);

async function fetchLogs() {
  loading.value = true;
  error.value = null;
  
  try {
    const authStore = useAuthStore();
    const config = useRuntimeConfig();
    
    // Assurer que l'auth store est initialisé
    if (process.client && !authStore.isAuthenticated) {
      authStore.initAuth();
    }
    
    if (!authStore.token) {
      error.value = 'Token d\'authentification manquant';
      logs.value = [];
      return;
    }

    const res = await $fetch<any>(`${config.public.apiBase}/admin/logs`, {
      headers: { 
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
    });
    
    if (res.success && res.data && Array.isArray(res.data.data)) {
      logs.value = res.data.data;
    } else if (res.data && Array.isArray(res.data)) {
      logs.value = res.data;
    } else if (Array.isArray(res)) {
      logs.value = res;
    } else {
      logs.value = [];
    }
    
    error.value = null;
    
  } catch (e: any) {
    error.value = 'Erreur lors du chargement des logs';
    logs.value = [];
    console.error('[ADMIN LOGS] Exception:', e);
  } finally {
    loading.value = false;
  }
}

async function deleteLogsBefore(date: string) {
  if (!date) return;
  
  deletingLogs.value = true;
  error.value = null;
  
  try {
    const authStore = useAuthStore();
    const config = useRuntimeConfig();
    
    if (!authStore.token) {
      error.value = 'Token d\'authentification manquant';
      return;
    }

    await $fetch(`${config.public.apiBase}/admin/logs/delete-before`, {
      method: 'POST',
      body: { date },
      headers: { 
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
    });
    
    await fetchLogs();
    
  } catch (e: any) {
    error.value = 'Erreur lors de la suppression des logs';
    console.error('[ADMIN LOGS] Delete error:', e);
  } finally {
    deletingLogs.value = false;
  }
}

// Actions uniques pour le filtre
const uniqueActions = computed(() => {
  const actions = logs.value.map(l => l.action);
  return [...new Set(actions)].sort();
});

// Utilisateurs uniques pour le filtre
const uniqueUsers = computed(() => {
  const users = logs.value.map(l => l.user_email ?? 'Système');
  return [...new Set(users)].sort((a, b) => a.localeCompare(b, 'fr'));
});

onMounted(fetchLogs);

export { logs, loading, error, deletingLogs, fetchLogs, deleteLogsBefore, uniqueActions, uniqueUsers };


