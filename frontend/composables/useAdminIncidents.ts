import { ref, onMounted } from 'vue';
import { useAuthStore } from '~/stores/auth';

export interface IncidentEntry {
  id: number;
  id_signaleur?: number | null;
  details: {
    datetime: string;
    object: string;
    statut: string;
    email_signaleur?: string;
    pieces_jointes?: string[];
    [key: string]: any;
  };
}

const incidents = ref<IncidentEntry[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

const authStore = useAuthStore();
const apiBase = useRuntimeConfig().public.apiBase;

async function fetchIncidents() {
  loading.value = true;
  try {
    const res = await $fetch<{ data: IncidentEntry[] }>(`${apiBase}/admin/incidents`, {
      headers: { Authorization: `Bearer ${authStore.token}` },
    });
    // Parse details for each incident
    incidents.value = res.data.map((inc: any) => {
      if (typeof inc.details === 'string') {
        try {
          inc.details = JSON.parse(inc.details);
        } catch {
          inc.details = {};
        }
      }
      return inc;
    });
  } catch (e: any) {
    error.value = 'Erreur lors du chargement des incidents';
  }
  loading.value = false;
}

onMounted(fetchIncidents);

export { incidents, loading, error, fetchIncidents };
