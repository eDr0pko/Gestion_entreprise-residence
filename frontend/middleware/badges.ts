export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore()

  // Initialiser l'auth depuis localStorage si pas encore fait
  if (process.client && !authStore.isAuthenticated) {
    authStore.initAuth()
  }

  // Vérifier si l'utilisateur est authentifié
  if (!authStore.isAuthenticated) {
    console.log('Middleware badges: utilisateur non authentifié, redirection vers /')
    return navigateTo('/')
  }

  // Vérifier si l'utilisateur a le rôle requis (gardien ou admin)
  const allowedRoles = ['gardien', 'admin']
  if (!allowedRoles.includes(authStore.userRole)) {
    throw createError({
      statusCode: 403,
      statusMessage: 'Accès refusé - Réservé aux gardiens et administrateurs'
    })
  }

  console.log('Middleware badges: accès autorisé pour:', authStore.userRole)
})
