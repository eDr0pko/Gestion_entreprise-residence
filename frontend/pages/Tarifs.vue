<template>
  <div class="min-h-screen bg-gradient-to-br from-white via-teal-50 to-cyan-50 font-sans text-gray-800">
    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-teal-700">NeoAccess</h1>
        <nav class="space-x-6">
          <RouterLink to="/presentation" class="text-gray-600 hover:text-teal-600">Présentation</RouterLink>
          <RouterLink to="/tarifs" class="text-teal-600 font-medium">Tarifs</RouterLink>
          <a href="#contact" class="text-gray-600 hover:text-teal-600">Contact</a>
        </nav>
      </div>
    </header>

    <!-- Tarifs -->
    <section class="py-20 px-6">
      <h2 class="text-4xl font-bold text-center text-teal-700 mb-10">Nos Offres</h2>
      <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-10">
        <!-- Basic -->
        <div class="border rounded-xl shadow-md p-8 text-center bg-white">
          <h3 class="text-xl font-bold text-teal-600 mb-4">Basic</h3>
          <p class="text-3xl font-bold mb-4">29€<span class="text-base text-gray-500"> /mois</span></p>
          <ul class="mb-6 text-sm space-y-2">
            <li>✅ Prise de RDV en ligne</li>
            <li>✅ Notifications e-mail</li>
            <li>✅ Planning hebdomadaire</li>
            <li>⛔ Intégration objets connectés</li>
          </ul>
          <a href="#" class="inline-block bg-teal-600 text-white px-5 py-2 rounded-full hover:bg-teal-700">Choisir</a>
        </div>

        <!-- Pro -->
        <div class="border-2 border-teal-600 rounded-xl shadow-lg p-8 text-center bg-white">
          <h3 class="text-xl font-bold text-teal-700 mb-4">Pro</h3>
          <p class="text-3xl font-bold mb-4">59€<span class="text-base text-gray-500"> /mois</span></p>
          <ul class="mb-6 text-sm space-y-2">
            <li>✅ Toutes les fonctionnalités Basic</li>
            <li>✅ Intégration objets connectés</li>
            <li>✅ Badge d’accès temporaires</li>
            <li>✅ Support prioritaire</li>
          </ul>
          <a href="#" class="inline-block bg-teal-700 text-white px-5 py-2 rounded-full hover:bg-teal-800">Choisir</a>
        </div>

        <!-- Entreprise -->
        <div class="border rounded-xl shadow-md p-8 text-center bg-white">
          <h3 class="text-xl font-bold text-teal-600 mb-4">Entreprise</h3>
          <p class="text-3xl font-bold mb-4">Sur devis</p>
          <ul class="mb-6 text-sm space-y-2">
            <li>✅ Fonctionnalités Pro + personnalisations</li>
            <li>✅ Intégration domotique complète</li>
            <li>✅ Dashboard analytique</li>
            <li>✅ Formation sur site / à distance</li>
          </ul>
          <a href="#contact" class="inline-block bg-gray-800 text-white px-5 py-2 rounded-full hover:bg-gray-900">Demander un devis</a>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-100 text-center py-6 text-sm text-gray-600" id="contact">
      © 2025 NeoAccess — Contact : contact@neoaccess.io
    </footer>
  </div>
</template>

<script setup>
// Aucune logique ici
</script>
