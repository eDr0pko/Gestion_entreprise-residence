export default defineNuxtRouteMiddleware(async (to, from) => {
  if (process.server) return // Skip sur le serveur
  
  const { useAuthStore } = await import('@/stores/auth')
  const authStore = useAuthStore()
  
  // Vérification de l'authentification
  if (!authStore.isAuthenticated) {
    // Redirection vers la page de connexion
    return navigateTo('/login')
  }
  
  // Vérification des permissions pour certaines routes
  const user = authStore.user
  const restrictedRoutes = ['/admin', '/settings']
  
  if (restrictedRoutes.some(route => to.path.startsWith(route))) {
    const isAdmin = user?.role === 'admin' || 
                   user?.niveau_acces === 'super_admin' || 
                   user?.niveau_acces === 'admin_standard'
    
    if (!isAdmin) {
      throw createError({
        statusCode: 403,
        statusMessage: 'Accès non autorisé'
      })
    }
  }
})


