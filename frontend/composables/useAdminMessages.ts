// Supprimer une conversation (groupe) par son id
async function deleteAdminConversation(groupId: number) {
  loading.value = true;
  try {
    const res = await $fetch<any>(`${apiBase}/admin/conversations/${groupId}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${authStore.token}` },
    });
    if (res.success) {
      // On recharge la liste après suppression
      await fetchAdminMessages();
      error.value = null;
    } else {
      error.value = res.message || 'Erreur lors de la suppression';
    }
  } catch (e: any) {
    if (e && e.data && e.data.message) {
      error.value = e.data.message;
    } else if (e && e.message) {
      error.value = e.message;
    } else {
      error.value = 'Erreur lors de la suppression';
    }
  }
  loading.value = false;
}
import { ref } from 'vue';
import { useAuthStore } from '~/stores/auth';

export interface AdminMessage {
  id_message: number;
  contenu_message: string;
  date_envoi: string;
  id_auteur: number;
  auteur_nom: string;
  id_groupe_message: number;
  nom_groupe: string;
}

const messages = ref<AdminMessage[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

const authStore = useAuthStore();
const apiBase = useRuntimeConfig().public.apiBase;

async function fetchAdminMessages() {
  loading.value = true;
  try {
    const res = await $fetch<any>(`${apiBase}/admin/messages`, {
      headers: { Authorization: `Bearer ${authStore.token}` },
    });
    if (res.success && Array.isArray(res.messages)) {
      messages.value = res.messages;
      error.value = null;
    } else {
      messages.value = [];
      error.value = res.message || 'Erreur lors du chargement des messages';
    }
  } catch (e: any) {
    if (e && e.data && e.data.message) {
      error.value = e.data.message;
    } else if (e && e.message) {
      error.value = e.message;
    } else {
      error.value = 'Erreur lors du chargement des messages';
    }
  }
  loading.value = false;
}

export { messages, loading, error, fetchAdminMessages, deleteAdminConversation };
