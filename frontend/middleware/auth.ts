export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore()

  // Initialiser l'auth depuis localStorage si pas encore fait
  if (process.client && !authStore.isAuthenticated) {
    authStore.initAuth()
  }

  // Vérifier si l'utilisateur est authentifié (membre ou invité)
  if (!authStore.isAuthenticated) {
    return navigateTo('/')
  }

  // Vérifications de rôles pour les routes admin (seulement pour les membres)
  if (to.path.startsWith('/admin')) {
    if (authStore.userRole !== 'admin') {
      throw createError({
        statusCode: 403,
        statusMessage: 'Accès refusé - Réservé aux administrateurs'
      })
    }
  }
})


