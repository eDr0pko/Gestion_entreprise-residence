<template>
  <div>
    <NuxtPage />
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '~/stores/auth' // <-- Ajoute cette ligne

const authStore = useAuthStore()

// Initialiser l'authentification au démarrage de l'app
onMounted(() => {
  authStore.initAuth()
})
</script>
