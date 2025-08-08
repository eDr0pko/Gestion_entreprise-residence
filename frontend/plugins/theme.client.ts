export default defineNuxtPlugin(async () => {
  // Initialiser les paramètres de l'application côté client
  if (process.client) {
    const { loadPublicSettings } = useAppSettings()
    const { applyTheme } = useAppTheme()
    
    try {
      // Charger les paramètres publics
      await loadPublicSettings()
      
      // Appliquer le thème
      applyTheme()
      
      console.log('Thème de l\'application initialisé')
    } catch (error) {
      console.warn('Erreur lors de l\'initialisation du thème:', error)
    }
  }
})


