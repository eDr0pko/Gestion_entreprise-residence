

import axios from 'axios';
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';

const stats = ref<any>(null);
const loading = ref(true);
const error = ref<string | null>(null);


onMounted(async () => {
  try {
    // Utilise la config Nuxt pour l'URL de l'API
    // @ts-ignore
    const config = useRuntimeConfig ? useRuntimeConfig() : { public: { apiBase: '/api' } };
    const apiBase = config.public?.apiBase || '/api';
    const auth = useAuthStore();
    let headers = {};
    if (auth.token) {
      headers = { Authorization: `Bearer ${auth.token}` };
    }
    const res = await axios.get(`${apiBase}/admin/stats`, { headers });
    stats.value = res.data;
  } catch (e: any) {
    error.value = e?.response?.data?.message || 'Erreur lors du chargement des statistiques';
  } finally {
    loading.value = false;
  }
});

export { stats, loading, error };
