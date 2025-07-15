
# Gestion Entreprise Résidence

Application complète de gestion pour entreprise de résidence, avec système de messagerie moderne, gestion des membres, visiteurs, statistiques, et interface responsive. Pensée pour la modularité, la sécurité et l’ergonomie.

---

## Prérequis

- **PHP** >= 8.1 avec extensions : mysql, mbstring, openssl, pdo, tokenizer, xml, ctype, json
- **Composer** (gestionnaire de dépendances PHP)
- **Node.js** >= 18.x et **npm**
- **MySQL** >= 8.0
- **Git**

## 1. Cloner le projet

```bash
git clone <URL_DU_REPOSITORY>
cd Gestion_entreprise-residence
```

## 2. Configuration du Backend (Laravel)

### Installation des dépendances
```bash
cd backend
composer install
npm install
```

### Configuration de l'environnement
```bash
# Copier le fichier de configuration
copy .env.example .env    # Windows
# ou
cp .env.example .env      # Linux/Mac

# Générer la clé d'application
php artisan key:generate
```

### Configuration de la base de données
Éditer le fichier `.env` avec vos paramètres :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_entreprise_residence
DB_USERNAME=votre_nom_utilisateur
DB_PASSWORD=votre_mot_de_passe

# Configuration JWT (optionnel, sera généré automatiquement)
JWT_SECRET=
```

### Création de la base de données
```bash
# Créer la base de données avec support UTF8MB4 (pour les emojis)
mysql -u root -p -e "CREATE DATABASE gestion_entreprise_residence CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Importer le schéma et les données
mysql -u votre_nom_utilisateur -p gestion_entreprise_residence < ../creationdb_corrected.sql
```

**Note importante :** La base de données est configurée avec UTF8MB4 pour supporter les emojis Unicode dans les réactions aux messages.

### Démarrage du backend
```bash
php artisan serve
```
Le backend sera accessible sur `http://localhost:8000`

## 3. Configuration du Frontend (Nuxt.js)


### Installation des dépendances
```bash
cd ../frontend
npm install
# Pour les statistiques et graphiques admin :
npm install chart.js vue-chartjs
```

