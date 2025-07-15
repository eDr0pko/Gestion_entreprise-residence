<template>
  <div class="min-h-screen bg-white text-gray-800 font-sans">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <div class="flex items-center space-x-2">
          <!--<img src="/img/logo.svg" alt="Logo" class="h-8" />-->    
          <span class="text-xl font-bold text-green-600">NeoAccess</span>
        </div>
        <nav class="hidden md:flex space-x-6 text-sm font-medium">
          <a href="#offres" class="hover:text-green-600 transition">Offres</a>
          <a href="#faq" class="hover:text-green-600 transition">FAQ</a>
          <a href="#contact" class="hover:text-green-600 transition">Contact</a>
        </nav>
      </div>
    </header>

    <!-- Hero -->
    <section class="bg-gradient-to-br from-green-600 to-emerald-500 text-white text-center py-20 px-6">
      <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-6">La sécurité et la gestion de vos accès, en toute simplicité</h1>
      <p class="max-w-xl mx-auto text-lg opacity-90 mb-6">Choisissez l'offre la plus adaptée à votre entreprise, résidence ou espace partagé. NeoAccess vous connecte à vos visiteurs.</p>
      <a href="#offres" class="inline-block bg-white text-green-600 font-semibold px-6 py-3 rounded-full shadow hover:bg-gray-100 transition">Voir les offres</a>
    </section>

    <!-- Tarifs -->
    <section id="offres" class="py-24 bg-gray-50">
      <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-12">Choisissez votre formule</h2>
        <div class="grid md:grid-cols-3 gap-8">
          <!-- Offre Basic -->
          <div class="bg-white rounded-xl border shadow-sm p-8 flex flex-col items-center text-center">
            <h3 class="text-xl font-semibold mb-2">Essential</h3>
            <p class="text-4xl font-extrabold text-green-600 mb-4">29€<span class="text-base font-normal">/mois</span></p>
            <ul class="text-sm text-gray-600 space-y-2 mb-6">
              <li>✔ Prise de RDV simplifiée</li>
              <li>✔ Notifications e-mail</li>
              <li>✔ Planning en ligne</li>
              <li>✖ Objets connectés</li>
            </ul>
            <button class="bg-green-600 text-white px-5 py-2 rounded-full font-semibold hover:bg-green-700 transition">Choisir</button>
          </div>

          <!-- Offre Pro -->
          <div class="bg-white rounded-xl border-2 border-green-600 shadow-md p-8 flex flex-col items-center text-center scale-105">
            <span class="text-xs uppercase text-green-600 font-bold mb-2">Populaire</span>
            <h3 class="text-xl font-semibold mb-2">Premium</h3>
            <p class="text-4xl font-extrabold text-green-600 mb-4">59€<span class="text-base font-normal">/mois</span></p>
            <ul class="text-sm text-gray-600 space-y-2 mb-6">
              <li>✔ Fonctionnalités Essential</li>
              <li>✔ Intégration objets connectés</li>
              <li>✔ Badges temporaires</li>
              <li>✔ Support prioritaire</li>
            </ul>
            <button class="bg-green-600 text-white px-5 py-2 rounded-full font-semibold hover:bg-green-700 transition">Choisir</button>
          </div>

          <!-- Offre Entreprise -->
          <div class="bg-white rounded-xl border shadow-sm p-8 flex flex-col items-center text-center">
            <h3 class="text-xl font-semibold mb-2">Entreprise</h3>
            <p class="text-3xl font-bold text-green-600 mb-4">Sur devis</p>
            <ul class="text-sm text-gray-600 space-y-2 mb-6">
              <li>✔ Toutes les fonctionnalités Premium</li>
              <li>✔ Personnalisation complète</li>
              <li>✔ Dashboard & Analytics</li>
              <li>✔ Formation dédiée</li>
            </ul>
            <button class="bg-gray-800 text-white px-5 py-2 rounded-full font-semibold hover:bg-gray-900 transition">Demander un devis</button>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="bg-white py-20 px-6">
      <div class="max-w-4xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-10">Questions fréquentes</h2>
        <div class="space-y-6">
          <div>
            <h4 class="font-semibold text-lg">Puis-je modifier mon offre plus tard ?</h4>
            <p class="text-sm text-gray-600">Oui, vous pouvez passer d'une offre à une autre à tout moment depuis votre espace client.</p>
          </div>
          <div>
            <h4 class="font-semibold text-lg">Les équipements connectés sont-ils inclus ?</h4>
            <p class="text-sm text-gray-600">L'intégration est incluse dans l'offre Premium et supérieure. Les équipements sont à commander séparément.</p>
          </div>
          <div>
            <h4 class="font-semibold text-lg">Proposez-vous une période d'essai ?</h4>
            <p class="text-sm text-gray-600">Oui, vous bénéficiez de 14 jours gratuits pour tester toutes les fonctionnalités.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact CTA -->
    <section id="contact" class="bg-gradient-to-r from-green-600 to-emerald-500 text-white py-16 text-center">
      <h2 class="text-3xl font-bold mb-4">Des questions supplémentaires ?</h2>
      <p class="mb-6 text-lg max-w-xl mx-auto">Notre équipe est à votre disposition pour vous accompagner dans votre choix ou vous fournir un devis personnalisé.</p>
      <a href="mailto:contact@neoaccess.io" class="bg-white text-green-700 px-6 py-3 rounded-full font-semibold hover:bg-gray-100 transition">Nous contacter</a>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-100 border-t text-sm text-gray-600 text-center py-6">
      © 2025 NeoAccess — Contact : <a href="mailto:contact@neoaccess.io" class="underline hover:text-green-600">contact@neoaccess.io</a>
    </footer>
  </div>
</template>

<script setup>
// Aucun script nécessaire pour cette page statique
</script>

<style scoped>
/* Ajouter animations ou responsivité ici si besoin */
</style>
