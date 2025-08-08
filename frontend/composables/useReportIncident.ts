import { ref } from 'vue';
import { useAuthStore } from '~/stores/auth';

const reportLoading = ref(false);
const reportError = ref('');
const success = ref(false);

async function reportIncident(description: string, statut: string = 'en_cours') {
  reportLoading.value = true;
  reportError.value = '';
  success.value = false;
  
  try {
    const authStore = useAuthStore();
    const config = useRuntimeConfig();
    
    // Assurer que l'auth store est initialisé
    if (process.client && !authStore.isAuthenticated) {
      authStore.initAuth();
    }
    
    if (!authStore.token) {
      reportError.value = 'Token d\'authentification manquant';
      return;
    }

    await $fetch(`${config.public.apiBase}/admin/incidents`, {
      method: 'POST',
      body: {
        datetime: new Date().toISOString(),
        object: description,
        statut
      },
      headers: { 
        'Authorization': `Bearer ${authStore.token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
    });
    
    success.value = true;
    reportError.value = '';
    
  } catch (e: any) {
    reportError.value = e?.data?.message || 'Erreur lors du signalement.';
    success.value = false;
    console.error('[REPORT INCIDENT] Exception:', e);
  } finally {
    reportLoading.value = false;
  }
}

export { reportIncident, reportLoading, reportError, success };
