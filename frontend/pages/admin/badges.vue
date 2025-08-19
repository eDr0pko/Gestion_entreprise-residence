<template>
  <div>
    <BadgeManagement />
  </div>
</template>

<script setup lang="ts">
  // Définir le middleware pour cette page
  definePageMeta({
    middleware: 'badges'
  })

  // Utiliser le nouveau composant de gestion des badges moderne
  import BadgeManagement from '~/components/badge/BadgeManagement.vue'
</script>


