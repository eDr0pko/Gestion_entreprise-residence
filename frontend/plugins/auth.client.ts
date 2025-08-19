import { useAuthStore } from '~/stores/auth' // Ajoutez cette ligne

export default defineNuxtPlugin(() => {
  const authStore = useAuthStore()
  
  // Initialiser l'authentification depuis localStorage
  authStore.initAuth()
  
  // console.log supprimé pour éviter l'affichage en console
})


