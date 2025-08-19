<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50">
    <DocumentationLayout>
      <!-- Page Header -->
      <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Architecture</h1>
        <p class="text-xl text-gray-600">
          Découvrez l'architecture innovante dual backend de l'application de gestion d'entreprise de résidence.
        </p>
      </div>

      <!-- Architecture Overview -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🏗️ Vue d'ensemble de l'architecture</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <p class="text-gray-700 text-lg mb-8">
            L'application utilise une <strong>architecture innovante en 2 backends</strong> permettant une séparation claire des responsabilités et une meilleure scalabilité.
          </p>
          
          <!-- Architecture Diagram -->
          <div class="text-center mb-8">
            <div class="inline-flex flex-col space-y-6">
              <!-- Frontend -->
              <div class="relative">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-8 py-4 rounded-xl shadow-lg">
                  <div class="text-lg font-semibold">Frontend (Nuxt.js)</div>
                  <div class="text-sm opacity-90">Vue 3 + TypeScript + Tailwind CSS</div>
                  <div class="text-xs opacity-75 mt-1">Port :3000</div>
                </div>
                <div class="absolute -top-2 -right-2 bg-blue-700 text-white text-xs px-2 py-1 rounded-full">
                  Interface utilisateur
                </div>
              </div>
              
              <!-- Arrow Down -->
              <div class="flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
                <span class="ml-2 text-sm text-gray-600">Requêtes API</span>
              </div>
              
              <!-- Backend Client -->
              <div class="relative">
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white px-8 py-4 rounded-xl shadow-lg">
                  <div class="text-lg font-semibold">Backend-Client (Proxy + DB)</div>
                  <div class="text-sm opacity-90">Laravel + MySQL + Sanctum Auth</div>
                  <div class="text-xs opacity-75 mt-1">Port :8000</div>
                </div>
                <div class="absolute -top-2 -right-2 bg-green-700 text-white text-xs px-2 py-1 rounded-full">
                  Proxy + Base de données
                </div>
              </div>
              
              <!-- Arrow Down -->
              <div class="flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
                <span class="ml-2 text-sm text-gray-600">Business Logic</span>
              </div>
              
              <!-- Backend NHS -->
              <div class="relative">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-8 py-4 rounded-xl shadow-lg">
                  <div class="text-lg font-semibold">Backend-NHS (Business Logic)</div>
                  <div class="text-sm opacity-90">Laravel + Logique métier complète</div>
                  <div class="text-xs opacity-75 mt-1">Port :8001</div>
                </div>
                <div class="absolute -top-2 -right-2 bg-purple-700 text-white text-xs px-2 py-1 rounded-full">
                  NHS propriétaire
                </div>
              </div>
            </div>
          </div>
          
          <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
            <p class="text-blue-700 text-sm">
              <strong>Avantage clé :</strong> Cette architecture permet de séparer les préoccupations tout en maintenant une communication fluide entre les couches.
            </p>
          </div>
        </div>
      </section>

      <!-- Frontend Details -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🎨 Frontend (Nuxt.js)</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <div class="grid md:grid-cols-2 gap-8">
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">📱 Technologies utilisées</h3>
              <ul class="space-y-2 text-gray-700">
                <li class="flex items-center">
                  <span class="text-blue-500 mr-2">•</span>
                  <strong>Nuxt.js 3 :</strong> Framework Vue.js full-stack
                </li>
                <li class="flex items-center">
                  <span class="text-blue-500 mr-2">•</span>
                  <strong>Vue 3 :</strong> Framework JavaScript réactif
                </li>
                <li class="flex items-center">
                  <span class="text-blue-500 mr-2">•</span>
                  <strong>TypeScript :</strong> Typage statique
                </li>
                <li class="flex items-center">
                  <span class="text-blue-500 mr-2">•</span>
                  <strong>Tailwind CSS :</strong> Framework CSS utilitaire
                </li>
                <li class="flex items-center">
                  <span class="text-blue-500 mr-2">•</span>
                  <strong>Pinia :</strong> Gestion d'état
                </li>
                <li class="flex items-center">
                  <span class="text-blue-500 mr-2">•</span>
                  <strong>Vue I18n :</strong> Internationalisation
                </li>
              </ul>
            </div>
            
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">🎯 Responsabilités</h3>
              <ul class="space-y-2 text-gray-700">
                <li class="flex items-start">
                  <span class="text-green-500 mr-2 mt-1">✓</span>
                  Interface utilisateur responsive
                </li>
                <li class="flex items-start">
                  <span class="text-green-500 mr-2 mt-1">✓</span>
                  Gestion des états locaux (Pinia)
                </li>
                <li class="flex items-start">
                  <span class="text-green-500 mr-2 mt-1">✓</span>
                  Communication avec Backend-Client
                </li>
                <li class="flex items-start">
                  <span class="text-green-500 mr-2 mt-1">✓</span>
                  Authentification côté client
                </li>
                <li class="flex items-start">
                  <span class="text-green-500 mr-2 mt-1">✓</span>
                  Routage et navigation
                </li>
                <li class="flex items-start">
                  <span class="text-green-500 mr-2 mt-1">✓</span>
                  Validation des formulaires
                </li>
              </ul>
            </div>
          </div>
          
          <div class="mt-8">
            <h3 class="font-semibold text-gray-900 mb-4">📁 Structure des dossiers</h3>
            <div class="bg-gray-900 rounded-lg p-4">
              <code class="text-green-400 font-mono text-sm">
                frontend/<br>
                ├── components/          # Composants réutilisables<br>
                ├── pages/              # Pages de l'application<br>
                ├── layouts/            # Layouts partagés<br>
                ├── stores/             # Stores Pinia<br>
                ├── composables/        # Composables Vue<br>
                ├── middleware/         # Middleware de route<br>
                ├── plugins/            # Plugins Nuxt<br>
                ├── assets/             # Assets statiques<br>
                ├── types/              # Définitions TypeScript<br>
                ├── i18n/               # Fichiers de traduction<br>
                └── nuxt.config.ts      # Configuration Nuxt
              </code>
            </div>
          </div>
        </div>
      </section>

      <!-- Backend Client Details -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🔄 Backend-Client (Proxy + Database)</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <div class="grid md:grid-cols-2 gap-8 mb-8">
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">🛠️ Technologies</h3>
              <ul class="space-y-2 text-gray-700">
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">•</span>
                  <strong>Laravel 10 :</strong> Framework PHP
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">•</span>
                  <strong>MySQL :</strong> Base de données relationnelle
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">•</span>
                  <strong>Laravel Sanctum :</strong> Authentification API
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">•</span>
                  <strong>Guzzle HTTP :</strong> Client HTTP pour proxy
                </li>
              </ul>
            </div>
            
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">📋 Responsabilités</h3>
              <ul class="space-y-2 text-gray-700">
                <li class="flex items-start">
                  <span class="text-blue-500 mr-2 mt-1">•</span>
                  Authentification locale (Sanctum)
                </li>
                <li class="flex items-start">
                  <span class="text-blue-500 mr-2 mt-1">•</span>
                  Accès direct à la base de données
                </li>
                <li class="flex items-start">
                  <span class="text-blue-500 mr-2 mt-1">•</span>
                  Proxy vers Backend-NHS
                </li>
                <li class="flex items-start">
                  <span class="text-blue-500 mr-2 mt-1">•</span>
                  Gestion des fichiers (avatars, uploads)
                </li>
                <li class="flex items-start">
                  <span class="text-blue-500 mr-2 mt-1">•</span>
                  Recherches optimisées
                </li>
              </ul>
            </div>
          </div>
          
          <!-- Proxy System -->
          <div class="bg-green-50 border-l-4 border-green-400 p-6 mb-6">
            <h3 class="font-semibold text-green-800 mb-3">🔄 Système de Proxy</h3>
            <p class="text-green-700 text-sm mb-3">
              Le Backend-Client agit comme un proxy intelligent vers le Backend-NHS, redirigeant automatiquement les requêtes métier.
            </p>
            <div class="bg-green-100 rounded-lg p-4">
              <code class="text-green-800 font-mono text-sm">
                Frontend → Backend-Client → Backend-NHS<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;↓<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Réponse traitée
              </code>
            </div>
          </div>
          
          <!-- Key Controllers -->
          <div>
            <h3 class="font-semibold text-gray-900 mb-4">🎛️ Contrôleurs principaux</h3>
            <div class="grid md:grid-cols-2 gap-4">
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">UnifiedProxyController</h4>
                <p class="text-gray-600 text-sm">Gère le routage automatique vers Backend-NHS</p>
              </div>
              
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">DatabaseController</h4>
                <p class="text-gray-600 text-sm">Accès direct optimisé à la base de données</p>
              </div>
              
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">AuthController</h4>
                <p class="text-gray-600 text-sm">Authentification et gestion des tokens</p>
              </div>
              
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">LogoUploadController</h4>
                <p class="text-gray-600 text-sm">Upload et gestion des fichiers</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Backend NHS Details -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🧠 Backend-NHS (Business Logic)</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <div class="grid md:grid-cols-2 gap-8 mb-8">
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">💼 Logique métier</h3>
              <ul class="space-y-2 text-gray-700">
                <li class="flex items-start">
                  <span class="text-purple-500 mr-2 mt-1">•</span>
                  Gestion complète des utilisateurs
                </li>
                <li class="flex items-start">
                  <span class="text-purple-500 mr-2 mt-1">•</span>
                  Système de messagerie avancé
                </li>
                <li class="flex items-start">
                  <span class="text-purple-500 mr-2 mt-1">•</span>
                  Planification intelligente des visites
                </li>
                <li class="flex items-start">
                  <span class="text-purple-500 mr-2 mt-1">•</span>
                  Traitement des incidents
                </li>
                <li class="flex items-start">
                  <span class="text-purple-500 mr-2 mt-1">•</span>
                  Analyses et statistiques avancées
                </li>
                <li class="flex items-start">
                  <span class="text-purple-500 mr-2 mt-1">•</span>
                  Système de logs complet
                </li>
              </ul>
            </div>
            
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">🔒 Fonctionnalités NHS</h3>
              <ul class="space-y-2 text-gray-700">
                <li class="flex items-start">
                  <span class="text-orange-500 mr-2 mt-1">🔥</span>
                  Analyses prédictives
                </li>
                <li class="flex items-start">
                  <span class="text-orange-500 mr-2 mt-1">🔥</span>
                  Automatisations intelligentes
                </li>
                <li class="flex items-start">
                  <span class="text-orange-500 mr-2 mt-1">🔥</span>
                  Maintenance prédictive
                </li>
                <li class="flex items-start">
                  <span class="text-orange-500 mr-2 mt-1">🔥</span>
                  Surveillance sécurité avancée
                </li>
                <li class="flex items-start">
                  <span class="text-orange-500 mr-2 mt-1">🔥</span>
                  Machine Learning intégré
                </li>
              </ul>
            </div>
          </div>
          
          <!-- NHS Features -->
          <div class="bg-purple-50 border-l-4 border-purple-400 p-6 mb-6">
            <h3 class="font-semibold text-purple-800 mb-3">🔥 Fonctionnalités NHS Propriétaires</h3>
            <p class="text-purple-700 text-sm mb-3">
              Le Backend-NHS contient des fonctionnalités avancées et propriétaires non distribuables.
            </p>
            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <h4 class="font-medium text-purple-800 mb-2">Endpoints NHS :</h4>
                <ul class="text-purple-700 text-sm space-y-1">
                  <li>• <code>/api/nhs/advanced-analytics</code></li>
                  <li>• <code>/api/nhs/smart-automation</code></li>
                  <li>• <code>/api/nhs/predictive-maintenance</code></li>
                  <li>• <code>/api/nhs/security-monitoring</code></li>
                </ul>
              </div>
              <div>
                <h4 class="font-medium text-purple-800 mb-2">Capacités avancées :</h4>
                <ul class="text-purple-700 text-sm space-y-1">
                  <li>• Prédiction d'occupation</li>
                  <li>• Optimisation énergétique</li>
                  <li>• Détection d'anomalies</li>
                  <li>• Recommandations IA</li>
                </ul>
              </div>
            </div>
          </div>
          
          <!-- Controllers -->
          <div>
            <h3 class="font-semibold text-gray-900 mb-4">🎛️ Contrôleurs métier</h3>
            <div class="grid md:grid-cols-3 gap-4">
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">AuthController</h4>
                <p class="text-gray-600 text-sm">Authentification complète et profils</p>
              </div>
              
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">MessageController</h4>
                <p class="text-gray-600 text-sm">Messagerie et conversations</p>
              </div>
              
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">VisiteController</h4>
                <p class="text-gray-600 text-sm">Planification et gestion des visites</p>
              </div>
              
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">IncidentController</h4>
                <p class="text-gray-600 text-sm">Gestion des incidents</p>
              </div>
              
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">GuestController</h4>
                <p class="text-gray-600 text-sm">Gestion des invités</p>
              </div>
              
              <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-2">ApiController (NHS)</h4>
                <p class="text-gray-600 text-sm">Fonctionnalités NHS avancées</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Database Architecture -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🗄️ Architecture de la base de données</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <div class="grid md:grid-cols-2 gap-8 mb-8">
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">📊 Tables principales</h3>
              <ul class="space-y-2 text-gray-700 text-sm">
                <li><strong>personne :</strong> Utilisateurs (admin, gardien, résident)</li>
                <li><strong>invite :</strong> Invités temporaires</li>
                <li><strong>visite :</strong> Planification des visites</li>
                <li><strong>groupe_message :</strong> Conversations de groupe</li>
                <li><strong>message :</strong> Messages individuels</li>
                <li><strong>incident :</strong> Signalements d'incidents</li>
                <li><strong>logs :</strong> Journaux d'activité</li>
                <li><strong>app_settings :</strong> Configuration de l'application</li>
              </ul>
            </div>
            
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">🔗 Relations clés</h3>
              <ul class="space-y-2 text-gray-700 text-sm">
                <li><strong>Personne → Invite :</strong> One-to-One</li>
                <li><strong>Personne → Visite :</strong> One-to-Many</li>
                <li><strong>Personne → GroupeMessage :</strong> Many-to-Many</li>
                <li><strong>GroupeMessage → Message :</strong> One-to-Many</li>
                <li><strong>Personne → Incident :</strong> One-to-Many</li>
                <li><strong>Message → Fichier :</strong> One-to-Many</li>
                <li><strong>Message → Reaction :</strong> One-to-Many</li>
              </ul>
            </div>
          </div>
          
          <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="font-semibold text-gray-900 mb-4">🔑 Points clés du schéma</h3>
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-medium text-gray-900 mb-2">Flexibilité des rôles :</h4>
                <p class="text-gray-600 text-sm">
                  Chaque personne peut avoir plusieurs rôles (admin, gardien, résident) via des tables séparées.
                </p>
              </div>
              
              <div>
                <h4 class="font-medium text-gray-900 mb-2">Système de messagerie :</h4>
                <p class="text-gray-600 text-sm">
                  Architecture scalable avec groupes, messages, fichiers et réactions.
                </p>
              </div>
              
              <div>
                <h4 class="font-medium text-gray-900 mb-2">Traçabilité complète :</h4>
                <p class="text-gray-600 text-sm">
                  Tous les événements sont loggés avec détails et contexte.
                </p>
              </div>
              
              <div>
                <h4 class="font-medium text-gray-900 mb-2">Personnalisation :</h4>
                <p class="text-gray-600 text-sm">
                  Table app_settings pour configurer l'apparence et les paramètres.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Communication Flow -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🔄 Flux de communication</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <div class="space-y-8">
            <!-- Request Flow -->
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">📤 Flux de requête standard</h3>
              <div class="bg-gray-50 rounded-lg p-6">
                <div class="space-y-4">
                  <div class="flex items-center space-x-4">
                    <div class="bg-blue-500 text-white px-3 py-1 rounded text-sm">Frontend</div>
                    <span class="text-gray-400">→</span>
                    <span class="text-gray-600 text-sm">Requête HTTP avec token Sanctum</span>
                  </div>
                  
                  <div class="flex items-center space-x-4">
                    <div class="bg-green-500 text-white px-3 py-1 rounded text-sm">Backend-Client</div>
                    <span class="text-gray-400">→</span>
                    <span class="text-gray-600 text-sm">Authentification + Routing décision</span>
                  </div>
                  
                  <div class="flex items-center space-x-4">
                    <div class="bg-purple-500 text-white px-3 py-1 rounded text-sm">Backend-NHS</div>
                    <span class="text-gray-400">→</span>
                    <span class="text-gray-600 text-sm">Traitement logique métier</span>
                  </div>
                  
                  <div class="flex items-center space-x-4">
                    <div class="bg-gray-500 text-white px-3 py-1 rounded text-sm">Réponse</div>
                    <span class="text-gray-400">←</span>
                    <span class="text-gray-600 text-sm">Données formatées JSON</span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Database Access -->
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">🗄️ Accès base de données</h3>
              <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-green-50 border-l-4 border-green-400 p-4">
                  <h4 class="font-medium text-green-800 mb-2">Via Backend-Client</h4>
                  <ul class="text-green-700 text-sm space-y-1">
                    <li>• Recherches optimisées</li>
                    <li>• Requêtes de visites</li>
                    <li>• Gestion des fichiers</li>
                    <li>• Authentification Sanctum</li>
                  </ul>
                </div>
                
                <div class="bg-purple-50 border-l-4 border-purple-400 p-4">
                  <h4 class="font-medium text-purple-800 mb-2">Via Backend-NHS</h4>
                  <ul class="text-purple-700 text-sm space-y-1">
                    <li>• Logique métier complexe</li>
                    <li>• Gestion des utilisateurs</li>
                    <li>• Messagerie avancée</li>
                    <li>• Statistiques et analyses</li>
                  </ul>
                </div>
              </div>
            </div>
            
            <!-- Error Handling -->
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">⚠️ Gestion des erreurs</h3>
              <div class="bg-red-50 border-l-4 border-red-400 p-4">
                <p class="text-red-700 text-sm mb-3">
                  <strong>Stratégie de fallback :</strong> Si le Backend-NHS est indisponible, le Backend-Client peut servir certaines données en cache ou en mode dégradé.
                </p>
                <ul class="text-red-700 text-sm space-y-1">
                  <li>• Timeout configuré pour les requêtes NHS</li>
                  <li>• Retry automatique avec backoff</li>
                  <li>• Logs détaillés des erreurs</li>
                  <li>• Health checks sur tous les services</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Advantages -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">✨ Avantages de cette architecture</h2>
        <div class="grid md:grid-cols-2 gap-6">
          <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
              <span class="text-green-500 mr-2">📈</span>
              Scalabilité
            </h3>
            <ul class="space-y-2 text-gray-700 text-sm">
              <li>• Séparation claire des responsabilités</li>
              <li>• Possibilité de scaler indépendamment</li>
              <li>• Architecture microservices ready</li>
              <li>• Load balancing facilité</li>
            </ul>
          </div>
          
          <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
              <span class="text-blue-500 mr-2">🔒</span>
              Sécurité
            </h3>
            <ul class="space-y-2 text-gray-700 text-sm">
              <li>• Isolation des fonctionnalités NHS</li>
              <li>• Double couche d'authentification</li>
              <li>• Proxy pour filtrer les requêtes</li>
              <li>• Logs complets sur deux niveaux</li>
            </ul>
          </div>
          
          <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
              <span class="text-purple-500 mr-2">⚡</span>
              Performance
            </h3>
            <ul class="space-y-2 text-gray-700 text-sm">
              <li>• Accès direct DB pour les lectures</li>
              <li>• Cache intelligent au niveau proxy</li>
              <li>• Requêtes optimisées par couche</li>
              <li>• Réduction des latences</li>
            </ul>
          </div>
          
          <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
              <span class="text-orange-500 mr-2">🛠️</span>
              Maintenabilité
            </h3>
            <ul class="space-y-2 text-gray-700 text-sm">
              <li>• Code organisé par domaine</li>
              <li>• Tests isolés par couche</li>
              <li>• Déploiement indépendant</li>
              <li>• Debug facilité par composant</li>
            </ul>
          </div>
        </div>
      </section>

      <!-- Deployment -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🚀 Déploiement</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <div class="grid md:grid-cols-3 gap-6">
            <div class="text-center">
              <div class="bg-blue-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                <span class="text-2xl">🌐</span>
              </div>
              <h3 class="font-semibold text-gray-900 mb-2">Frontend</h3>
              <p class="text-gray-600 text-sm mb-3">
                Déployable sur Vercel, Netlify, ou serveur statique
              </p>
              <code class="bg-gray-100 px-2 py-1 rounded text-xs">npm run build</code>
            </div>
            
            <div class="text-center">
              <div class="bg-green-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                <span class="text-2xl">🔄</span>
              </div>
              <h3 class="font-semibold text-gray-900 mb-2">Backend-Client</h3>
              <p class="text-gray-600 text-sm mb-3">
                Serveur web Apache/Nginx avec PHP-FPM
              </p>
              <code class="bg-gray-100 px-2 py-1 rounded text-xs">composer install --optimize-autoloader</code>
            </div>
            
            <div class="text-center">
              <div class="bg-purple-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                <span class="text-2xl">🧠</span>
              </div>
              <h3 class="font-semibold text-gray-900 mb-2">Backend-NHS</h3>
              <p class="text-gray-600 text-sm mb-3">
                Service isolé avec monitoring avancé
              </p>
              <code class="bg-gray-100 px-2 py-1 rounded text-xs">php artisan serve --host=0.0.0.0</code>
            </div>
          </div>
          
          <div class="mt-8 text-center">
            <NuxtLink to="/documentation/deployment" 
              class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
              </svg>
              Guide de déploiement détaillé
            </NuxtLink>
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
    title: 'Architecture - Documentation Gestion Résidence',
    meta: [
      { name: 'description', content: 'Architecture détaillée de l\'application de gestion d\'entreprise de résidence avec dual backend.' }
    ]
  })
</script>


