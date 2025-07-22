import { ref, onMounted } from 'vue';
import { useAuthStore } from '~/stores/auth';


export interface Conversation {
  id_groupe_message: number;
  nom_groupe: string;
  date_creation: string;
  derniere_activite?: string;
  dernier_contenu?: string;
  dernier_auteur?: string;
  nombre_membres?: number;
  messages_non_lus?: number;
  derniere_connexion?: string;
  membres?: { id_personne: number; nom: string; prenom: string; email: string }[];
  messages_count?: number;
}

const conversations = ref<Conversation[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

const authStore = useAuthStore();
const apiBase = useRuntimeConfig().public.apiBase;


async function fetchConversations() {
  loading.value = true;
  try {
    const res = await $fetch<any>(`${apiBase}/conversations`, {
      headers: { Authorization: `Bearer ${authStore.token}` },
    });
    // Le backend retourne { success, conversations }
    if (res.success && Array.isArray(res.conversations)) {
      conversations.value = res.conversations;
    } else if (Array.isArray(res.data)) {
      conversations.value = res.data;
    } else {
      conversations.value = [];
    }
  } catch (e: any) {
    error.value = 'Erreur lors du chargement des conversations';
  }
  loading.value = false;
}

onMounted(fetchConversations);

export { conversations, loading, error, fetchConversations };
