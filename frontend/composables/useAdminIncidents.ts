import { ref } from 'vue';
import { useAuthStore } from '~/stores/auth';

export interface IncidentEntry {
  id: number;
  datetime: string;
  object: string;
  statut: string;
  id_signaleur?: number | null;
  nom_signaleur?: string | null;
  prenom_signaleur?: string | null;
  pieces_jointes?: string[] | null;
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
    incidents.value = res.data;
  } catch (e: any) {
    error.value = 'Erreur lors du chargement des incidents';
  }
  loading.value = false;
}



export { incidents, loading, error, fetchIncidents };
