<template>
  <div>
    <NuxtPage />
  </div>
</template>

<script setup>
const authStore = useAuthStore()

// Initialiser l'authentification au démarrage de l'app
onMounted(() => {
  authStore.initAuth()
})
</script>
