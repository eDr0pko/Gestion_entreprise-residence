<template>
  <div class="relative min-h-screen bg-gray-50">
    <NavBar />
    <aside class="fixed top-16 left-0 h-[calc(100vh-4rem)] w-64 bg-white shadow-lg z-30 flex flex-col">
      <!-- Ajoute ici d'autres liens ou sections du menu si besoin -->
    </aside>
    <main class="ml-64 pt-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Tableau de bord Administrateur</h1>
        </div>
        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <!-- ...existing code for stats cards... -->
        </div>
        <!-- Actions rapides -->
        <div class="bg-white rounded-lg shadow">
          <!-- ...existing code for actions rapides... -->
        </div>
        <!-- Liste des incidents -->
        <div class="bg-white rounded-lg shadow mt-10">
          <AdminIncidents />
        </div>
      </div>
    </main>
  </div>
</template>
<style>
/* Menu latéral sticky pour le dashboard admin */
aside {
  position: fixed;
  top: 4rem; /* hauteur de la navbar */
  left: 0;
  height: calc(100vh - 4rem);
  width: 16rem;
  z-index: 30;
  background: #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.07);
  display: flex;
  flex-direction: column;
}
main {
  margin-left: 16rem;
}
</style>

<script setup lang="ts">
  definePageMeta({
    middleware: 'auth'
  })

  useHead({
    title: 'Dashboard Admin - Gestion Entreprise de Résidence'
  })


  import AdminIncidents from '@/components/dashboard/AdminIncidents.vue'
  // ...existing code for stats, actions rapides, etc...
</script>


