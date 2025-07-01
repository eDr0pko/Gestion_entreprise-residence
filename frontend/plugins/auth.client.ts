export default defineNuxtPlugin(() => {
  const authStore = useAuthStore()
  
  // Initialiser l'authentification depuis localStorage
  authStore.initAuth()
  
  console.log('Plugin auth: initialisation', {
    authenticated: authStore.isAuthenticated,
    user: authStore.user?.email,
    token: !!authStore.token
  })
})
