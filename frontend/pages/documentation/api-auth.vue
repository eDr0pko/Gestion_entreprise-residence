<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50">
    <DocumentationLayout>
      <!-- Page Header -->
      <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">API Authentification</h1>
        <p class="text-xl text-gray-600">
          Documentation complète de l'API d'authentification avec Laravel Sanctum et gestion multi-rôles.
        </p>
      </div>

      <!-- Overview -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🔐 Vue d'ensemble</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <p class="text-gray-700 text-lg mb-6">
            L'API d'authentification utilise <strong>Laravel Sanctum</strong> pour fournir une authentification sécurisée basée sur des tokens. Elle supporte plusieurs types d'utilisateurs avec des rôles spécifiques.
          </p>
          
          <div class="grid md:grid-cols-2 gap-8">
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">🔑 Fonctionnalités clés</h3>
              <ul class="space-y-2 text-gray-700">
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  Authentification par token JWT
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  Gestion multi-rôles (Admin, Gardien, Résident, Invité)
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  Gestion des profils utilisateurs
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  Upload et gestion d'avatars
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  Système de ban/débannissement
                </li>
              </ul>
            </div>
            
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">📋 Types d'utilisateurs</h3>
              <ul class="space-y-2 text-gray-700">
                <li class="flex items-center">
                  <span class="text-red-500 mr-2">👑</span>
                  <strong>Administrateur :</strong> Accès complet
                </li>
                <li class="flex items-center">
                  <span class="text-blue-500 mr-2">🔑</span>
                  <strong>Gardien :</strong> Gestion des visites et sécurité
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">🏠</span>
                  <strong>Résident :</strong> Planification et messagerie
                </li>
                <li class="flex items-center">
                  <span class="text-purple-500 mr-2">👤</span>
                  <strong>Invité :</strong> Accès temporaire limité
                </li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- Authentication Endpoints -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🚪 Endpoints d'authentification</h2>
        
        <!-- Login -->
        <div class="space-y-6">
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium mr-4">POST</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/login</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Authentifie un utilisateur et retourne un token d'accès.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Paramètres de requête</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"email": "admin@residence.com",<br>
                    &nbsp;&nbsp;"password": "password"<br>
                    }
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"token": "1|abc123...",<br>
                    &nbsp;&nbsp;"user": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_personne": 1,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"email": "admin@residence.com",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"nom": "Admin",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"prenom": "Super",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"role": "admin"<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
            
            <div class="mt-6">
              <h4 class="font-semibold text-gray-900 mb-3">🔧 Exemple d'utilisation</h4>
              <div class="bg-gray-900 rounded-lg p-4">
                <code class="text-yellow-400 font-mono text-sm">
                  fetch('http://localhost:8000/api/login', {<br>
                  &nbsp;&nbsp;method: 'POST',<br>
                  &nbsp;&nbsp;headers: {<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;'Content-Type': 'application/json',<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;'Accept': 'application/json'<br>
                  &nbsp;&nbsp;},<br>
                  &nbsp;&nbsp;body: JSON.stringify({<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;email: 'admin@residence.com',<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;password: 'password'<br>
                  &nbsp;&nbsp;})<br>
                  })
                </code>
              </div>
            </div>
          </div>

          <!-- Logout -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium mr-4">POST</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/logout</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Déconnecte l'utilisateur et révoque le token d'accès.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">🔑 Headers requis</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    Authorization: Bearer 1|abc123...<br>
                    Accept: application/json
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"message": "Déconnexion réussie"<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Current User -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium mr-4">GET</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/user</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Récupère les informations de l'utilisateur authentifié.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">🔑 Headers requis</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    Authorization: Bearer 1|abc123...<br>
                    Accept: application/json
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"id_personne": 1,<br>
                    &nbsp;&nbsp;"email": "admin@residence.com",<br>
                    &nbsp;&nbsp;"nom": "Admin",<br>
                    &nbsp;&nbsp;"prenom": "Super",<br>
                    &nbsp;&nbsp;"numero_telephone": "+33123456789",<br>
                    &nbsp;&nbsp;"photo_profil": "avatar.jpg",<br>
                    &nbsp;&nbsp;"role": "admin",<br>
                    &nbsp;&nbsp;"niveau_acces": "super_admin"<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Guest Authentication -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">👤 Authentification des invités</h2>
        
        <div class="space-y-6">
          <!-- Guest Register -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium mr-4">POST</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/guests/register</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Inscription d'un nouvel invité temporaire.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Paramètres de requête</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"nom": "Dupont",<br>
                    &nbsp;&nbsp;"prenom": "Jean",<br>
                    &nbsp;&nbsp;"email": "jean.dupont@example.com",<br>
                    &nbsp;&nbsp;"numero_telephone": "+33987654321",<br>
                    &nbsp;&nbsp;"password": "motdepasse123",<br>
                    &nbsp;&nbsp;"commentaire": "Invité pour visite médicale"<br>
                    }
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"message": "Invité créé avec succès",<br>
                    &nbsp;&nbsp;"user": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_personne": 25,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"email": "jean.dupont@example.com",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"nom": "Dupont",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"prenom": "Jean",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"role": "invite"<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Guest Login -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium mr-4">POST</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/guests/login</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Connexion d'un invité avec vérification du statut actif.</p>
            
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
              <p class="text-yellow-700 text-sm">
                <strong>Note :</strong> Les invités doivent être actifs et non expirés pour pouvoir se connecter.
              </p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Paramètres de requête</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"email": "jean.dupont@example.com",<br>
                    &nbsp;&nbsp;"password": "motdepasse123"<br>
                    }
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"token": "2|def456...",<br>
                    &nbsp;&nbsp;"user": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_personne": 25,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"email": "jean.dupont@example.com",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"nom": "Dupont",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"prenom": "Jean",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"role": "invite",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"actif": true<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Profile Management -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">👤 Gestion des profils</h2>
        
        <div class="space-y-6">
          <!-- Get Profile -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium mr-4">GET</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/profile</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Récupère le profil complet de l'utilisateur avec statistiques.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4 text-xs">
                  <code class="text-green-400 font-mono">
                    {<br>
                    &nbsp;&nbsp;"user": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_personne": 1,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"email": "admin@residence.com",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"nom": "Admin",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"prenom": "Super",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"numero_telephone": "+33123456789",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"photo_profil": "avatar.jpg"<br>
                    &nbsp;&nbsp;},<br>
                    &nbsp;&nbsp;"stats": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"messages_envoyes": 42,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"groupes_participes": 5,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"visites_total": 8,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"incidents_signales": 2<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📊 Statistiques incluses</h4>
                <ul class="space-y-2 text-gray-700 text-sm">
                  <li>• <strong>Messages envoyés :</strong> Nombre total de messages</li>
                  <li>• <strong>Groupes participés :</strong> Conversations actives</li>
                  <li>• <strong>Visites total :</strong> Visites planifiées/effectuées</li>
                  <li>• <strong>Incidents signalés :</strong> Incidents reportés</li>
                  <li>• <strong>Date dernière connexion :</strong> Timestamp</li>
                  <li>• <strong>Taux de participation :</strong> Activité relative</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Update Profile -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium mr-4">PUT</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/profile/update</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Met à jour les informations du profil utilisateur.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Paramètres de requête</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"nom": "Nouveau Nom",<br>
                    &nbsp;&nbsp;"prenom": "Nouveau Prénom",<br>
                    &nbsp;&nbsp;"numero_telephone": "+33111222333",<br>
                    &nbsp;&nbsp;"adresse": "123 Rue Exemple",<br>
                    &nbsp;&nbsp;"date_naissance": "1990-01-01"<br>
                    }
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"message": "Profil mis à jour",<br>
                    &nbsp;&nbsp;"user": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;// Données mises à jour<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Avatar Upload -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium mr-4">POST</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/profile/avatar</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Upload d'un avatar pour l'utilisateur.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Paramètres multipart</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    Content-Type: multipart/form-data<br><br>
                    
                    avatar: [fichier image]<br>
                    // Formats acceptés: jpg, jpeg, png, gif<br>
                    // Taille max: 2MB
                  </code>
                </div>
                
                <div class="mt-4 bg-blue-50 border-l-4 border-blue-400 p-4">
                  <p class="text-blue-700 text-sm">
                    <strong>Contraintes :</strong> JPG/PNG/GIF uniquement, maximum 2MB, dimensions recommandées 300x300px.
                  </p>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"message": "Avatar mis à jour",<br>
                    &nbsp;&nbsp;"avatar_url": "http://localhost:8000/avatars/user_1_avatar.jpg"<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Password Management -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🔐 Gestion des mots de passe</h2>
        
        <div class="space-y-6">
          <!-- Verify Password -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-medium mr-4">POST</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/profile/verify-password</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Vérifie le mot de passe actuel avant modification.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Paramètres de requête</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"current_password": "ancienMotDePasse123"<br>
                    }
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"message": "Mot de passe vérifié"<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Update Password -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium mr-4">PUT</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/profile/password</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Change le mot de passe de l'utilisateur authentifié.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Paramètres de requête</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"current_password": "ancienMotDePasse123",<br>
                    &nbsp;&nbsp;"password": "nouveauMotDePasse456",<br>
                    &nbsp;&nbsp;"password_confirmation": "nouveauMotDePasse456"<br>
                    }
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"message": "Mot de passe mis à jour avec succès"<br>
                    }
                  </code>
                </div>
              </div>
            </div>
            
            <div class="mt-6 bg-red-50 border-l-4 border-red-400 p-4">
              <p class="text-red-700 text-sm">
                <strong>Sécurité :</strong> Le changement de mot de passe révoque automatiquement tous les tokens existants sauf celui utilisé pour la requête.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Error Codes -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">⚠️ Codes d'erreur</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <div class="grid md:grid-cols-2 gap-8">
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">🔒 Erreurs d'authentification</h3>
              <div class="space-y-3">
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                  <div class="font-medium text-red-800">401 - Non autorisé</div>
                  <div class="text-red-600 text-sm">Token manquant ou invalide</div>
                </div>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                  <div class="font-medium text-red-800">422 - Données invalides</div>
                  <div class="text-red-600 text-sm">Email ou mot de passe incorrect</div>
                </div>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                  <div class="font-medium text-red-800">403 - Accès interdit</div>
                  <div class="text-red-600 text-sm">Utilisateur banni ou inactif</div>
                </div>
              </div>
            </div>
            
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">⚡ Erreurs de validation</h3>
              <div class="space-y-3">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                  <div class="font-medium text-yellow-800">422 - Email requis</div>
                  <div class="text-yellow-600 text-sm">Le champ email est obligatoire</div>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                  <div class="font-medium text-yellow-800">422 - Format email</div>
                  <div class="text-yellow-600 text-sm">L'email doit être valide</div>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                  <div class="font-medium text-yellow-800">422 - Mot de passe</div>
                  <div class="text-yellow-600 text-sm">Minimum 8 caractères requis</div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="mt-8">
            <h3 class="font-semibold text-gray-900 mb-4">📋 Format d'erreur standard</h3>
            <div class="bg-gray-900 rounded-lg p-4">
              <code class="text-red-400 font-mono text-sm">
                {<br>
                &nbsp;&nbsp;"success": false,<br>
                &nbsp;&nbsp;"message": "Erreur d'authentification",<br>
                &nbsp;&nbsp;"errors": {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"email": ["Le champ email est obligatoire"],<br>
                &nbsp;&nbsp;&nbsp;&nbsp;"password": ["Le mot de passe doit contenir au moins 8 caractères"]<br>
                &nbsp;&nbsp;}<br>
                }
              </code>
            </div>
          </div>
        </div>
      </section>

      <!-- Rate Limiting -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🚦 Limitation de débit</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <div class="grid md:grid-cols-2 gap-8">
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">📊 Limites par endpoint</h3>
              <ul class="space-y-2 text-gray-700">
                <li class="flex justify-between">
                  <span>Login :</span>
                  <span class="font-medium">5 tentatives / 15 min</span>
                </li>
                <li class="flex justify-between">
                  <span>Profile :</span>
                  <span class="font-medium">60 requêtes / min</span>
                </li>
                <li class="flex justify-between">
                  <span>Avatar upload :</span>
                  <span class="font-medium">3 uploads / 10 min</span>
                </li>
                <li class="flex justify-between">
                  <span>Password change :</span>
                  <span class="font-medium">2 changements / heure</span>
                </li>
              </ul>
            </div>
            
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">🔄 Headers de réponse</h3>
              <div class="bg-gray-900 rounded-lg p-4">
                <code class="text-green-400 font-mono text-sm">
                  X-RateLimit-Limit: 60<br>
                  X-RateLimit-Remaining: 57<br>
                  X-RateLimit-Reset: 1693123456<br>
                  Retry-After: 60
                </code>
              </div>
              
              <div class="mt-4 bg-blue-50 border-l-4 border-blue-400 p-4">
                <p class="text-blue-700 text-sm">
                  <strong>Note :</strong> Quand la limite est atteinte, l'API retourne un code 429 avec l'en-tête Retry-After.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Integration Examples -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🔧 Exemples d'intégration</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <!-- JavaScript Example -->
          <div class="mb-8">
            <h3 class="font-semibold text-gray-900 mb-4">📘 Exemple JavaScript (Frontend)</h3>
            <div class="bg-gray-900 rounded-lg p-4">
              <code class="text-yellow-400 font-mono text-sm">
                // Service d'authentification<br>
                class AuthService {<br>
                &nbsp;&nbsp;constructor() {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;this.baseURL = 'http://localhost:8000/api'<br>
                &nbsp;&nbsp;&nbsp;&nbsp;this.token = localStorage.getItem('auth_token')<br>
                &nbsp;&nbsp;}<br><br>
                
                &nbsp;&nbsp;async login(email, password) {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;const response = await fetch(`${this.baseURL}/login`, {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;method: 'POST',<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;headers: { 'Content-Type': 'application/json' },<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;body: JSON.stringify({ email, password })<br>
                &nbsp;&nbsp;&nbsp;&nbsp;})<br><br>
                
                &nbsp;&nbsp;&nbsp;&nbsp;const data = await response.json()<br>
                &nbsp;&nbsp;&nbsp;&nbsp;if (data.success) {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;this.token = data.token<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;localStorage.setItem('auth_token', this.token)<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;return data.user<br>
                &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                &nbsp;&nbsp;&nbsp;&nbsp;throw new Error(data.message)<br>
                &nbsp;&nbsp;}<br>
                }
              </code>
            </div>
          </div>
          
          <!-- PHP Example -->
          <div>
            <h3 class="font-semibold text-gray-900 mb-4">🐘 Exemple PHP (Backend)</h3>
            <div class="bg-gray-900 rounded-lg p-4">
              <code class="text-green-400 font-mono text-sm">
                // Middleware d'authentification personnalisé<br>
                public function handle($request, Closure $next) {<br>
                &nbsp;&nbsp;$token = $request->bearerToken();<br><br>
                
                &nbsp;&nbsp;if (!$token) {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;return response()->json(['error' => 'Token manquant'], 401);<br>
                &nbsp;&nbsp;}<br><br>
                
                &nbsp;&nbsp;$user = PersonalAccessToken::findToken($token)?->tokenable;<br><br>
                
                &nbsp;&nbsp;if (!$user || !$user->isActive()) {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;return response()->json(['error' => 'Token invalide'], 401);<br>
                &nbsp;&nbsp;}<br><br>
                
                &nbsp;&nbsp;Auth::setUser($user);<br>
                &nbsp;&nbsp;return $next($request);<br>
                }
              </code>
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
    title: 'API Authentification - Documentation Gestion Résidence',
    meta: [
      { name: 'description', content: 'Documentation complète de l\'API d\'authentification avec Laravel Sanctum pour l\'application de gestion de résidence.' }
    ]
  })
</script>


