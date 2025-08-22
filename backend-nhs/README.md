# 🏥 Backend NHS - Gestion Entreprise Résidence

Backend dédié au système NHS (National Health Service) de l'application de gestion d'entreprise résidence.

## 🚀 Installation

### Prérequis
- PHP 8.1+
- Composer
- MySQL 8.0+
- Node.js 18+ (pour les assets)

### Configuration

1. **Installation des dépendances**
```bash
composer install
npm install
```

2. **Configuration de l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configuration de la base de données**
Éditez le fichier `.env` avec vos paramètres de base de données :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_residence_nhs
DB_USERNAME=votre_username
DB_PASSWORD=votre_password
```

4. **Migration et seeding**
```bash
php artisan migrate
php artisan db:seed
```

## 🏃‍♂️ Lancement

### Serveur de développement
```bash
php artisan serve --port=8001
```

Le serveur sera accessible sur `http://localhost:8001`

### Compilation des assets
```bash
npm run dev        # Mode développement
npm run build      # Mode production
npm run watch      # Mode watch
```

## 📁 Structure

```
backend-nhs/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   ├── Mail/
│   └── Providers/
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
├── routes/
└── storage/
```

## 🔧 API Endpoints

### Authentification
- `POST /api/auth/login` - Connexion
- `POST /api/auth/logout` - Déconnexion
- `POST /api/auth/refresh` - Rafraîchissement du token

### NHS Spécifique
- `GET /api/nhs/patients` - Liste des patients
- `POST /api/nhs/patients` - Création d'un patient
- `GET /api/nhs/appointments` - Rendez-vous médicaux
- `POST /api/nhs/medical-records` - Dossiers médicaux

## 🔒 Sécurité

- Authentification JWT
- Validation CSRF
- Rate limiting
- Sanitisation des données

## 📝 Environnements

### Développement
```bash
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8001
```

### Production
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
```

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
