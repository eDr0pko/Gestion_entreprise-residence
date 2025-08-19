<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50">
    <DocumentationLayout>
      <!-- Page Header -->
      <div class="mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">API Visites</h1>
        <p class="text-xl text-gray-600">
          Documentation complète du système de gestion des visites avec planification, validation et contrôle d'accès.
        </p>
      </div>

      <!-- Overview -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🏠 Vue d'ensemble</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <p class="text-gray-700 text-lg mb-6">
            Le système de visites permet aux résidents de <strong>planifier des visites</strong>, aux gardiens de les <strong>valider</strong> et de <strong>contrôler les accès</strong>. Il inclut un système complet de notifications et de suivi.
          </p>
          
          <div class="grid md:grid-cols-2 gap-8">
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">🎯 Fonctionnalités clés</h3>
              <ul class="space-y-2 text-gray-700">
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  Planification de visites par les résidents
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  Validation par les gardiens
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  Contrôle d'accès temps réel
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  Notifications automatiques
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  Historique complet des visites
                </li>
                <li class="flex items-center">
                  <span class="text-green-500 mr-2">✓</span>
                  Rapports et statistiques
                </li>
              </ul>
            </div>
            
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">🔄 Workflow des visites</h3>
              <div class="space-y-3">
                <div class="flex items-center">
                  <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium mr-3">1</span>
                  <span class="text-gray-700">Résident planifie une visite</span>
                </div>
                <div class="flex items-center">
                  <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium mr-3">2</span>
                  <span class="text-gray-700">Gardien valide ou refuse</span>
                </div>
                <div class="flex items-center">
                  <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium mr-3">3</span>
                  <span class="text-gray-700">Visiteur arrive à la résidence</span>
                </div>
                <div class="flex items-center">
                  <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs font-medium mr-3">4</span>
                  <span class="text-gray-700">Contrôle d'accès et entrée</span>
                </div>
                <div class="flex items-center">
                  <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-medium mr-3">5</span>
                  <span class="text-gray-700">Fin de visite et sortie</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Visits Management -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">📋 Gestion des visites</h2>
        
        <div class="space-y-6">
          <!-- Get Visits -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium mr-4">GET</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/visits</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Récupère la liste des visites avec filtres et pagination.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">🔍 Paramètres de filtrage</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    ?status=planifiee|validee|en_cours|terminee|annulee<br>
                    ?date_from=2024-01-01<br>
                    ?date_to=2024-01-31<br>
                    ?resident_id=5<br>
                    ?visitor_name=Jean<br>
                    ?page=1<br>
                    ?limit=20<br>
                    ?sort=date_visite<br>
                    ?order=desc
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4 text-xs">
                  <code class="text-green-400 font-mono">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"visits": [<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;{<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"id_visite": 1,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"nom_visiteur": "Jean Dupont",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"telephone_visiteur": "+33123456789",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"date_visite": "2024-01-20",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"heure_arrivee": "14:00:00",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"heure_depart": "16:00:00",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"motif": "Visite familiale",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"statut": "validee",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"resident": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"nom": "Martin",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"prenom": "Sophie",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"numero_appartement": "A101"<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;},<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"validateur": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"nom": "Gardien",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"prenom": "Paul"<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                    &nbsp;&nbsp;],<br>
                    &nbsp;&nbsp;"pagination": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"current_page": 1,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"total_pages": 3,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"total_visits": 47<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Create Visit -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium mr-4">POST</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/visits</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Planifie une nouvelle visite (résidents uniquement).</p>
            
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
              <p class="text-blue-700 text-sm">
                <strong>Permissions :</strong> Seuls les résidents peuvent planifier des visites pour leur propre appartement.
              </p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Paramètres de requête</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"nom_visiteur": "Jean Dupont",<br>
                    &nbsp;&nbsp;"prenom_visiteur": "Jean",<br>
                    &nbsp;&nbsp;"telephone_visiteur": "+33123456789",<br>
                    &nbsp;&nbsp;"email_visiteur": "jean@example.com",<br>
                    &nbsp;&nbsp;"date_visite": "2024-01-20",<br>
                    &nbsp;&nbsp;"heure_arrivee": "14:00",<br>
                    &nbsp;&nbsp;"heure_depart": "16:00",<br>
                    &nbsp;&nbsp;"motif": "Visite familiale",<br>
                    &nbsp;&nbsp;"nombre_personnes": 2,<br>
                    &nbsp;&nbsp;"commentaire": "Apporte des affaires"<br>
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
                    &nbsp;&nbsp;"message": "Visite planifiée avec succès",<br>
                    &nbsp;&nbsp;"visit": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_visite": 15,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"nom_visiteur": "Jean Dupont",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"date_visite": "2024-01-20",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"statut": "planifiee",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"code_acces": "VIS-2024-001",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"date_creation": "2024-01-15T10:30:00Z"<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
            
            <div class="mt-6">
              <h4 class="font-semibold text-gray-900 mb-3">📝 Règles de validation</h4>
              <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <ul class="text-yellow-800 text-sm space-y-1">
                  <li>• La date de visite doit être dans le futur</li>
                  <li>• L'heure d'arrivée doit être avant l'heure de départ</li>
                  <li>• Maximum 5 personnes par visite</li>
                  <li>• Horaires autorisés : 08h00 - 22h00</li>
                  <li>• Pas plus de 3 visites simultanées par résident</li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Get Visit Details -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium mr-4">GET</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/visits/{id}</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Récupère les détails complets d'une visite spécifique.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">🔍 Paramètres URL</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    /api/visits/15<br><br>
                    
                    // Paramètres optionnels :<br>
                    ?include=logs,notifications<br>
                    ?with_access_code=true
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4 text-xs">
                  <code class="text-green-400 font-mono">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"visit": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_visite": 15,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"nom_visiteur": "Jean Dupont",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"prenom_visiteur": "Jean",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"telephone_visiteur": "+33123456789",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"email_visiteur": "jean@example.com",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"date_visite": "2024-01-20",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"heure_arrivee": "14:00:00",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"heure_depart": "16:00:00",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"motif": "Visite familiale",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"statut": "validee",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"code_acces": "VIS-2024-001",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"heure_entree_reelle": "14:05:00",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"heure_sortie_reelle": null,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"logs": [<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"action": "validation",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"date": "2024-01-19T16:00:00Z",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"utilisateur": "Paul Gardien"<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;]<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Update Visit -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium mr-4">PUT</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/visits/{id}</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Modifie les détails d'une visite (avant validation uniquement).</p>
            
            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6">
              <p class="text-amber-700 text-sm">
                <strong>Restrictions :</strong> Seules les visites avec le statut "planifiee" peuvent être modifiées.
              </p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Paramètres modifiables</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"nom_visiteur": "Jean-Pierre Dupont",<br>
                    &nbsp;&nbsp;"telephone_visiteur": "+33987654321",<br>
                    &nbsp;&nbsp;"date_visite": "2024-01-21",<br>
                    &nbsp;&nbsp;"heure_arrivee": "15:00",<br>
                    &nbsp;&nbsp;"heure_depart": "17:00",<br>
                    &nbsp;&nbsp;"motif": "Visite médicale",<br>
                    &nbsp;&nbsp;"nombre_personnes": 1,<br>
                    &nbsp;&nbsp;"commentaire": "Médecin à domicile"<br>
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
                    &nbsp;&nbsp;"message": "Visite mise à jour avec succès",<br>
                    &nbsp;&nbsp;"visit": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_visite": 15,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"nom_visiteur": "Jean-Pierre Dupont",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"date_visite": "2024-01-21",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"statut": "planifiee",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"date_modification": "2024-01-15T14:30:00Z"<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Cancel Visit -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium mr-4">DELETE</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/visits/{id}</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Annule une visite planifiée ou validée.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Paramètres optionnels</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"motif_annulation": "Changement de programme",<br>
                    &nbsp;&nbsp;"notifier_visiteur": true<br>
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
                    &nbsp;&nbsp;"message": "Visite annulée avec succès",<br>
                    &nbsp;&nbsp;"visit": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_visite": 15,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"statut": "annulee",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"motif_annulation": "Changement de programme",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"date_annulation": "2024-01-15T16:30:00Z"<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Validation & Access Control -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">✅ Validation et contrôle d'accès</h2>
        
        <div class="space-y-6">
          <!-- Validate Visit -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium mr-4">POST</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/visits/{id}/validate</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Valide ou refuse une visite planifiée (gardiens et administrateurs).</p>
            
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
              <p class="text-green-700 text-sm">
                <strong>Permissions :</strong> Seuls les gardiens et administrateurs peuvent valider les visites.
              </p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Validation positive</h4>
                <div class="bg-gray-900 rounded-lg p-4 mb-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"action": "validate",<br>
                    &nbsp;&nbsp;"commentaire": "Visite autorisée",<br>
                    &nbsp;&nbsp;"conditions_speciales": "Présenter une pièce d'identité"<br>
                    }
                  </code>
                </div>
                
                <h4 class="font-semibold text-gray-900 mb-3">📤 Refus</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-red-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"action": "reject",<br>
                    &nbsp;&nbsp;"motif_refus": "Horaire non autorisé",<br>
                    &nbsp;&nbsp;"commentaire": "Proposer un autre créneau"<br>
                    }
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse validation</h4>
                <div class="bg-gray-900 rounded-lg p-4 mb-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"message": "Visite validée",<br>
                    &nbsp;&nbsp;"visit": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_visite": 15,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"statut": "validee",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"validateur": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"nom": "Paul Gardien"<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;},<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"date_validation": "2024-01-19T16:00:00Z"<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
                
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse refus</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-red-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"message": "Visite refusée",<br>
                    &nbsp;&nbsp;"visit": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_visite": 15,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"statut": "refusee",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"motif_refus": "Horaire non autorisé"<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Check Access -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium mr-4">POST</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/visits/check-access</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Vérifie l'accès d'un visiteur avec son code ou QR code.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Vérification par code</h4>
                <div class="bg-gray-900 rounded-lg p-4 mb-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"code_acces": "VIS-2024-001"<br>
                    }
                  </code>
                </div>
                
                <h4 class="font-semibold text-gray-900 mb-3">📤 Vérification par nom</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"nom_visiteur": "Jean Dupont",<br>
                    &nbsp;&nbsp;"telephone": "+33123456789",<br>
                    &nbsp;&nbsp;"resident_id": 5<br>
                    }
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Accès autorisé</h4>
                <div class="bg-gray-900 rounded-lg p-4 mb-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"access_granted": true,<br>
                    &nbsp;&nbsp;"visit": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_visite": 15,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"nom_visiteur": "Jean Dupont",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"resident": "Sophie Martin",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"appartement": "A101",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"heure_arrivee_prevue": "14:00",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"heure_depart_prevue": "16:00"<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
                
                <h4 class="font-semibold text-gray-900 mb-3">📥 Accès refusé</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-red-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"success": false,<br>
                    &nbsp;&nbsp;"access_granted": false,<br>
                    &nbsp;&nbsp;"reason": "CODE_INVALID",<br>
                    &nbsp;&nbsp;"message": "Code d'accès invalide"<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Record Entry/Exit -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium mr-4">POST</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/visits/{id}/access</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Enregistre l'entrée ou la sortie d'un visiteur.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Enregistrer entrée</h4>
                <div class="bg-gray-900 rounded-lg p-4 mb-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"action": "entry",<br>
                    &nbsp;&nbsp;"heure_reelle": "14:05:30",<br>
                    &nbsp;&nbsp;"nombre_personnes_reelles": 2,<br>
                    &nbsp;&nbsp;"commentaire": "Arrivée avec retard"<br>
                    }
                  </code>
                </div>
                
                <h4 class="font-semibold text-gray-900 mb-3">📤 Enregistrer sortie</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"action": "exit",<br>
                    &nbsp;&nbsp;"heure_reelle": "15:45:00",<br>
                    &nbsp;&nbsp;"commentaire": "Départ anticipé"<br>
                    }
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse entrée</h4>
                <div class="bg-gray-900 rounded-lg p-4 mb-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"message": "Entrée enregistrée",<br>
                    &nbsp;&nbsp;"visit": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_visite": 15,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"statut": "en_cours",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"heure_entree_reelle": "14:05:30",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"retard_minutes": 5<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
                
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse sortie</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"message": "Sortie enregistrée",<br>
                    &nbsp;&nbsp;"visit": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"id_visite": 15,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"statut": "terminee",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"heure_sortie_reelle": "15:45:00",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"duree_reelle_minutes": 100<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Visit Statistics -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">📊 Statistiques et rapports</h2>
        
        <div class="space-y-6">
          <!-- Get Statistics -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium mr-4">GET</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/visits/statistics</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Récupère les statistiques détaillées des visites.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">🔍 Paramètres de période</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    ?period=day|week|month|year<br>
                    ?start_date=2024-01-01<br>
                    ?end_date=2024-01-31<br>
                    ?resident_id=5<br>
                    ?group_by=status|day|week|resident
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4 text-xs">
                  <code class="text-green-400 font-mono">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"period": "month",<br>
                    &nbsp;&nbsp;"stats": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"total_visits": 125,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"validated_visits": 118,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"completed_visits": 110,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"cancelled_visits": 7,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"average_duration_minutes": 95,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"most_active_day": "Wednesday",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"peak_hours": ["14:00", "15:00", "16:00"]<br>
                    &nbsp;&nbsp;},<br>
                    &nbsp;&nbsp;"by_status": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"planifiee": 15,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"validee": 8,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"en_cours": 2,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"terminee": 110,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"annulee": 7<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Daily Report -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium mr-4">GET</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/visits/daily-report</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Rapport quotidien des visites pour les gardiens.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">🔍 Paramètres optionnels</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    ?date=2024-01-20<br>
                    ?include_past=true<br>
                    ?format=json|pdf<br>
                    ?sort=time|apartment
                  </code>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📥 Réponse succès</h4>
                <div class="bg-gray-900 rounded-lg p-4 text-xs">
                  <code class="text-green-400 font-mono">
                    {<br>
                    &nbsp;&nbsp;"success": true,<br>
                    &nbsp;&nbsp;"date": "2024-01-20",<br>
                    &nbsp;&nbsp;"summary": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"total_scheduled": 8,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"pending_validation": 2,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"in_progress": 1,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"completed": 5<br>
                    &nbsp;&nbsp;},<br>
                    &nbsp;&nbsp;"visits": [<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;{<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"heure": "14:00",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"visiteur": "Jean Dupont",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"resident": "Sophie Martin",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"appartement": "A101",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"statut": "validee",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"motif": "Visite familiale"<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                    &nbsp;&nbsp;]<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>

          <!-- Export Reports -->
          <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
            <div class="flex items-center mb-4">
              <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium mr-4">POST</span>
              <h3 class="text-xl font-semibold text-gray-900">/api/visits/export</h3>
            </div>
            
            <p class="text-gray-700 mb-6">Exporte les données de visites dans différents formats.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <h4 class="font-semibold text-gray-900 mb-3">📤 Paramètres d'export</h4>
                <div class="bg-gray-900 rounded-lg p-4">
                  <code class="text-green-400 font-mono text-sm">
                    {<br>
                    &nbsp;&nbsp;"format": "excel|pdf|csv",<br>
                    &nbsp;&nbsp;"period": "month",<br>
                    &nbsp;&nbsp;"start_date": "2024-01-01",<br>
                    &nbsp;&nbsp;"end_date": "2024-01-31",<br>
                    &nbsp;&nbsp;"include_stats": true,<br>
                    &nbsp;&nbsp;"include_details": true,<br>
                    &nbsp;&nbsp;"filters": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"status": ["validee", "terminee"],<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"resident_id": 5<br>
                    &nbsp;&nbsp;}<br>
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
                    &nbsp;&nbsp;"message": "Export généré avec succès",<br>
                    &nbsp;&nbsp;"download_url": "http://localhost:8000/exports/visits_2024_01.xlsx",<br>
                    &nbsp;&nbsp;"file_info": {<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"filename": "visits_2024_01.xlsx",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"size": "245KB",<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"total_records": 125,<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;"expires_at": "2024-01-22T10:30:00Z"<br>
                    &nbsp;&nbsp;}<br>
                    }
                  </code>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Status Codes -->
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">🏷️ Statuts des visites</h2>
        <div class="bg-white rounded-xl p-8 shadow-lg border border-gray-200">
          <div class="grid md:grid-cols-2 gap-8">
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">📋 Statuts disponibles</h3>
              <div class="space-y-3">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                  <div class="font-medium text-blue-800">planifiee</div>
                  <div class="text-blue-600 text-sm">Visite créée, en attente de validation</div>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                  <div class="font-medium text-green-800">validee</div>
                  <div class="text-green-600 text-sm">Visite approuvée par un gardien</div>
                </div>
                
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                  <div class="font-medium text-purple-800">en_cours</div>
                  <div class="text-purple-600 text-sm">Visiteur présent dans la résidence</div>
                </div>
                
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                  <div class="font-medium text-gray-800">terminee</div>
                  <div class="text-gray-600 text-sm">Visite terminée, visiteur parti</div>
                </div>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                  <div class="font-medium text-red-800">annulee</div>
                  <div class="text-red-600 text-sm">Visite annulée par le résident</div>
                </div>
                
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                  <div class="font-medium text-orange-800">refusee</div>
                  <div class="text-orange-600 text-sm">Visite refusée par un gardien</div>
                </div>
              </div>
            </div>
            
            <div>
              <h3 class="font-semibold text-gray-900 mb-4">🔄 Transitions autorisées</h3>
              <div class="space-y-2 text-sm">
                <div class="text-gray-700">
                  <strong>planifiee</strong> → validee, refusee, annulee
                </div>
                <div class="text-gray-700">
                  <strong>validee</strong> → en_cours, annulee
                </div>
                <div class="text-gray-700">
                  <strong>en_cours</strong> → terminee
                </div>
                <div class="text-gray-700">
                  <strong>terminee</strong> → (final)
                </div>
                <div class="text-gray-700">
                  <strong>annulee</strong> → (final)
                </div>
                <div class="text-gray-700">
                  <strong>refusee</strong> → (final)
                </div>
              </div>
              
              <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <p class="text-yellow-700 text-sm">
                  <strong>Note :</strong> Les transitions de statut sont automatiquement journalisées et peuvent déclencher des notifications.
                </p>
              </div>
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
                  <div class="text-red-600 text-sm">Pas autorisé à modifier cette visite</div>
                </div>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                  <div class="font-medium text-red-800">422 - Validation échouée</div>
                  <div class="text-red-600 text-sm">Données de visite invalides</div>
                </div>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                  <div class="font-medium text-red-800">409 - Conflit</div>
                  <div class="text-red-600 text-sm">Visite déjà validée ou terminée</div>
                </div>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                  <div class="font-medium text-red-800">404 - Non trouvé</div>
                  <div class="text-red-600 text-sm">Code d'accès invalide</div>
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
                  &nbsp;&nbsp;&nbsp;&nbsp;"code": "VISIT_ALREADY_VALIDATED",<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;"message": "Cette visite a déjà été validée",<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;"details": {<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"visit_id": 15,<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"current_status": "validee",<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"validated_by": "Paul Gardien",<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"validated_at": "2024-01-19T16:00:00Z"<br>
                  &nbsp;&nbsp;&nbsp;&nbsp;}<br>
                  &nbsp;&nbsp;}<br>
                  }
                </code>
              </div>
              
              <div class="mt-4 bg-blue-50 border-l-4 border-blue-400 p-4">
                <p class="text-blue-700 text-sm">
                  <strong>Conseil :</strong> Vérifiez toujours le statut actuel d'une visite avant d'essayer de la modifier.
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
    title: 'API Visites - Documentation Gestion Résidence',
    meta: [
      { name: 'description', content: 'Documentation complète de l\'API de gestion des visites pour l\'application de gestion de résidence.' }
    ]
  })
</script>


