export default defineNuxtRouteMiddleware((to) => {
  // Pages publiques qui ne nécessitent pas de redirection
  const publicPages = ['/', '/login', '/Inscription', '/connexion']
  
  // Si on va vers une page publique, laisser passer
  if (publicPages.includes(to.path)) {
    return
  }
  
  // Pour toutes les autres pages, vérifier l'authentification
  const authStore = useAuthStore()
  
  // Si on est côté client et pas authentifié, rediriger vers l'accueil
  if (process.client && !authStore.isAuthenticated) {
    return navigateTo('/')
  }
})


