export default defineNuxtPlugin(() => {
  // Le système de notifications sera initialisé automatiquement
  // par le layout default.vue via useNotifications
  return {
    provide: {
      // Autres providers si nécessaire
    }
  }
})
