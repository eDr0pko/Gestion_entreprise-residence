# 🏢 Backend Client - Gestion Entreprise Résidence

Backend principal pour la gestion d'entreprise résidence avec gestion des clients, badges, visiteurs et résidents.

## 🗄️ Base de données

Ce repository inclut le fichier `final.sql` contenant le schéma complet de la base de données avec :
- Tables des utilisateurs et rôles
- Gestion des badges et accès
- Système de visiteurs
- Logs et audit trails
- Données de test

## 🚀 Installation

### Prérequis
- PHP 8.1+
- Composer
- MySQL 8.0+
- Node.js 18+ (pour les assets)

### Configuration

1. **Clonage et installation**
```bash
git clone https://github.com/neostart-tech/backend-client.git
cd backend-client
composer install
npm install
```

2. **Configuration de l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configuration de la base de données**
Éditez le fichier `.env` :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_residence_client
DB_USERNAME=votre_username
DB_PASSWORD=votre_password
```

4. **Import de la base de données**
```bash
# Créer la base de données
mysql -u root -p -e "CREATE DATABASE gestion_residence_client;"

# Importer le schéma
mysql -u root -p gestion_residence_client < final.sql
```

## 🏃‍♂️ Lancement

### Serveur de développement
```bash
php artisan serve --port=8000
```

Le serveur sera accessible sur `http://localhost:8000`

### Compilation des assets
```bash
npm run dev        # Mode développement
npm run build      # Mode production
npm run watch      # Mode watch
```

## 📁 Structure

```
backend-client/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php
│   │   ├── BadgeController.php
│   │   ├── ResidentController.php
│   │   └── VisitorController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Badge.php
│   │   └── Resident.php
│   └── Middleware/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
└── final.sql         # Schéma de base de données
```

## 🔧 API Endpoints

### Authentification
- `POST /api/auth/login` - Connexion utilisateur
- `POST /api/auth/logout` - Déconnexion
- `POST /api/auth/refresh` - Rafraîchissement token
- `GET /api/auth/me` - Profil utilisateur

### Gestion des Badges
- `GET /api/badges` - Liste des badges
- `POST /api/badges` - Création d'un badge
- `PUT /api/badges/{id}` - Modification d'un badge
- `DELETE /api/badges/{id}` - Suppression d'un badge
- `POST /api/badges/{id}/activate` - Activation
- `POST /api/badges/{id}/deactivate` - Désactivation

### Gestion des Résidents
- `GET /api/residents` - Liste des résidents
- `POST /api/residents` - Ajout d'un résident
- `PUT /api/residents/{id}` - Modification
- `DELETE /api/residents/{id}` - Suppression

### Gestion des Visiteurs
- `GET /api/visitors` - Liste des visiteurs
- `POST /api/visitors` - Enregistrement visite
- `PUT /api/visitors/{id}` - Modification visite

### Statistiques
- `GET /api/stats/dashboard` - Statistiques globales
- `GET /api/stats/badges` - Statistiques badges
- `GET /api/stats/visitors` - Statistiques visiteurs

## 🔒 Sécurité

- Authentification JWT avec Sanctum
- Validation CSRF
- Rate limiting configuré
- Sanitisation des données
- Rôles et permissions (Admin, Gardien, Résident)

## 👤 Comptes de test

Après import de `final.sql` :

```
Admin:
Email: admin@residence.com
Password: 1234

Gardien:
Email: gardien@residence.com  
Password: 1234

Résident:
Email: resident@residence.com
Password: 1234
```

## 📝 Environnements

### Développement
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3001
```

### Production
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.votre-domaine.com
FRONTEND_URL=https://votre-domaine.com
```

## 🗄️ Schéma de Base de Données

Le fichier `final.sql` contient :
- **Tables principales** : users, badges, residents, visitors
- **Tables de liaison** : user_roles, badge_permissions
- **Tables de logs** : activity_logs, access_logs
- **Données de test** : Utilisateurs et permissions de base

## 🤝 Contribution

1. Fork le repository
2. Créez une branche feature (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 🏢 NeoStart.tech

Développé par [NeoStart.tech](https://neostart.tech) - Solutions innovantes pour entreprises modernes.

---
*Dernière mise à jour : Août 2025*
