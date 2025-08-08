# Utilisation de Postman pour tester les API du projet

## 1. Installation de Postman
- Téléchargez et installez Postman : https://www.postman.com/downloads/

## 2. Importer la collection
- Ouvrez Postman
- Cliquez sur "Importer" puis sélectionnez le fichier `Gestion_entreprise-residence.postman_collection.json` fourni dans ce dossier

## 3. Configuration de l'environnement
- Configurez l'environnement Postman avec les variables suivantes :
  - `API_CLIENT` : http://localhost:8000/api
  - `API_NHS` : http://localhost:8001/api
  - `EMAIL` : admin@residence.com
  - `PASSWORD` : 1234

## 4. Authentification
- Lancez la requête "Login" pour obtenir le token
- Le token sera automatiquement utilisé pour les requêtes suivantes

## 5. Tests des endpoints
- Testez chaque route (incidents, logs, résidents, visiteurs, conversations, stats, etc.)
- Vérifiez les réponses et les éventuelles erreurs

## 6. Débogage
- Si une route ne répond pas ou retourne une erreur, vérifiez :
  - Les logs Laravel (`backend-client/storage/logs/laravel.log` ou `backend-nhs/storage/logs/laravel.log`)
  - La configuration CORS
  - Les permissions et le token

## 7. Correction
- Si un endpoint ne fonctionne pas dans Postman, il ne fonctionnera pas dans le frontend. Corrigez d'abord côté backend.

---

**Remarque :**
Ce dossier contient la collection Postman pour automatiser les tests de toutes les API du projet.
