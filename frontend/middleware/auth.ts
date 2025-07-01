export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore()

  // Initialiser l'auth depuis localStorage si pas encore fait
  if (process.client && !authStore.isAuthenticated) {
    authStore.initAuth()
  }

  // Vérifier si l'utilisateur est authentifié
  if (!authStore.isAuthenticated || !authStore.token) {
    console.log('Middleware auth: utilisateur non authentifié, redirection vers /login')
    return navigateTo('/login')
  }

  console.log('Middleware auth: utilisateur authentifié', {
    user: authStore.user?.email,
    token: !!authStore.token
  })

  // Vérifier les autorisations selon la route
  const user = authStore.user
  if (user) {
    // Routes spécifiques aux admins
    if (to.path.startsWith('/admin') && user.role !== 'admin') {
      throw createError({
        statusCode: 403,
        statusMessage: 'Accès refusé'
      })
    }

    // Routes spécifiques aux gardiens
    if (to.path.startsWith('/gardien') && user.role !== 'gardien') {
      throw createError({
        statusCode: 403,
        statusMessage: 'Accès refusé'
      })
    }

    // Routes spécifiques aux résidents
    if (to.path.startsWith('/resident') && user.role !== 'resident') {
      throw createError({
        statusCode: 403,
        statusMessage: 'Accès refusé'
      })
    }
  }
})