> **Note :** Les graphiques du tableau de bord admin utilisent [chart.js](https://www.chartjs.org/) et [vue-chartjs](https://vue-chartjs.org/). Si vous ne voyez pas les graphiques, vérifiez que ces deux paquets sont bien installés dans le dossier `frontend`.

### Configuration de l'API
Vérifier le fichier `nuxt.config.ts` :
```typescript
export default defineNuxtConfig({
  // ...
  runtimeConfig: {
    public: {
      apiBase: 'http://localhost:8000/api'
    }
  }
})
```


### Démarrage du frontend
```bash
npm run dev
```
Le frontend sera accessible sur `http://localhost:3000` (ou 3001 si le port 3000 est occupé)


## 4. Vérification de l'installation

### Tester le backend
```bash
# Test de santé de l'API
curl http://localhost:8000/api/health

# Ou avec PowerShell sur Windows
Invoke-RestMethod -Uri "http://localhost:8000/api/health"
```

### Accéder à l'application
1. Ouvrir `http://localhost:3000` dans votre navigateur
2. Se connecter avec un compte de test (voir les données dans `creationdb_corrected.sql`)
3. Accéder à la page Statistiques (Admin) pour visualiser les graphiques et indicateurs avancés.

## 5. Comptes de test

Après avoir importé `creationdb_corrected.sql`, vous aurez accès à ces comptes :

### Administrateurs
- **Email :** `admin1@residence.com` / **Mot de passe :** `1234`
- **Email :** `admin2@residence.com` / **Mot de passe :** `1234`

### Gardiens
- **Email :** `gardien1@residence.com` / **Mot de passe :** `1234`
- **Email :** `gardien2@residence.com` / **Mot de passe :** `1234`

### Résidents
- **Email :** `resident1@residence.com` / **Mot de passe :** `1234`
- **Email :** `resident2@residence.com` / **Mot de passe :** `1234`

## 6. Structure du projet

```
Gestion_entreprise-residence/
├── backend/                    # API Laravel
│   ├── app/
│   │   ├── Http/Controllers/  # Contrôleurs API
│   │   └── Models/            # Modèles Eloquent
│   ├── routes/api.php         # Routes API
│   ├── .env.example          # Fichier de configuration exemple
│   └── ...
├── frontend/                  # Interface Nuxt.js
│   ├── components/            # Composants Vue.js
│   ├── pages/                 # Pages de l'application
│   ├── types/                 # Types TypeScript
│   ├── stores/                # Stores Pinia
│   └── ...
├── creationdb_corrected.sql   # Script de base de données
├── install.bat                # Script d'installation Windows
├── install.sh                 # Script d'installation Linux/Mac
└── README.md                  # Ce fichier
```


## 7. Fonctionnalités principales

- **Authentification multi-rôles** : Connexion sécurisée (JWT) pour Administrateur, Gardien, Résident
- **Tableau de bord dynamique** : Sections selon le rôle (statistiques, gestion résidents/gardiens, visiteurs, conversations, paramètres)
- **Messagerie de groupe** : Conversations, réactions (emojis), pièces jointes, notifications, auto-refresh
- **Gestion des membres** : Ajout/suppression de membres dans les groupes, gestion des rôles
- **Gestion des visiteurs** : Ajout, suivi, historique des visites
- **Statistiques** : Vue synthétique pour admins et gardiens
- **Paramètres** : Gestion des préférences et sécurité (admin)
- **Interface responsive** : Design moderne, glassmorphisme, animations, compatible mobile/desktop
- **Téléchargement de fichiers** : Support des pièces jointes dans la messagerie
- **Sécurité** : Permissions par rôle, middleware, protection des routes, gestion des sessions
- **Support international** : Composant PhoneInput avec plus de 100 pays
- **Scripts d’installation automatique** : Windows & Linux/Mac
## 17. Sécurité et bonnes pratiques

- Authentification JWT (Laravel Sanctum)
- Middleware d’authentification et de gestion des rôles (voir `frontend/middleware/auth.ts`)
- Permissions strictes sur les routes et les actions (backend & frontend)
- Upload de fichiers sécurisé (taille, type, stockage)
- Base de données en UTF8MB4 pour le support des emojis et caractères internationaux
- Variables d’environnement pour toutes les clés sensibles
- Logs backend (`backend/storage/logs/laravel.log`) et gestion des erreurs frontend (console navigateur)
## 18. Personnalisation & extension

- **Ajouter un rôle** :
  - Ajouter le rôle dans la table `personnes` (backend)
  - Adapter les contrôleurs, middleware et la gestion des permissions
  - Ajouter la section correspondante dans le dashboard (frontend/pages/dashboard.vue)
- **Ajouter une section au dashboard** :
  - Créer un composant Vue dans `frontend/components/dashboard/`
  - Ajouter la section dans le tableau `adminSections` ou `gardienSections`
- **Modifier le style** :
  - Modifier les fichiers Tailwind (`frontend/assets/css/tailwind.css`, `tailwind.config.js`)
  - Personnaliser les composants Vue
- **Changer l’URL de l’API** :
  - Modifier `apiBase` dans `frontend/nuxt.config.ts`
- **Déploiement** :
  - Utiliser `npm run build` (frontend) et `composer install --no-dev` (backend)
  - Configurer un serveur web (Nginx/Apache) pour servir le frontend et le backend
## 19. API REST : Endpoints principaux

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| POST    | /api/login | Authentification utilisateur |
| POST    | /api/logout | Déconnexion |
| GET     | /api/conversations | Liste des conversations |
| GET     | /api/messages/{id} | Messages d’un groupe |
| POST    | /api/messages/{id}/reactions | Réagir à un message |
| POST    | /api/messages/{id} | Envoyer un message |
| GET     | /api/visites | Liste des visites |
| POST    | /api/guests | Ajouter un visiteur |
| GET     | /api/users | Liste des utilisateurs (admin) |
| ...     | ...      | Voir `routes/api.php` pour la liste complète |

### Exemples de payloads

**Authentification**
```json
{
  "email": "admin1@residence.com",
  "password": "1234"
}
```

**Message**
```json
{
  "contenu": "Bonjour à tous !",
  "fichiers": [/* fichiers optionnels */]
}
```
## 20. Types TypeScript principaux

Voir `frontend/types/index.ts` pour la liste complète. Exemples :

```typescript
export interface Conversation {
  id_groupe_message: number
  nom_groupe: string
  date_creation: string
  messages_non_lus: number
  // ...
}

export interface Message {
  id_message: number
  contenu_message?: string
  date_envoi: string
  email_auteur: string
  auteur_nom: string
  // ...
}
```
## 21. Tests & qualité

- **Backend** :
  - Tests unitaires et fonctionnels dans `backend/tests/`
  - Lancer avec `php artisan test`
- **Frontend** :
  - Vérification des types : `npm run type-check`
  - Prévu pour être compatible avec des outils comme Vitest ou Cypress
- **Linting** :
  - Respect des conventions PSR-12 (PHP), ESLint/Prettier (JS/TS)
## 22. FAQ (compléments)

- **Comment ajouter un champ à un utilisateur ?**
  - Modifier le modèle `User` (backend/app/Models/User.php) et la migration correspondante
  - Adapter le formulaire côté frontend
- **Comment changer le port du frontend ?**
  - Lancer avec `npm run dev -- --port=3001`
- **Comment activer le SSR ?**
  - Modifier `ssr: false` dans `frontend/nuxt.config.ts` (par défaut désactivé pour simplifier l’auth)
## 23. Liens utiles complémentaires

- [Nuxt Auth Module](https://auth.nuxt.com/)
- [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum)
- [VueUse (composables)](https://vueuse.org/)
- [Vite](https://vitejs.dev/)

## 8. Développement

### Backend (Laravel)
```bash
cd backend
php artisan serve                # Démarrer le serveur de développement
php artisan migrate              # Exécuter les migrations (si disponibles)
php artisan tinker               # Console interactive Laravel
php artisan route:list           # Lister toutes les routes
```

### Frontend (Nuxt.js)
```bash
cd frontend
npm run dev                      # Mode développement avec hot-reload
npm run build                    # Build de production
npm run preview                  # Prévisualiser le build de production
npm run type-check               # Vérification des types TypeScript (si disponible)
```

## 9. Dépannage

### Erreur "Class not found"
```bash
cd backend
composer dump-autoload
```

### Erreur de permissions (Linux/Mac)
```bash
sudo chmod -R 775 storage bootstrap/cache
```

### Erreur de base de données
- Vérifier que MySQL est démarré
- Vérifier les paramètres dans le fichier `.env`
- Vérifier que la base de données existe
- S'assurer que l'utilisateur a les permissions nécessaires

### Problème avec les emojis (réactions)
- S'assurer que la base de données utilise UTF8MB4 : `SHOW CREATE DATABASE gestion_entreprise_residence;`
- Réinstaller la base avec UTF8MB4 : `./reinstall_utf8mb4.bat` (Windows) ou `./reinstall_utf8mb4.sh` (Linux/Mac)
- Vérifier que la configuration Laravel utilise `charset=utf8mb4` dans `config/database.php`

### Port déjà utilisé
- **Backend :** `php artisan serve --port=8001`
- **Frontend :** Le port sera automatiquement changé (ex: 3001)

## 10. Déploiement en production

### Backend
```bash
# Optimiser les dépendances (sans les packages de développement)
composer install --optimize-autoloader --no-dev

# Mettre en cache les configurations pour améliorer les performances
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Générer la clé d'application en production
php artisan key:generate --force
```

### Frontend
```bash
# Construire l'application pour la production
npm run build
```

## 11. Scripts d'installation automatique

Pour faciliter l'installation, des scripts sont disponibles :

### Windows
```cmd
install.bat
```

### Linux/Mac
```bash
chmod +x install.sh
./install.sh
```

Ces scripts installent automatiquement les dépendances et configurent l'environnement de base.

## 12. Support et Aide

Pour toute question ou problème, vérifiez :

1. **Logs du backend** : `backend/storage/logs/laravel.log`
2. **Console du navigateur** : F12 → Console (pour les erreurs frontend)
3. **Endpoints de test** : 
   - `/api/health` - Vérifier que l'API fonctionne
   - `/api/db-check` - Vérifier la connexion à la base de données

### Problèmes courants

- **Erreur 500** : Vérifier les logs Laravel et la configuration de la base de données
- **CORS** : S'assurer que l'URL du frontend est autorisée dans la configuration Laravel
- **Authentification** : Vérifier que JWT est correctement configuré
- **Permissions** : Sur Linux/Mac, vérifier les permissions des dossiers `storage` et `bootstrap/cache`

---
# Pays supportés - Composant PhoneInput

Le composant `PhoneInput.vue` supporte maintenant **plus de 100 pays** avec leur formatage spécifique et leurs drapeaux.

## Liste complète des pays

### Europe 🇪🇺

| Pays | Code | Drapeau | Format exemple |
|------|------|---------|----------------|
| France | +33 | 🇫🇷 | +33 6 12 34 56 78 |
| Allemagne | +49 | 🇩🇪 | +49 123 12345678 |
| Royaume-Uni | +44 | 🇬🇧 | +44 20 1234 5678 |
| Espagne | +34 | 🇪🇸 | +34 123 45 67 89 |
| Italie | +39 | 🇮🇹 | +39 123 456 7890 |
| Belgique | +32 | 🇧🇪 | +32 12 34 56 78 |
| Pays-Bas | +31 | 🇳🇱 | +31 12 345 6789 |
| Portugal | +351 | 🇵🇹 | +351 123 456 789 |
| Norvège | +47 | 🇳🇴 | +47 123 45 678 |
| Suède | +46 | 🇸🇪 | +46 12 345 67 89 |
| Finlande | +358 | 🇫🇮 | +358 12 345 6789 |
| Danemark | +45 | 🇩🇰 | +45 12 34 56 78 |
| Islande | +354 | 🇮🇸 | +354 123 4567 |
| Irlande | +353 | 🇮🇪 | +353 12 345 6789 |
| Autriche | +43 | 🇦🇹 | +43 123 1234567 |
| Suisse | +41 | 🇨🇭 | +41 12 345 67 89 |
| République tchèque | +420 | 🇨🇿 | +420 123 456 789 |
| Slovaquie | +421 | 🇸🇰 | +421 123 456 789 |
| Hongrie | +36 | 🇭🇺 | +36 12 345 6789 |
| Pologne | +48 | 🇵🇱 | +48 123 456 789 |
| Roumanie | +40 | 🇷🇴 | +40 123 456 789 |
| Bulgarie | +359 | 🇧🇬 | +359 12 345 6789 |
| Croatie | +385 | 🇭🇷 | +385 12 345 6789 |
| Serbie | +381 | 🇷🇸 | +381 12 345 6789 |
| Slovénie | +386 | 🇸🇮 | +386 12 345 678 |
| Lituanie | +370 | 🇱🇹 | +370 123 45678 |
| Lettonie | +371 | 🇱🇻 | +371 12 345 678 |
| Estonie | +372 | 🇪🇪 | +372 1234 5678 |
| Ukraine | +380 | 🇺🇦 | +380 12 345 6789 |
| Biélorussie | +375 | 🇧🇾 | +375 12 345-67-89 |
| Moldavie | +373 | 🇲🇩 | +373 1234 5678 |

### Afrique 🌍

| Pays | Code | Drapeau | Format exemple |
|------|------|---------|----------------|
| Togo | +228 | 🇹🇬 | +228 12 34 56 78 |
| Maroc | +212 | 🇲🇦 | +212 6-12-34-56-78 |
| Algérie | +213 | 🇩🇿 | +213 123 45 67 89 |
| Tunisie | +216 | 🇹🇳 | +216 12 345 678 |
| Sénégal | +221 | 🇸🇳 | +221 12 345 67 89 |
| Mali | +223 | 🇲🇱 | +223 12 34 56 78 |
| Burkina Faso | +226 | 🇧🇫 | +226 12 34 56 78 |
| Niger | +227 | 🇳🇪 | +227 12 34 56 78 |
| Bénin | +229 | 🇧🇯 | +229 12 34 56 78 |
| Mauritanie | +222 | 🇲🇷 | +222 12 34 56 78 |
| Côte d'Ivoire | +225 | 🇨🇮 | +225 12 34 56 78 90 |
| Ghana | +233 | 🇬🇭 | +233 123 456 7890 |
| Nigeria | +234 | 🇳🇬 | +234 123 456 7890 |
| Cameroun | +237 | 🇨🇲 | +237 1 23 45 67 89 |
| Gabon | +241 | 🇬🇦 | +241 1 23 45 67 |
| RD Congo | +243 | 🇨🇩 | +243 123 456 789 |
| Madagascar | +261 | 🇲🇬 | +261 12 34 567 89 |
| Maurice | +230 | 🇲🇺 | +230 1234 5678 |
| Égypte | +20 | 🇪🇬 | +20 12 3456 7890 |
| Kenya | +254 | 🇰🇪 | +254 123 456789 |
| Éthiopie | +251 | 🇪🇹 | +251 12 345 6789 |
| Tanzanie | +255 | 🇹🇿 | +255 123 456 789 |
| Ouganda | +256 | 🇺🇬 | +256 123 456789 |
| Zambie | +260 | 🇿🇲 | +260 12 3456789 |
| Zimbabwe | +263 | 🇿🇼 | +263 12 345 6789 |
| Botswana | +267 | 🇧🇼 | +267 12 345 678 |
| Afrique du Sud | +27 | 🇿🇦 | +27 12 345 6789 |

### Amériques 🌎

| Pays | Code | Drapeau | Format exemple |
|------|------|---------|----------------|
| États-Unis | +1 | 🇺🇸 | +1 (555) 123-4567 |
| Canada | +1 | 🇨🇦 | +1 (555) 123-4567 |
| Mexique | +52 | 🇲🇽 | +52 12 3456 7890 |
| Brésil | +55 | 🇧🇷 | +55 (11) 12345-6789 |
| Argentine | +54 | 🇦🇷 | +54 11 1234-5678 |
| Chili | +56 | 🇨🇱 | +56 1 2345 6789 |
| Colombie | +57 | 🇨🇴 | +57 123 456 7890 |
| Pérou | +51 | 🇵🇪 | +51 123 456 789 |
| Venezuela | +58 | 🇻🇪 | +58 123-456-7890 |
| Uruguay | +598 | 🇺🇾 | +598 1 234 5678 |
| Paraguay | +595 | 🇵🇾 | +595 123 456789 |
| Équateur | +593 | 🇪🇨 | +593 12 345 6789 |
| Bolivie | +591 | 🇧🇴 | +591 1 234 5678 |

### Asie 🌏

| Pays | Code | Drapeau | Format exemple |
|------|------|---------|----------------|
| Chine | +86 | 🇨🇳 | +86 123 456 7890 |
| Japon | +81 | 🇯🇵 | +81 123-4567-8901 |
| Corée du Sud | +82 | 🇰🇷 | +82 12-3456-7890 |
| Inde | +91 | 🇮🇳 | +91 12345 67890 |
| Thaïlande | +66 | 🇹🇭 | +66 12 345 6789 |
| Singapour | +65 | 🇸🇬 | +65 1234 5678 |
| Malaisie | +60 | 🇲🇾 | +60 12-345 6789 |
| Philippines | +63 | 🇵🇭 | +63 123 456 7890 |
| Indonésie | +62 | 🇮🇩 | +62 123 456 7890 |
| Vietnam | +84 | 🇻🇳 | +84 123 456 789 |
| Iran | +98 | 🇮🇷 | +98 123 456 7890 |
| Irak | +964 | 🇮🇶 | +964 123 456 7890 |
| Afghanistan | +93 | 🇦🇫 | +93 12 345 6789 |
| Pakistan | +92 | 🇵🇰 | +92 123 4567890 |
| Bangladesh | +880 | 🇧🇩 | +880 1234-567890 |
| Sri Lanka | +94 | 🇱🇰 | +94 12 345 6789 |
| Népal | +977 | 🇳🇵 | +977 123-456-7890 |
| Myanmar | +95 | 🇲🇲 | +95 12 3456 7890 |
| Cambodge | +855 | 🇰🇭 | +855 12 345 678 |
| Laos | +856 | 🇱🇦 | +856 12 345 678 |
| Mongolie | +976 | 🇲🇳 | +976 1234 5678 |

### Moyen-Orient 🕌

| Pays | Code | Drapeau | Format exemple |
|------|------|---------|----------------|
| Arabie Saoudite | +966 | 🇸🇦 | +966 12 345 6789 |
| Émirats Arabes Unis | +971 | 🇦🇪 | +971 12 345 6789 |
| Qatar | +974 | 🇶🇦 | +974 1234 5678 |
| Koweït | +965 | 🇰🇼 | +965 1234 5678 |
| Bahreïn | +973 | 🇧🇭 | +973 1234 5678 |
| Oman | +968 | 🇴🇲 | +968 1234 5678 |
| Jordanie | +962 | 🇯🇴 | +962 1 2345 6789 |
| Liban | +961 | 🇱🇧 | +961 12 345 678 |
| Israël | +972 | 🇮🇱 | +972 12-345-6789 |

### Ex-URSS & Asie Centrale 🏔️

| Pays | Code | Drapeau | Format exemple |
|------|------|---------|----------------|
| Russie | +7 | 🇷🇺 | +7 123 456-78-90 |
| Kazakhstan | +7 | 🇰🇿 | +7 123 456 7890 |
| Ouzbékistan | +998 | 🇺🇿 | +998 12 345 6789 |
| Kirghizistan | +996 | 🇰🇬 | +996 123 456789 |
| Tadjikistan | +992 | 🇹🇯 | +992 12 345 6789 |
| Turkménistan | +993 | 🇹🇲 | +993 12 345678 |
| Géorgie | +995 | 🇬🇪 | +995 123 456 789 |
| Arménie | +374 | 🇦🇲 | +374 12 345678 |
| Azerbaïdjan | +994 | 🇦🇿 | +994 12 345 67 89 |

### Océanie 🏝️

| Pays | Code | Drapeau | Format exemple |
|------|------|---------|----------------|
| Australie | +61 | 🇦🇺 | +61 123 456 789 |
| Nouvelle-Zélande | +64 | 🇳🇿 | +64 12 345 6789 |

### Autres

| Pays | Code | Drapeau | Format exemple |
|------|------|---------|----------------|
| Turquie | +90 | 🇹🇷 | +90 123 456 7890 |



**Auteur :** Projet Gestion Entreprise Résidence  
**Technologies :** Laravel 10, Nuxt.js 3, TypeScript, MySQL, Tailwind CSS

## 13. Architecture technique

### Backend (Laravel)
- **Contrôleurs principaux** :
  - `AuthController` : Authentification, login/logout, vérification de token
  - `MessageController` : Gestion des messages, réactions, fichiers
  - `GuestController` : Gestion des visiteurs
  - `Api/` : Contrôleurs API REST
- **Modèles** :
  - `User`, `Admin`, `Gardien`, `Resident`, `GroupeMessage`, `Message`, `Invite`, `Visite`, etc.
- **Middleware** :
  - Authentification, gestion des rôles, CORS, etc.
- **Migrations** :
  - Unification des invitations, support UTF8MB4, etc.

### Frontend (Nuxt.js)
- **Pages** :
  - `/dashboard` : Tableau de bord (sections selon le rôle)
  - `/messages` : Messagerie de groupe
  - `/planning` : Planning des visites
  - `/profile` : Profil utilisateur
  - `/connexion`, `/Inscription` : Authentification
- **Composants** :
  - `AppHeader`, `AppFooter`, `DashboardSidebar`, `MessageComposer`, etc.
- **Stores** :
  - `auth.js` (Pinia) : gestion de l’état utilisateur, token, etc.
- **Types** :
  - `types/index.ts` : Conversation, Message, User, Member, etc.
- **Plugins** :
  - `auth.client.ts` : Initialisation de l’auth
  - `click-outside.client.js` : Gestion des clics extérieurs
  - `session-cleanup.client.ts` : Nettoyage de session

### API REST (exemples)
- `POST /api/login` : Authentification
- `GET /api/conversations` : Liste des conversations
- `POST /api/messages/{id}/reactions` : Réagir à un message
- `GET /api/visites` : Liste des visites
- `POST /api/guests` : Ajouter un visiteur

### Types principaux (TypeScript)
Voir `frontend/types/index.ts` pour la liste complète.

---

## 14. Contribution

1. Forker le repo, créer une branche, proposer une PR.
2. Respecter la structure du projet et les conventions de code (PSR-12 pour PHP, ESLint/Prettier pour JS/TS).
3. Ajouter des tests pour toute nouvelle fonctionnalité.

---

## 15. FAQ

- **Comment ajouter un nouveau rôle ?**
  - Ajouter le rôle dans la table `personnes` et adapter les contrôleurs/middleware.
- **Comment changer l’URL de l’API ?**
  - Modifier `apiBase` dans `frontend/nuxt.config.ts`.
- **Comment réinitialiser la base ?**
  - Utiliser le script SQL fourni et les scripts d’installation.

---

## 16. Liens utiles

- [Documentation Laravel](https://laravel.com/docs)
- [Documentation Nuxt.js](https://nuxt.com/docs)
- [Pinia (gestion d’état)](https://pinia.vuejs.org/)
- [Tailwind CSS](https://tailwindcss.com/)
- [TypeScript](https://www.typescriptlang.org/)

---


**Auteur :** Projet Gestion Entreprise Résidence  
**Contact :** [Votre email ou lien GitHub]
**Technologies :** Laravel 10, Nuxt.js 3, TypeScript, MySQL, Tailwind CSS

---

N’hésite pas à demander une version complète prête à coller si tu veux que je génère tout le README.md enrichi d’un coup !
