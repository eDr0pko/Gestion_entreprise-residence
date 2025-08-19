<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50">
    <DocumentationLayout>
      <!-- Page Header -->
      <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Installation</h1>
        <p class="text-xl text-gray-600">
          Guide complet pour installer et configurer l'application de gestion d'entreprise de résidence.
        </p>
      </div>

      <!-- Prerequisites -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">📋 Prérequis</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <div class="grid md:grid-cols-2 gap-8">
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">🖥️ Système requis</h3>
              <ul class="space-y-2 text-gray-700">
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  <strong>OS :</strong> Windows 10+, macOS 10.15+, Linux Ubuntu 18+
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  <strong>RAM :</strong> 4 GB minimum, 8 GB recommandé
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  <strong>Stockage :</strong> 2 GB d'espace libre
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  <strong>Réseau :</strong> Connexion internet stable
                </li>
              </ul>
            </div>
            
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">🛠️ Logiciels requis</h3>
              <ul class="space-y-2 text-gray-700">
                <li class="flex items-center">
                  <span class="text-blue-500 mr-2">•</span>
                  <strong>Node.js :</strong> v18.0.0 ou supérieur
                </li>
                <li class="flex items-center">
                  <span class="text-blue-500 mr-2">•</span>
                  <strong>PHP :</strong> v8.1 ou supérieur
                </li>
                <li class="flex items-center">
                  <span class="text-blue-500 mr-2">•</span>
                  <strong>Composer :</strong> v2.0 ou supérieur
                </li>
                <li class="flex items-center">
                  <span class="text-blue-500 mr-2">•</span>
                  <strong>MySQL :</strong> v8.0 ou supérieur
                </li>
                <li class="flex items-center">
                  <span class="text-blue-500 mr-2">•</span>
                  <strong>Git :</strong> Dernière version
                </li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- Quick Installation -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">⚡ Installation rapide</h2>
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-8 mb-8">
          <p class="text-gray-700 mb-6">
            Pour les utilisateurs pressés, utilisez le script d'installation automatique :
          </p>
          
          <div class="bg-gray-900 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between mb-2">
              <span class="text-green-400 text-sm font-mono">Windows PowerShell</span>
              <button class="text-gray-400 hover:text-white text-sm">Copier</button>
            </div>
            <code class="text-green-400 font-mono text-sm">
              git clone [repository-url] gestion-residence<br>
              cd gestion-residence<br>
              .\start-architecture.ps1
            </code>
          </div>
          
          <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
              <svg class="w-5 h-5 text-yellow-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
              </svg>
              <p class="text-yellow-700 text-sm">
                <strong>Note :</strong> Cette méthode lance automatiquement les trois serveurs (Frontend, Backend-Client, Backend-NHS). Continuez avec l'installation manuelle pour plus de contrôle.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Detailed Installation -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🔧 Installation détaillée</h2>
        
        <!-- Step 1: Clone -->
        <div class="space-y-8">
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-6">
              <span class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
              <h3 class="ml-4 text-xl font-semibold text-gray-900">Cloner le repository</h3>
            </div>
            
            <p class="text-gray-700 mb-4">Téléchargez le code source depuis le repository Git :</p>
            
            <div class="bg-gray-900 rounded-lg p-4 mb-4">
              <code class="text-green-400 font-mono text-sm">
                git clone [repository-url] gestion-residence<br>
                cd gestion-residence
              </code>
            </div>
            
            <p class="text-sm text-gray-600">
              Cela créera un dossier <code class="bg-gray-100 px-2 py-1 rounded text-gray-800">gestion-residence</code> avec tous les fichiers nécessaires.
            </p>
          </div>

          <!-- Step 2: Database -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-6">
              <span class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
              <h3 class="ml-4 text-xl font-semibold text-gray-900">Configuration de la base de données</h3>
            </div>
            
            <div class="space-y-4">
              <div>
                <h4 class="font-semibold text-gray-900 mb-2">Créer la base de données :</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    mysql -u root -p<br>
                    mysql> source final.sql;<br>
                    mysql> exit;
                  </code>
                </div>
              </div>
              
              <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                <p class="text-blue-700 text-sm">
                  <strong>Astuce :</strong> Le fichier <code>final.sql</code> contient la structure complète de la base avec des données de test pour démarrer rapidement.
                </p>
              </div>
            </div>
          </div>

          <!-- Step 3: Backend Client -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-6">
              <span class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">3</span>
              <h3 class="ml-4 text-xl font-semibold text-gray-900">Configuration Backend-Client (Port 8000)</h3>
            </div>
            
            <div class="space-y-4">
              <div>
                <h4 class="font-semibold text-gray-900 mb-2">Installation des dépendances :</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    cd backend-client<br>
                    composer install<br>
                    cp .env.example .env<br>
                    php artisan key:generate
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-2">Configuration .env :</h4>
                <div class="bg-gray-100 rounded-lg p-4">
                  <code class="text-gray-800 font-mono text-sm">
                    DB_CONNECTION=mysql<br>
                    DB_HOST=127.0.0.1<br>
                    DB_PORT=3306<br>
                    DB_DATABASE=gestion_entreprise_residence<br>
                    DB_USERNAME=root<br>
                    DB_PASSWORD=votre_mot_de_passe<br><br>
                    
                    # URL du Backend-NHS<br>
                    NHS_BACKEND_URL=http://localhost:8001
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-2">Lancer le serveur :</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    php artisan serve --port=8000
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 4: Backend NHS -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-6">
              <span class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">4</span>
              <h3 class="ml-4 text-xl font-semibold text-gray-900">Configuration Backend-NHS (Port 8001)</h3>
            </div>
            
            <div class="space-y-4">
              <div>
                <h4 class="font-semibold text-gray-900 mb-2">Installation des dépendances :</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    cd ../backend-nhs<br>
                    composer install<br>
                    cp .env.example .env<br>
                    php artisan key:generate
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-2">Configuration .env :</h4>
                <div class="bg-gray-100 rounded-lg p-4">
                  <code class="text-gray-800 font-mono text-sm">
                    DB_CONNECTION=mysql<br>
                    DB_HOST=127.0.0.1<br>
                    DB_PORT=3306<br>
                    DB_DATABASE=gestion_entreprise_residence<br>
                    DB_USERNAME=root<br>
                    DB_PASSWORD=votre_mot_de_passe
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-2">Lancer le serveur :</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    php artisan serve --port=8001
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 5: Frontend -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-6">
              <span class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">5</span>
              <h3 class="ml-4 text-xl font-semibold text-gray-900">Configuration Frontend (Port 3000)</h3>
            </div>
            
            <div class="space-y-4">
              <div>
                <h4 class="font-semibold text-gray-900 mb-2">Installation des dépendances :</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    cd ../frontend<br>
                    npm install
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-2">Configuration .env :</h4>
                <div class="bg-gray-100 rounded-lg p-4">
                  <code class="text-gray-800 font-mono text-sm">
                    # URL de l'API Backend-Client<br>
                    NUXT_PUBLIC_API_BASE=http://localhost:8000/api
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-2">Lancer le serveur de développement :</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    npm run dev
                  </code>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Verification -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">✅ Vérification de l'installation</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <p class="text-gray-700 mb-6">
            Une fois tous les serveurs lancés, vérifiez que l'installation fonctionne correctement :
          </p>
          
          <div class="grid md:grid-cols-3 gap-6 mb-6">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
              <h3 class="font-semibold text-blue-800 mb-2">Frontend</h3>
              <a href="http://localhost:3000" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm">
                http://localhost:3000
              </a>
            </div>
            
            <div class="text-center p-4 bg-green-50 rounded-lg">
              <h3 class="font-semibold text-green-800 mb-2">Backend-Client</h3>
              <a href="http://localhost:8000/api/health" target="_blank" class="text-green-600 hover:text-green-700 text-sm">
                http://localhost:8000/api/health
              </a>
            </div>
            
            <div class="text-center p-4 bg-purple-50 rounded-lg">
              <h3 class="font-semibold text-purple-800 mb-2">Backend-NHS</h3>
              <a href="http://localhost:8001/api/health" target="_blank" class="text-purple-600 hover:text-purple-700 text-sm">
                http://localhost:8001/api/health
              </a>
            </div>
          </div>
          
          <div class="bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
              <svg class="w-5 h-5 text-green-400 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <p class="text-green-700 text-sm">
                <strong>Succès !</strong> Si toutes les URLs répondent correctement, votre installation est prête. Vous pouvez maintenant vous connecter avec les comptes de test.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Test Accounts -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">👤 Comptes de test</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <p class="text-gray-700 mb-6">
            L'installation inclut des comptes de test pour chaque rôle :
          </p>
          
          <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
              <h3 class="font-semibold text-red-800 mb-3">👑 Administrateur</h3>
              <div class="text-sm text-red-700 space-y-1">
                <p><strong>Email :</strong> admin@residence.com</p>
                <p><strong>Mot de passe :</strong> password</p>
                <p class="text-xs text-red-600 mt-2">Accès complet à toutes les fonctionnalités</p>
              </div>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
              <h3 class="font-semibold text-blue-800 mb-3">🔑 Gardien</h3>
              <div class="text-sm text-blue-700 space-y-1">
                <p><strong>Email :</strong> gardien@residence.com</p>
                <p><strong>Mot de passe :</strong> password</p>
                <p class="text-xs text-blue-600 mt-2">Gestion des visites et incidents</p>
              </div>
            </div>
            
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
              <h3 class="font-semibold text-green-800 mb-3">🏠 Résident</h3>
              <div class="text-sm text-green-700 space-y-1">
                <p><strong>Email :</strong> marie.durand@residence.com</p>
                <p><strong>Mot de passe :</strong> password</p>
                <p class="text-xs text-green-600 mt-2">Planification et messagerie</p>
              </div>
            </div>
            
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
              <h3 class="font-semibold text-purple-800 mb-3">👤 Invité</h3>
              <div class="text-sm text-purple-700 space-y-1">
                <p><strong>Email :</strong> invite@example.com</p>
                <p><strong>Mot de passe :</strong> password</p>
                <p class="text-xs text-purple-600 mt-2">Accès limité aux visites</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Troubleshooting -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🔧 Résolution de problèmes</h2>
        <div class="space-y-6">
          <!-- Database Error -->
          <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-3">❌ Erreur de connexion à la base de données</h3>
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
              <p class="text-red-700 text-sm">
                <strong>Erreur :</strong> "SQLSTATE[HY000] [2002] Connection refused"
              </p>
            </div>
            <div class="space-y-2 text-gray-700 text-sm">
              <p><strong>Solutions :</strong></p>
              <ul class="list-disc list-inside space-y-1 ml-4">
                <li>Vérifiez que MySQL est démarré</li>
                <li>Vérifiez les informations de connexion dans le fichier <code>.env</code></li>
                <li>Assurez-vous que la base de données existe</li>
                <li>Testez la connexion : <code>mysql -u root -p</code></li>
              </ul>
            </div>
          </div>

          <!-- Port Error -->
          <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-3">🚪 Erreur de port occupé</h3>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
              <p class="text-yellow-700 text-sm">
                <strong>Erreur :</strong> "Port 8000 is already in use"
              </p>
            </div>
            <div class="space-y-2 text-gray-700 text-sm">
              <p><strong>Solutions :</strong></p>
              <ul class="list-disc list-inside space-y-1 ml-4">
                <li>Arrêtez les processus utilisant le port : <code>netstat -ano | findstr :8000</code></li>
                <li>Utilisez un port différent : <code>php artisan serve --port=8002</code></li>
                <li>Redémarrez votre ordinateur pour libérer tous les ports</li>
              </ul>
            </div>
          </div>

          <!-- Dependencies Error -->
          <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200">
            <h3 class="font-semibold text-gray-900 mb-3">📦 Erreur de dépendances</h3>
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
              <p class="text-blue-700 text-sm">
                <strong>Erreur :</strong> Erreurs lors de l'installation des packages
              </p>
            </div>
            <div class="space-y-2 text-gray-700 text-sm">
              <p><strong>Solutions :</strong></p>
              <ul class="list-disc list-inside space-y-1 ml-4">
                <li>Videz le cache : <code>composer clear-cache</code> et <code>npm cache clean --force</code></li>
                <li>Supprimez les dossiers de cache et réinstallez</li>
                <li>Vérifiez votre connexion internet</li>
                <li>Utilisez <code>composer install --no-dev</code> pour la production</li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- Next Steps -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🚀 Prochaines étapes</h2>
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-8">
          <p class="text-gray-700 mb-6">
            Félicitations ! Votre installation est terminée. Voici ce que vous pouvez faire maintenant :
          </p>
          
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <h3 class="font-semibold text-gray-900 mb-3">📚 Découvrir l'application</h3>
              <ul class="space-y-2 text-gray-700 text-sm">
                <li>
                  <NuxtLink to="/documentation/gestion-utilisateurs" class="text-blue-600 hover:text-blue-700">
                    → Guide de gestion des utilisateurs
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink to="/documentation/messagerie" class="text-blue-600 hover:text-blue-700">
                    → Utiliser la messagerie
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink to="/documentation/planification-visites" class="text-blue-600 hover:text-blue-700">
                    → Planifier des visites
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink to="/documentation/administration" class="text-blue-600 hover:text-blue-700">
                    → Panneau d'administration
                  </NuxtLink>
                </li>
              </ul>
            </div>
            
            <div>
              <h3 class="font-semibold text-gray-900 mb-3">⚙️ Configuration avancée</h3>
              <ul class="space-y-2 text-gray-700 text-sm">
                <li>
                  <NuxtLink to="/documentation/architecture" class="text-blue-600 hover:text-blue-700">
                    → Comprendre l'architecture
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink to="/documentation/api-auth" class="text-blue-600 hover:text-blue-700">
                    → Documentation API
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink to="/documentation/deployment" class="text-blue-600 hover:text-blue-700">
                    → Déploiement en production
                  </NuxtLink>
                </li>
                <li>
                  <NuxtLink to="/documentation/base-donnees" class="text-blue-600 hover:text-blue-700">
                    → Structure de la base de données
                  </NuxtLink>
                </li>
              </ul>
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
    title: 'Installation - Documentation Gestion Résidence',
    meta: [
      { name: 'description', content: 'Guide complet d\'installation de l\'application de gestion d\'entreprise de résidence.' }
    ]
  })
</script>


