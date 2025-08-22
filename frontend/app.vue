<template>
  <div :class="{ 'dark': isDark }">
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </div>
</template>

<script setup>
  import { onMounted } from 'vue'
  import { useAuthStore } from '~/stores/auth'
  import { useTheme } from '~/composables/useTheme'

const authStore = useAuthStore()
const { isDark, initTheme } = useTheme()

// Initialiser l'authentification et le thème au démarrage de l'app
onMounted(() => {
  authStore.initAuth()
  initTheme()
})
</script>


