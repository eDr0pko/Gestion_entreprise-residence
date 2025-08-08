export default defineNuxtRouteMiddleware((to, from) => {
  // Seulement pour les pages protégées (dashboard)
  if (to.path.startsWith('/dashboard')) {
    const authStore = useAuthStore()
    
    // Initialiser l'auth store si nécessaire
    if (process.client && !authStore.isAuthenticated) {
      authStore.initAuth()
    }
    
    // Si pas authentifié, rediriger vers login
    if (!authStore.isAuthenticated) {
      return navigateTo('/login')
    }
  }
})
