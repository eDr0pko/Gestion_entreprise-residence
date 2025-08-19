<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50">
    <DocumentationLayout>
      <!-- Page Header -->
      <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">API Messagerie</h1>
        <p class="text-xl text-gray-600">
          Documentation complète du système de messagerie temps réel avec groupes, conversations privées et notifications.
        </p>
      </div>

      <!-- Overview -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">💬 Vue d'ensemble</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <p class="text-gray-700 text-lg mb-6">
            Le système de messagerie permet aux résidents, gardiens et administrateurs de communiquer via des <strong>groupes thématiques</strong> et des <strong>conversations privées</strong>. Il supporte les messages texte, fichiers et émojis.
          </p>
          
          <div class="grid md:grid-cols-2 gap-8">
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">💫 Fonctionnalités clés</h3>
              <ul class="space-y-2 text-gray-700">
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  Groupes de discussion thématiques
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  Conversations privées 1-à-1
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  Partage de fichiers et images
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  Notifications temps réel
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  Historique des messages
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  Statuts de lecture (lu/non lu)
                </li>
              </ul>
            </div>
            
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">🏷️ Types de groupes</h3>
              <ul class="space-y-2 text-gray-700">
                <li class="flex items-center">
                  <span class="text-blue-500 mr-2">🏢</span>
                  <strong>Général :</strong> Tous les résidents
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">🚨</span>
                  <strong>Urgences :</strong> Alertes importantes
                </li>
                <li class="flex items-center">
                  <span class="text-purple-500 mr-2">🎉</span>
                  <strong>Événements :</strong> Activités et sorties
                </li>
                <li class="flex items-center">
                  <span class="text-orange-500 mr-2">🔧</span>
                  <strong>Maintenance :</strong> Travaux et réparations
                </li>
                <li class="flex items-center">
                  <span class="text-pink-500 mr-2">👥</span>
                  <strong>Privé :</strong> Conversations individuelles
                </li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- Groups API -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">👥 API Groupes</h2>
        
        <div class="space-y-6">
          <!-- Get Groups -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium mr-4">GET</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/messages/groups</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Récupère tous les groupes accessibles à l'utilisateur avec les derniers messages.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">🔍 Paramètres optionnels</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    ?type=general<br>
                    ?active_only=true<br>
                    ?with_messages=true<br>
                    ?limit=20
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4 text-xs">
                  <code class="text-green-400 font-mono">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"groups": [<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;{<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id_groupe": 1,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"nom_groupe": "Général",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"description": "Discussions générales",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type": "general",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"nombre_participants": 24,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"dernier_message": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"contenu": "Bonjour tout le monde !",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"expediteur": "Jean Dupont",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"date_envoi": "2024-01-15T10:30:00Z"<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"messages_non_lus": 3<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                    &nbsp;&nbsp;]<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Create Group -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium mr-4">POST</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/messages/groups</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Crée un nouveau groupe de discussion (administrateurs uniquement).</p>
            
            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6">
              <p class="text-amber-700 text-sm">
                <strong>Permissions :</strong> Seuls les administrateurs peuvent créer de nouveaux groupes.
              </p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Paramètres de requête</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"nom_groupe": "Nouveau Groupe",<br>
                    &nbsp;&nbsp;"description": "Description du groupe",<br>
                    &nbsp;&nbsp;"type": "evenements",<br>
                    &nbsp;&nbsp;"actif": true,<br>
                    &nbsp;&nbsp;"participants": [1, 2, 3]<br>
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
                    &nbsp;&nbsp;"message": "Groupe créé avec succès",<br>
                    &nbsp;&nbsp;"group": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_groupe": 5,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"nom_groupe": "Nouveau Groupe",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"description": "Description du groupe",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"type": "evenements",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"date_creation": "2024-01-15T10:30:00Z"<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Get Group Details -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium mr-4">GET</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/messages/groups/{id}</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Récupère les détails d'un groupe spécifique avec ses participants.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">🔍 Paramètres URL</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    /api/messages/groups/1<br><br>
                    
                    // Paramètres optionnels :<br>
                    ?with_participants=true<br>
                    ?with_recent_messages=true
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4 text-xs">
                  <code class="text-green-400 font-mono">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"group": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_groupe": 1,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"nom_groupe": "Général",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"description": "Discussions générales",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"participants": [<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id_personne": 1,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"nom": "Dupont",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"prenom": "Jean",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"role": "resident",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"actif": true<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;]<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Messages API -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">💬 API Messages</h2>
        
        <div class="space-y-6">
          <!-- Get Messages -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium mr-4">GET</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/messages/groups/{group_id}/messages</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Récupère l'historique des messages d'un groupe avec pagination.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">🔍 Paramètres de requête</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    ?page=1<br>
                    ?limit=50<br>
                    ?before_date=2024-01-15T10:30:00Z<br>
                    ?search=keyword<br>
                    ?user_id=5
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4 text-xs">
                  <code class="text-green-400 font-mono">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"messages": [<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;{<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id_message": 123,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"contenu": "Bonjour tout le monde !",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type_contenu": "texte",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"expediteur": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id_personne": 1,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"nom": "Dupont",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"prenom": "Jean",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"photo_profil": "avatar.jpg"<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"date_envoi": "2024-01-15T10:30:00Z",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"lu": true<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                    &nbsp;&nbsp;],<br>
                    &nbsp;&nbsp;"pagination": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"current_page": 1,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"total_pages": 5,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"total_messages": 247<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Send Message -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium mr-4">POST</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/messages/send</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Envoie un nouveau message dans un groupe ou en privé.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Message texte</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"id_groupe": 1,<br>
                    &nbsp;&nbsp;"contenu": "Bonjour tout le monde !",<br>
                    &nbsp;&nbsp;"type_contenu": "texte"<br>
                    }
                  </code>
                </div>
                
                <h4 class="font-semibold text-gray-900 mb-3 mt-6">📤 Message privé</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"id_destinataire": 5,<br>
                    &nbsp;&nbsp;"contenu": "Message privé",<br>
                    &nbsp;&nbsp;"type_contenu": "texte"<br>
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
                    &nbsp;&nbsp;"message": "Message envoyé",<br>
                    &nbsp;&nbsp;"data": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_message": 124,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"contenu": "Bonjour tout le monde !",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"date_envoi": "2024-01-15T10:35:00Z",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"expediteur": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"nom": "Dupont",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"prenom": "Jean"<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Upload File -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium mr-4">POST</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/messages/upload</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Upload d'un fichier ou d'une image à partager dans un message.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Paramètres multipart</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    Content-Type: multipart/form-data<br><br>
                    
                    file: [fichier]<br>
                    id_groupe: 1<br>
                    // OU<br>
                    id_destinataire: 5<br><br>
                    
                    // Optionnel :<br>
                    description: "Description du fichier"
                  </code>
                </div>
                
                <div class="mt-4 bg-blue-50 border-l-4 border-blue-400 p-4">
                  <p class="text-blue-700 text-sm">
                    <strong>Formats acceptés :</strong> JPG, PNG, GIF, PDF, DOC, DOCX, TXT<br>
                    <strong>Taille max :</strong> 10MB
                  </p>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"message": "Fichier uploadé avec succès",<br>
                    &nbsp;&nbsp;"data": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_message": 125,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"contenu": "document.pdf",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"type_contenu": "fichier",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"url_fichier": "http://localhost:8000/uploads/messages/document.pdf",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"taille_fichier": "1.2MB",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"type_fichier": "application/pdf"<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Private Messages -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">👤 Messages privés</h2>
        
        <div class="space-y-6">
          <!-- Get Private Conversations -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium mr-4">GET</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/messages/private</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Liste toutes les conversations privées de l'utilisateur.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">🔍 Paramètres optionnels</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    ?unread_only=true<br>
                    ?limit=20<br>
                    ?search=nom_utilisateur
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4 text-xs">
                  <code class="text-green-400 font-mono">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"conversations": [<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;{<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"correspondant": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id_personne": 5,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"nom": "Martin",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"prenom": "Sophie",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"photo_profil": "sophie.jpg",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"role": "resident"<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"dernier_message": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"contenu": "Merci pour l'info !",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"date_envoi": "2024-01-15T14:22:00Z",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"lu": false<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"messages_non_lus": 2<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                    &nbsp;&nbsp;]<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Get Private Messages -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium mr-4">GET</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/messages/private/{user_id}</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Récupère l'historique des messages privés avec un utilisateur spécifique.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">🔍 Paramètres URL</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    /api/messages/private/5<br><br>
                    
                    // Paramètres optionnels :<br>
                    ?page=1<br>
                    ?limit=30<br>
                    ?mark_as_read=true
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4 text-xs">
                  <code class="text-green-400 font-mono">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"correspondant": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_personne": 5,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"nom": "Martin",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"prenom": "Sophie"<br>
                    &nbsp;&nbsp;},<br>
                    &nbsp;&nbsp;"messages": [<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;{<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id_message": 201,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"contenu": "Salut ! Comment ça va ?",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"expediteur_id": 1,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"destinataire_id": 5,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"date_envoi": "2024-01-15T14:20:00Z",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"lu": true<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                    &nbsp;&nbsp;]<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Notifications -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🔔 Notifications</h2>
        
        <div class="space-y-6">
          <!-- Get Notifications -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium mr-4">GET</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/messages/notifications</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Récupère les notifications de messages non lus.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">🔍 Paramètres optionnels</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    ?unread_only=true<br>
                    ?limit=50<br>
                    ?type=group|private
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4 text-xs">
                  <code class="text-green-400 font-mono">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"total_non_lus": 7,<br>
                    &nbsp;&nbsp;"notifications": [<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;{<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type": "group_message",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"groupe": "Général",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"expediteur": "Jean Dupont",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"apercu": "Bonjour tout le monde !",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"date": "2024-01-15T10:30:00Z",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"count": 3<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;},<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;{<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"type": "private_message",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"expediteur": "Sophie Martin",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"apercu": "Merci pour l'info !",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"date": "2024-01-15T14:22:00Z",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"count": 2<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                    &nbsp;&nbsp;]<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Mark as Read -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium mr-4">PUT</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/messages/mark-read</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Marque des messages comme lus.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Paramètres de requête</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    // Marquer un message spécifique<br>
                    {<br>
                    &nbsp;&nbsp;"message_id": 123<br>
                    }<br><br>
                    
                    // Marquer tous les messages d'un groupe<br>
                    {<br>
                    &nbsp;&nbsp;"group_id": 1<br>
                    }<br><br>
                    
                    // Marquer une conversation privée<br>
                    {<br>
                    &nbsp;&nbsp;"user_id": 5<br>
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
                    &nbsp;&nbsp;"message": "Messages marqués comme lus",<br>
                    &nbsp;&nbsp;"updated_count": 3<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Search and Filters -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🔍 Recherche et filtres</h2>
        
        <div class="space-y-6">
          <!-- Search Messages -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium mr-4">GET</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/messages/search</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Recherche dans l'historique des messages avec filtres avancés.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">🔍 Paramètres de recherche</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    ?q=keyword<br>
                    ?group_id=1<br>
                    ?user_id=5<br>
                    ?type=texte|fichier|image<br>
                    ?date_from=2024-01-01<br>
                    ?date_to=2024-01-31<br>
                    ?limit=20<br>
                    ?page=1
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4 text-xs">
                  <code class="text-green-400 font-mono">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"total_results": 15,<br>
                    &nbsp;&nbsp;"messages": [<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;{<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id_message": 123,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"contenu": "Message trouvé avec keyword",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"context": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"groupe": "Général",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"expediteur": "Jean Dupont",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"date": "2024-01-15T10:30:00Z"<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"highlight": "Message trouvé avec &lt;mark&gt;keyword&lt;/mark&gt;"<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                    &nbsp;&nbsp;]<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Get Message Statistics -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium mr-4">GET</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/messages/stats</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Statistiques d'utilisation de la messagerie pour l'utilisateur.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">🔍 Paramètres optionnels</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    ?period=day|week|month|year<br>
                    ?group_id=1<br>
                    ?start_date=2024-01-01<br>
                    ?end_date=2024-01-31
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4 text-xs">
                  <code class="text-green-400 font-mono">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"stats": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"messages_envoyes": 42,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"messages_recus": 156,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"groupes_actifs": 5,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"conversations_privees": 8,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"fichiers_partages": 12,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"taux_reponse": "85%",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"derniere_activite": "2024-01-15T14:22:00Z"<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- WebSocket Events -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">⚡ Événements temps réel</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <p class="text-gray-700 mb-6">
            Le système utilise WebSocket pour les notifications temps réel. Voici les événements disponibles :
          </p>
          
          <div class="grid md:grid-cols-2 gap-8">
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">📡 Événements entrants</h3>
              <div class="space-y-3">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                  <div class="font-medium text-blue-800">message.new</div>
                  <div class="text-blue-600 text-sm">Nouveau message reçu</div>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                  <div class="font-medium text-green-800">message.read</div>
                  <div class="text-green-600 text-sm">Message marqué comme lu</div>
                </div>
                
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                  <div class="font-medium text-purple-800">user.typing</div>
                  <div class="text-purple-600 text-sm">Utilisateur en train d'écrire</div>
                </div>
                
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                  <div class="font-medium text-orange-800">user.online</div>
                  <div class="text-orange-600 text-sm">Statut en ligne/hors ligne</div>
                </div>
              </div>
            </div>
            
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">📤 Événements sortants</h3>
              <div class="space-y-3">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                  <div class="font-medium text-gray-800">join.group</div>
                  <div class="text-gray-600 text-sm">Rejoindre un groupe</div>
                </div>
                
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                  <div class="font-medium text-gray-800">leave.group</div>
                  <div class="text-gray-600 text-sm">Quitter un groupe</div>
                </div>
                
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                  <div class="font-medium text-gray-800">typing.start</div>
                  <div class="text-gray-600 text-sm">Commencer à écrire</div>
                </div>
                
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                  <div class="font-medium text-gray-800">typing.stop</div>
                  <div class="text-gray-600 text-sm">Arrêter d'écrire</div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="mt-8">
            <h3 class="font-semibold text-gray-900 mb-4">🔧 Exemple d'utilisation WebSocket</h3>
            <div class="bg-gray-900 rounded-lg p-4">
              <code class="text-yellow-400 font-mono text-sm">
                // Connexion WebSocket<br>
                const socket = new WebSocket('ws://localhost:6001/app/your-app-key');<br><br>
                
                // Écouter les nouveaux messages<br>
                socket.onmessage = (event) => {<br>
                &nbsp;&nbsp;const data = JSON.parse(event.data);<br>
                &nbsp;&nbsp;if (data.event === 'message.new') {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;displayNewMessage(data.data);<br>
                &nbsp;&nbsp;}<br>
                };<br><br>
                
                // Envoyer un événement de frappe<br>
                socket.send(JSON.stringify({<br>
                &nbsp;&nbsp;event: 'typing.start',<br>
                &nbsp;&nbsp;data: { group_id: 1 }<br>
                }));
              </code>
            </div>
          </div>
        </div>
      </section>

      <!-- Error Handling -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">⚠️ Gestion d'erreurs</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <div class="grid md:grid-cols-2 gap-8">
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">🚫 Erreurs communes</h3>
              <div class="space-y-3">
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                  <div class="font-medium text-red-800">403 - Accès refusé</div>
                  <div class="text-red-600 text-sm">Pas autorisé à accéder au groupe</div>
                </div>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                  <div class="font-medium text-red-800">404 - Non trouvé</div>
                  <div class="text-red-600 text-sm">Groupe ou message inexistant</div>
                </div>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                  <div class="font-medium text-red-800">413 - Fichier trop volumineux</div>
                  <div class="text-red-600 text-sm">Upload dépassant 10MB</div>
                </div>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                  <div class="font-medium text-red-800">429 - Trop de requêtes</div>
                  <div class="text-red-600 text-sm">Limite de débit dépassée</div>
                </div>
              </div>
            </div>
            
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">📋 Format d'erreur</h3>
              <div class="bg-gray-900 rounded-lg p-4">
                <code class="text-red-400 font-mono text-sm">
                  {<br>
                  &nbsp;&nbsp;"success": false,<br>
                  &nbsp;&nbsp;"error": {<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;"code": "UNAUTHORIZED_GROUP_ACCESS",<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;"message": "Vous n'êtes pas autorisé à accéder à ce groupe",<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;"details": {<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"group_id": 1,<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"required_role": "resident"<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                  &nbsp;&nbsp;}<br>
                  }
                </code>
              </div>
              
              <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <p class="text-yellow-700 text-sm">
                  <strong>Conseil :</strong> Vérifiez toujours les permissions avant d'accéder aux groupes et implémentez une gestion d'erreur appropriée.
                </p>
              </div>
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
    title: 'API Messagerie - Documentation Gestion Résidence',
    meta: [
      { name: 'description', content: 'Documentation complète de l\'API de messagerie temps réel pour l\'application de gestion de résidence.' }
    ]
  })
</script>


