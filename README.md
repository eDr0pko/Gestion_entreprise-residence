# Gestion Entreprise Résidence - Architecture Refactorisée

Application complète de gestion pour entreprise de résidence avec architecture moderne en microservices : **Backend-Client** (Proxy + DB) et **Backend-NHS** (Business Logic).

---

## 🏗️ Architecture

L'application utilise une architecture innovante en 2 backends :

```
Frontend (Nuxt.js) :3000
        ↓
Backend-Client :8000 (Proxy + Database)
        ↓
Backend-NHS :8001 (Business Logic)
```

### Backend-Client (Port 8000)
- **Rôle** : Proxy + Accès base de données
- **Responsabilités** : Authentification locale, DB directe, fichiers, proxy vers NHS

### Backend-NHS (Port 8001) 
- **Rôle** : Logique métier complète
- **Responsabilités** : Business logic, validation, règles métier, NHS propriétaire

---

## 🚀 Démarrage Rapide

### Option 1 : Script automatique
```powershell
# Démarrage automatique de tous les services
.\start-architecture.ps1
```

### Option 2 : Manuel

1. **Backend-Client**
```powershell
cd backend-client
composer install
cp .env.example .env
php artisan key:generate
php artisan serve --port=8000
```

2. **Backend-NHS**
```powershell
cd backend-nhs
composer install
cp .env.example .env
php artisan key:generate
php artisan serve --port=8001
```

3. **Frontend**
```powershell
cd frontend
npm install
npm run dev
```

---

## 📋 Prérequis

- **PHP** >= 8.1 (avec extensions : mysql, mbstring, openssl, pdo, tokenizer, xml, ctype, json)
- **Composer** (gestionnaire de dépendances PHP)
- **Node.js** >= 18.x et **npm**
- **MySQL** >= 8.0
- **Git**

---

## 🗄️ Base de données

### Création et peuplement
```bash
# Créer la base de données
mysql -u root -p
CREATE DATABASE gestion_entreprise_residence CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Importer les données
mysql -u root -p gestion_entreprise_residence < final.sql
```

### Configuration .env (identique pour les 2 backends)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_entreprise_residence
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe

# Backend-Client
APP_URL=http://localhost:8000
NHS_API_URL=http://localhost:8001

# Backend-NHS
APP_URL=http://localhost:8001
```

---

## 🔧 Tests et Validation

### Test complet de l'architecture
```powershell
.\test-architecture.ps1
```

### Tests individuels
```bash
# Backend-Client
curl http://localhost:8000/api/health
curl http://localhost:8000/api/db-check

# Backend-NHS
curl http://localhost:8001/api/health
curl http://localhost:8001/api/nhs/status

# Proxy
curl http://localhost:8000/api/nhs-test
```

---

## 📱 Fonctionnalités

### 🔐 Authentification
- Login/Logout utilisateurs et invités
- Gestion des tokens (Laravel Sanctum)
- Profils et avatars

### 💬 Messagerie
- Conversations de groupe
- Messages avec fichiers joints
- Réactions emoji
- Notifications temps réel

### 🏥 Incidents
- Signalement d'incidents
- Suivi et résolution
- Pièces jointes

### 👥 Gestion Utilisateurs
- Résidents, gardiens, administrateurs
- Invités temporaires
- Système de bannissement

### 📅 Visites
- Planification de visites
- Gestion des statuts
- Invitations

### 📊 Administration
- Statistiques complètes
- Logs d'activité
- Modération des messages

### 🚀 NHS Propriétaire
- Analytics avancées
- Automatisations intelligentes
- Maintenance prédictive
- Monitoring sécurité

---

## 🎯 Accès Application

- **Frontend** : http://localhost:3000
- **Backend-Client API** : http://localhost:8000/api
- **Backend-NHS API** : http://localhost:8001/api

### Comptes de test
```
Admin: admin@residence.com / password
Gardien: gardien@residence.com / password
Résident: marie.durand@residence.com / password
```

---

## 📁 Structure du Projet

```
Gestion_entreprise-residence/
├── frontend/                 # Nuxt.js (Vue 3 + TypeScript)
│   ├── components/          # Composants réutilisables
│   ├── pages/              # Pages de l'application
│   ├── stores/             # Pinia stores
│   └── nuxt.config.ts      # Configuration Nuxt
├── backend-client/          # Laravel (Proxy + DB)
│   ├── app/Http/Controllers/
│   │   ├── UnifiedProxyController.php
│   │   ├── DatabaseController.php
│   │   └── AuthController.php (minimal)
│   └── routes/api.php      # Routes proxy
├── backend-nhs/            # Laravel (Business Logic)
│   ├── app/Http/Controllers/
│   │   ├── AuthController.php (complet)
│   │   ├── MessageController.php
│   │   ├── IncidentController.php
│   │   └── ApiController.php (NHS)
│   └── routes/api.php      # Routes métier
├── final.sql               # Base de données avec données de test
├── ARCHITECTURE.md         # Documentation architecture
├── VALIDATION_COMPLETE.md  # Validation fonctionnelle
├── start-architecture.ps1  # Script de démarrage
└── test-architecture.ps1   # Script de test
```

---

## 🔍 Comparaison avec l'ancien backend

| Aspect | Ancien Backend | Nouvelle Architecture |
|--------|----------------|-----------------------|
| **Architecture** | Monolithique | Microservices (2 backends) |
| **Ports** | 8000 | 8000 (client) + 8001 (NHS) |
| **Responsabilités** | Tout en un | Séparées (Proxy/DB vs Logic) |
| **Scalabilité** | Limitée | Excellente |
| **Fonctionnalités** | Standard | Standard + NHS propriétaire |
| **Performance** | Bonne | Améliorée |
| **Maintenance** | Complexe | Simplifiée |

**✅ 100% des fonctionnalités de l'ancien backend sont préservées**

---

## 📚 Documentation

- [`ARCHITECTURE.md`](ARCHITECTURE.md) - Architecture détaillée
- [`VALIDATION_COMPLETE.md`](VALIDATION_COMPLETE.md) - Validation fonctionnelle
- [Documentation API Backend-Client](http://localhost:8000/api/health)
- [Documentation API Backend-NHS](http://localhost:8001/api/health)

---

## 🤝 Contribution

1. Cloner le repository
2. Créer une branche feature (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Commiter les changements (`git commit -am 'Ajout nouvelle fonctionnalité'`)
4. Pousser la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. Créer une Pull Request

---

## 📄 Licence

Projet propriétaire - Tous droits réservés

---

## ⚡ Dépannage

### Problèmes courants

1. **Port déjà utilisé**
   ```bash
   netstat -ano | findstr :8000
   taskkill /PID <PID> /F
   ```

2. **Erreur de base de données**
   - Vérifier les paramètres `.env`
   - Vérifier que MySQL est démarré
   - Importer `final.sql`

3. **Proxy non fonctionnel**
   - Vérifier que Backend-NHS est démarré
   - Vérifier `NHS_API_URL` dans `.env`

4. **Frontend ne charge pas**
   - Vérifier `npm install`
   - Vérifier que les backends sont démarrés

### Support
- Consulter les logs : `storage/logs/laravel.log`
- Tester l'architecture : `.\test-architecture.ps1`
- Vérifier les services : `.\start-architecture.ps1`
