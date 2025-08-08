export default defineNuxtPlugin(() => {
  const authStore = useAuthStore()
  
  // Fonction pour nettoyer les sessions expirées
  const cleanExpiredSessions = () => {
    if (process.client) {
      // Vérifier si le token est encore valide
      const token = localStorage.getItem('auth_token')
      if (token) {
        try {
          // Décoder le token JWT pour vérifier l'expiration (si c'est un JWT)
          // Pour l'instant, on fait une vérification simple
          authStore.checkAuth().catch(() => {
            console.log('Session expirée, nettoyage et redirection vers l\'accueil')
            authStore.clearAuth()
            navigateTo('/')
          })
        } catch (error) {
          console.log('Erreur de validation du token, nettoyage')
          authStore.clearAuth()
          navigateTo('/')
        }
      }
    }
  }
  
  // Nettoyer au démarrage
  cleanExpiredSessions()
  
  // Nettoyer périodiquement (toutes les 5 minutes)
  if (process.client) {
    setInterval(cleanExpiredSessions, 5 * 60 * 1000)
  }
})


