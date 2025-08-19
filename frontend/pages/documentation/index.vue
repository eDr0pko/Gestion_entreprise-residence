<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50">
    <!-- Header -->
    <header class="bg-white/90 backdrop-blur-md border-b border-gray-200 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center space-x-4">
            <NuxtLink to="/" class="flex items-center space-x-2">
              <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
              <span class="font-bold text-xl text-gray-900">Documentation</span>
            </NuxtLink>
          </div>
          <nav class="hidden md:flex space-x-8">
            <a href="#installation" class="text-gray-600 hover:text-blue-600 transition-colors">Installation</a>
            <a href="#api" class="text-gray-600 hover:text-blue-600 transition-colors">API</a>
            <a href="#guide" class="text-gray-600 hover:text-blue-600 transition-colors">Guide</a>
            <a href="#architecture" class="text-gray-600 hover:text-blue-600 transition-colors">Architecture</a>
          </nav>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <div class="flex">
      <!-- Sidebar Navigation -->
      <aside class="w-64 bg-white/80 backdrop-blur-sm border-r border-gray-200 min-h-screen sticky top-16">
        <nav class="p-6">
          <div class="space-y-8">
            <!-- Introduction -->
            <div>
              <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Introduction</h3>
              <ul class="space-y-2">
                <li><NuxtLink to="/documentation/introduction" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">Vue d'ensemble</NuxtLink></li>
                <li><NuxtLink to="/documentation/installation" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">Installation</NuxtLink></li>
                <li><NuxtLink to="/documentation/architecture" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">Architecture</NuxtLink></li>
              </ul>
            </div>

            <!-- Guide d'utilisation -->
            <div>
              <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Guide d'utilisation</h3>
              <ul class="space-y-2">
                <li><NuxtLink to="/documentation/authentification" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">Authentification</NuxtLink></li>
                <li><NuxtLink to="/documentation/gestion-utilisateurs" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">Gestion des utilisateurs</NuxtLink></li>
                <li><NuxtLink to="/documentation/messagerie" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">Messagerie</NuxtLink></li>
                <li><NuxtLink to="/documentation/planification-visites" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">Planification des visites</NuxtLink></li>
                <li><NuxtLink to="/documentation/incidents" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">Gestion des incidents</NuxtLink></li>
                <li><NuxtLink to="/documentation/administration" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">Administration</NuxtLink></li>
              </ul>
            </div>

            <!-- API Reference -->
            <div>
              <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">API Reference</h3>
              <ul class="space-y-2">
                <li><NuxtLink to="/documentation/api-auth" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">Authentification API</NuxtLink></li>
                <li><NuxtLink to="/documentation/api-users" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">API Utilisateurs</NuxtLink></li>
                <li><NuxtLink to="/documentation/api-messages" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">API Messages</NuxtLink></li>
                <li><NuxtLink to="/documentation/api-visites" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">API Visites</NuxtLink></li>
                <li><NuxtLink to="/documentation/api-incidents" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">API Incidents</NuxtLink></li>
                <li><NuxtLink to="/documentation/api-admin" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">API Administration</NuxtLink></li>
              </ul>
            </div>

            <!-- Développement -->
            <div>
              <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Développement</h3>
              <ul class="space-y-2">
                <li><NuxtLink to="/documentation/frontend" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">Frontend (Nuxt.js)</NuxtLink></li>
                <li><NuxtLink to="/documentation/backend-client" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">Backend Client</NuxtLink></li>
                <li><NuxtLink to="/documentation/backend-nhs" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">Backend NHS</NuxtLink></li>
                <li><NuxtLink to="/documentation/base-donnees" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">Base de données</NuxtLink></li>
                <li><NuxtLink to="/documentation/deployment" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">Déploiement</NuxtLink></li>
              </ul>
            </div>
          </div>
        </nav>
      </aside>

      <!-- Main Content Area -->
      <main class="flex-1 max-w-none">
        <div class="max-w-4xl mx-auto px-6 py-12">
          <!-- Hero Section -->
          <div class="text-center mb-16">
            <h1 class="text-5xl font-bold text-gray-900 mb-6">
              Documentation
              <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Gestion Entreprise de Résidence
              </span>
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
              Guide complet pour comprendre, installer et utiliser l'application de gestion de résidence moderne avec architecture dual backend.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
              <NuxtLink to="/documentation/installation" 
                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Démarrage rapide
              </NuxtLink>
              <NuxtLink to="/documentation/api-auth" 
                class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                API Reference
              </NuxtLink>
            </div>
          </div>

          <!-- Quick Overview Cards -->
          <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            <!-- Architecture Card -->
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200 hover:shadow-xl transition-shadow">
              <div class="flex items-center mb-4">
                <div class="p-2 bg-blue-100 rounded-lg">
                  <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                  </svg>
                </div>
                <h3 class="ml-3 font-semibold text-gray-900">Architecture Innovante</h3>
              </div>
              <p class="text-gray-600 text-sm mb-4">Architecture dual backend avec Frontend Nuxt.js, Backend-Client (Proxy) et Backend-NHS (Business Logic).</p>
              <NuxtLink to="/documentation/architecture" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                En savoir plus →
              </NuxtLink>
            </div>

            <!-- Features Card -->
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200 hover:shadow-xl transition-shadow">
              <div class="flex items-center mb-4">
                <div class="p-2 bg-green-100 rounded-lg">
                  <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <h3 class="ml-3 font-semibold text-gray-900">Fonctionnalités</h3>
              </div>
              <p class="text-gray-600 text-sm mb-4">Gestion complète : utilisateurs, messagerie, visites, incidents, administration avec tableau de bord.</p>
              <NuxtLink to="/documentation/gestion-utilisateurs" class="text-green-600 hover:text-green-700 text-sm font-medium">
                Découvrir →
              </NuxtLink>
            </div>

            <!-- API Card -->
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200 hover:shadow-xl transition-shadow">
              <div class="flex items-center mb-4">
                <div class="p-2 bg-purple-100 rounded-lg">
                  <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                  </svg>
                </div>
                <h3 class="ml-3 font-semibold text-gray-900">API REST</h3>
              </div>
              <p class="text-gray-600 text-sm mb-4">API REST complète avec authentification Laravel Sanctum, endpoints documentés et exemples.</p>
              <NuxtLink to="/documentation/api-auth" class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                Explorer l'API →
              </NuxtLink>
            </div>
          </div>

          <!-- Quick Start Section -->
          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-8 mb-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">🚀 Démarrage rapide</h2>
            <p class="text-gray-600 mb-6">Lancez l'application en quelques minutes avec notre guide d'installation.</p>
            
            <div class="space-y-4">
              <div class="flex items-start space-x-3">
                <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                <div>
                  <h4 class="font-semibold text-gray-900">Cloner le repository</h4>
                  <code class="text-sm bg-gray-800 text-green-400 px-2 py-1 rounded mt-1 inline-block">git clone [repository-url]</code>
                </div>
              </div>
              
              <div class="flex items-start space-x-3">
                <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                <div>
                  <h4 class="font-semibold text-gray-900">Installer les dépendances</h4>
                  <code class="text-sm bg-gray-800 text-green-400 px-2 py-1 rounded mt-1 inline-block">composer install && npm install</code>
                </div>
              </div>
              
              <div class="flex items-start space-x-3">
                <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">3</span>
                <div>
                  <h4 class="font-semibold text-gray-900">Lancer l'application</h4>
                  <code class="text-sm bg-gray-800 text-green-400 px-2 py-1 rounded mt-1 inline-block">./start-architecture.ps1</code>
                </div>
              </div>
            </div>
            
            <div class="mt-6">
              <NuxtLink to="/documentation/installation" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                Guide d'installation complet
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
              </NuxtLink>
            </div>
          </div>

          <!-- Architecture Overview -->
          <div class="mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Architecture système</h2>
            <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
              <div class="text-center">
                <div class="inline-flex flex-col space-y-4">
                  <!-- Frontend -->
                  <div class="bg-blue-100 px-6 py-3 rounded-lg">
                    <div class="font-semibold text-blue-800">Frontend (Nuxt.js)</div>
                    <div class="text-sm text-blue-600">Port :3000</div>
                  </div>
                  
                  <!-- Arrow Down -->
                  <svg class="w-6 h-6 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                  </svg>
                  
                  <!-- Backend Client -->
                  <div class="bg-green-100 px-6 py-3 rounded-lg">
                    <div class="font-semibold text-green-800">Backend-Client (Proxy + DB)</div>
                    <div class="text-sm text-green-600">Port :8000</div>
                  </div>
                  
                  <!-- Arrow Down -->
                  <svg class="w-6 h-6 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                  </svg>
                  
                  <!-- Backend NHS -->
                  <div class="bg-purple-100 px-6 py-3 rounded-lg">
                    <div class="font-semibold text-purple-800">Backend-NHS (Business Logic)</div>
                    <div class="text-sm text-purple-600">Port :8001</div>
                  </div>
                </div>
              </div>
              
              <div class="mt-8 text-center">
                <NuxtLink to="/documentation/architecture" 
                  class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                  Architecture détaillée
                  <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                  </svg>
                </NuxtLink>
              </div>
            </div>
          </div>

          <!-- Latest Updates -->
          <div class="mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Dernières mises à jour</h2>
            <div class="space-y-6">
              <div class="bg-white rounded-lg p-6 shadow-md border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                  <span class="text-sm font-medium text-blue-600">v2.1.0</span>
                  <span class="text-sm text-gray-500">12 Août 2025</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Personnalisation complète de l'interface</h3>
                <p class="text-gray-600 text-sm">Ajout du système de personnalisation avec logos, couleurs et textes personnalisables depuis l'interface admin.</p>
              </div>
              
              <div class="bg-white rounded-lg p-6 shadow-md border border-gray-200">
                <div class="flex items-center justify-between mb-3">
                  <span class="text-sm font-medium text-green-600">v2.0.0</span>
                  <span class="text-sm text-gray-500">10 Août 2025</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Architecture dual backend</h3>
                <p class="text-gray-600 text-sm">Refactorisation complète avec séparation Backend-Client (Proxy) et Backend-NHS (Business Logic).</p>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
  definePageMeta({
    layout: false
  })

  useHead({
    title: 'Documentation - Gestion Entreprise de Résidence',
    meta: [
      { name: 'description', content: 'Documentation complète pour l\'application de gestion de résidence avec architecture moderne dual backend.' }
    ]
  })
</script>

<style scoped>
  /* Custom scrollbar for sidebar */
  aside {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f8fafc;
  }

  aside::-webkit-scrollbar {
    width: 6px;
  }

  aside::-webkit-scrollbar-track {
    background: #f8fafc;
  }

  aside::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
  }

  aside::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }
</style>


