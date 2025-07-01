<template>
  <div>
    <!-- Navigation -->
    <NavBar />
    
    <!-- Contenu principal -->
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center pt-16">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Carte principale -->
        <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12 text-center transform hover:scale-105 transition-transform duration-300">
          <!-- Icône de bienvenue -->
          <div class="mb-8">
            <div class="w-24 h-24 mx-auto bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
              <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
              </svg>
            </div>
          </div>

          <!-- Message de bienvenue -->
          <h1 class="text-5xl md:text-6xl font-bold text-gray-800 mb-6">
            <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
              Bienvenue
            </span>
          </h1>
          
          <!-- Sous-titre -->
          <p class="text-xl md:text-2xl text-gray-600 mb-8">
            Système de Gestion d'Entreprise de Résidence
          </p>

          <!-- Description -->
          <div class="prose prose-lg mx-auto text-gray-700 mb-10">
            <p class="text-lg leading-relaxed">
              Gérez efficacement votre résidence avec notre plateforme moderne.
              Suivez les visites, gérez les invitations et communiquez avec vos résidents.
            </p>
          </div>

          <!-- Boutons d'action -->
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <NuxtLink 
              to="/dashboard" 
              class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-8 py-4 rounded-xl font-semibold text-lg hover:from-blue-600 hover:to-indigo-700 transform hover:scale-105 transition-all duration-200 shadow-lg"
            >
              Accéder au Tableau de Bord
            </NuxtLink>
            
            <NuxtLink 
              to="/about" 
              class="bg-white text-gray-700 px-8 py-4 rounded-xl font-semibold text-lg border-2 border-gray-300 hover:border-blue-500 hover:text-blue-600 transform hover:scale-105 transition-all duration-200"
            >
              En savoir plus
            </NuxtLink>
          </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ stats.residents }}</div>
            <div class="text-gray-600">Résidents</div>
          </div>
          
          <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="text-3xl font-bold text-green-600 mb-2">{{ stats.visites }}</div>
            <div class="text-gray-600">Visites du mois</div>
          </div>
          
          <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-shadow duration-300">
            <div class="text-3xl font-bold text-purple-600 mb-2">{{ stats.messages }}</div>
            <div class="text-gray-600">Messages</div>
          </div>
        </div>

        <!-- Footer -->
        <footer class="mt-16 text-center">
          <p class="text-gray-500">
            © {{ currentYear }} Gestion Entreprise de Résidence. Tous droits réservés.
          </p>
        </footer>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  middleware: 'auth' // Protéger cette page
})

// Meta données de la page
useHead({
  title: 'Bienvenue - Gestion Entreprise de Résidence',
  meta: [
    { name: 'description', content: 'Page d\'accueil du système de gestion d\'entreprise de résidence' }
  ]
})

const authStore = useAuthStore()

// Données réactives
const stats = ref({
  residents: 156,
  visites: 89,
  messages: 234
})

// Année actuelle
const currentYear = new Date().getFullYear()

// Animation au montage du composant
onMounted(() => {
  // Animation des statistiques (compteur animé)
  animateCounters()
})

// Fonction d'animation des compteurs
const animateCounters = () => {
  const duration = 2000 // 2 secondes
  const frameRate = 60
  const totalFrames = Math.round(duration / (1000 / frameRate))
  
  const targets = {
    residents: 156,
    visites: 89,
    messages: 234
  }
  
  let frame = 0
  const timer = setInterval(() => {
    frame++
    const progress = frame / totalFrames
    
    stats.value.residents = Math.round(targets.residents * progress)
    stats.value.visites = Math.round(targets.visites * progress)
    stats.value.messages = Math.round(targets.messages * progress)
    
    if (frame === totalFrames) {
      clearInterval(timer)
    }
  }, 1000 / frameRate)
}
</script>

<style scoped>
/* Animations personnalisées */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.prose {
  animation: fadeInUp 0.8s ease-out 0.3s both;
}

/* Effet de hover sur les cartes statistiques */
.bg-white\/80:hover {
  transform: translateY(-4px);
  transition: transform 0.3s ease;
}
</style>
