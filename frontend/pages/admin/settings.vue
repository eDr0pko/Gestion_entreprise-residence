<template>
  <div class="relative min-h-screen bg-gray-50">
    <NavBar />
    <AdminSidebar />
    <main class="ml-64 pt-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <AdminSettings />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
// Authentification requise pour cette page
definePageMeta({
  middleware: 'auth'
})

// Meta de la page
useHead({
  title: 'Administration - Paramètres de l\'application',
  meta: [
    { name: 'description', content: 'Interface d\'administration pour personnaliser l\'application' }
  ]
})

// Vérifier que l'utilisateur est admin si nécessaire
// const { user } = useAuth()
// if (!user.value?.is_admin) {
//   throw createError({
//     statusCode: 403,
//     statusMessage: 'Accès non autorisé'
//   })
// }
</script>

<style scoped>
/* Styles spécifiques à la page settings */
</style>
