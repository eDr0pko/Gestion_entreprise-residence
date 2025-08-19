<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50">
    <DocumentationLayout>
      <!-- Page Header -->
      <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Vue d'ensemble</h1>
        <p class="text-xl text-gray-600">
          Découvrez l'application de gestion d'entreprise de résidence, une solution moderne et complète pour gérer votre établissement.
        </p>
      </div>

      <!-- Introduction -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🏢 Qu'est-ce que Gestion Entreprise de Résidence ?</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <p class="text-gray-700 text-lg leading-relaxed mb-6">
            <strong>Gestion Entreprise de Résidence</strong> est une application web moderne conçue pour simplifier et optimiser la gestion quotidienne des résidences, maisons de retraite, résidences étudiantes et autres établissements collectifs.
          </p>
          
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <h3 class="font-semibold text-gray-900 mb-3">🎯 Objectifs principaux</h3>
              <ul class="space-y-2 text-gray-700">
                <li class="flex items-start">
                  <span class="text-green-500 mr-2">✓</span>
                  Centraliser la gestion des résidents et visiteurs
                </li>
                <li class="flex items-start">
                  <span class="text-green-500 mr-2">✓</span>
                  Faciliter la communication entre les parties
                </li>
                <li class="flex items-start">
                  <span class="text-green-500 mr-2">✓</span>
                  Optimiser la planification des visites
                </li>
                <li class="flex items-start">
                  <span class="text-green-500 mr-2">✓</span>
                  Suivre et résoudre les incidents
                </li>
              </ul>
            </div>
            
            <div>
              <h3 class="font-semibold text-gray-900 mb-3">🔧 Technologies utilisées</h3>
              <ul class="space-y-2 text-gray-700">
                <li class="flex items-start">
                  <span class="text-blue-500 mr-2">•</span>
                  <strong>Frontend :</strong> Nuxt.js 3, Vue 3, TypeScript, Tailwind CSS
                </li>
                <li class="flex items-start">
                  <span class="text-blue-500 mr-2">•</span>
                  <strong>Backend :</strong> Laravel 10, MySQL, Laravel Sanctum
                </li>
                <li class="flex items-start">
                  <span class="text-blue-500 mr-2">•</span>
                  <strong>Architecture :</strong> Dual Backend (Proxy + Business Logic)
                </li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- Features Overview -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">⚡ Fonctionnalités principales</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Authentification -->
          <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
              </div>
              <h3 class="ml-3 font-semibold text-gray-900">Authentification</h3>
            </div>
            <ul class="text-sm text-gray-600 space-y-1">
              <li>• Connexion sécurisée multi-rôles</li>
              <li>• Gestion des invités temporaires</li>
              <li>• Système de tokens JWT</li>
              <li>• Gestion des avatars</li>
            </ul>
          </div>

          <!-- Gestion Utilisateurs -->
          <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <div class="p-2 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <h3 class="ml-3 font-semibold text-gray-900">Gestion Utilisateurs</h3>
            </div>
            <ul class="text-sm text-gray-600 space-y-1">
              <li>• Résidents, gardiens, administrateurs</li>
              <li>• Profils complets avec statistiques</li>
              <li>• Système de bannissement</li>
              <li>• Recherche avancée</li>
            </ul>
          </div>

          <!-- Messagerie -->
          <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <div class="p-2 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
              </div>
              <h3 class="ml-3 font-semibold text-gray-900">Messagerie</h3>
            </div>
            <ul class="text-sm text-gray-600 space-y-1">
              <li>• Conversations de groupe</li>
              <li>• Messages avec fichiers joints</li>
              <li>• Réactions emoji</li>
              <li>• Notifications temps réel</li>
            </ul>
          </div>

          <!-- Planification -->
          <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <div class="p-2 bg-yellow-100 rounded-lg">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l-2 4m8-4l2 4M5 11h14m-9 4h4"/>
                </svg>
              </div>
              <h3 class="ml-3 font-semibold text-gray-900">Planning Visites</h3>
            </div>
            <ul class="text-sm text-gray-600 space-y-1">
              <li>• Planification intelligente</li>
              <li>• Gestion des créneaux</li>
              <li>• Suivi des statuts</li>
              <li>• Calendrier interactif</li>
            </ul>
          </div>

          <!-- Incidents -->
          <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <div class="p-2 bg-red-100 rounded-lg">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
              </div>
              <h3 class="ml-3 font-semibold text-gray-900">Gestion Incidents</h3>
            </div>
            <ul class="text-sm text-gray-600 space-y-1">
              <li>• Signalement rapide</li>
              <li>• Suivi de résolution</li>
              <li>• Pièces jointes</li>
              <li>• Historique complet</li>
            </ul>
          </div>

          <!-- Administration -->
          <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <div class="p-2 bg-indigo-100 rounded-lg">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
              </div>
              <h3 class="ml-3 font-semibold text-gray-900">Administration</h3>
            </div>
            <ul class="text-sm text-gray-600 space-y-1">
              <li>• Tableau de bord complet</li>
              <li>• Statistiques avancées</li>
              <li>• Logs d'activité</li>
              <li>• Personnalisation interface</li>
            </ul>
          </div>
        </div>
      </section>

      <!-- User Roles -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">👤 Rôles utilisateurs</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Admin -->
            <div class="text-center">
              <div class="p-3 bg-red-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
              </div>
              <h3 class="font-semibold text-gray-900 mb-2">Administrateur</h3>
              <p class="text-sm text-gray-600">Accès complet à toutes les fonctionnalités, gestion des utilisateurs, configuration système.</p>
            </div>

            <!-- Gardien -->
            <div class="text-center">
              <div class="p-3 bg-blue-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
              </div>
              <h3 class="font-semibold text-gray-900 mb-2">Gardien</h3>
              <p class="text-sm text-gray-600">Gestion des visites, surveillance, incidents, accès limité aux fonctions administratives.</p>
            </div>

            <!-- Résident -->
            <div class="text-center">
              <div class="p-3 bg-green-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
              </div>
              <h3 class="font-semibold text-gray-900 mb-2">Résident</h3>
              <p class="text-sm text-gray-600">Planification de visites, messagerie, signalement d'incidents, gestion du profil.</p>
            </div>

            <!-- Invité -->
            <div class="text-center">
              <div class="p-3 bg-purple-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
              </div>
              <h3 class="font-semibold text-gray-900 mb-2">Invité</h3>
              <p class="text-sm text-gray-600">Accès temporaire limité, planification de visites pour les résidents qu'ils visitent.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Quick Start -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🚀 Prêt à commencer ?</h2>
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-8">
          <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="mb-6 md:mb-0">
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Démarrez votre installation</h3>
              <p class="text-gray-600">Suivez notre guide d'installation détaillé pour configurer l'application en quelques minutes.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
              <NuxtLink to="/documentation/installation" 
                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Installation
              </NuxtLink>
              <NuxtLink to="/documentation/architecture" 
                class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Architecture
              </NuxtLink>
            </div>
          </div>
        </div>
      </section>
    </DocumentationLayout>
  </div>
</template>

<script setup lang="ts">
  definePageMeta({
    layout: false
  })

  useHead({
    title: 'Introduction - Documentation Gestion Résidence',
    meta: [
      { name: 'description', content: 'Vue d\'ensemble de l\'application de gestion d\'entreprise de résidence avec toutes ses fonctionnalités.' }
    ]
  })
</script>


