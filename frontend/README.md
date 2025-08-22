# 🌐 Frontend - Gestion Entreprise Résidence

Interface utilisateur moderne construite avec Nuxt.js pour l'application de gestion d'entreprise résidence.

## ✨ Fonctionnalités

- 🎨 **Interface moderne** avec Tailwind CSS et thème sombre/clair
- 🌍 **Multilingue** (Français, Anglais, Chinois)
- 📱 **Responsive Design** adaptatif
- 🔐 **Authentification JWT** avec gestion des rôles
- 📊 **Dashboard** avec statistiques en temps réel
- 🎫 **Gestion des badges** et contrôles d'accès
- 👥 **Gestion des résidents** et visiteurs
- 🔔 **Notifications** en temps réel
- ♿ **Accessibilité** WCAG compliant

## 🏗️ Architecture API

Le frontend communique uniquement avec le backend-client (`http://localhost:8000/api`).
Pour les endpoints propriétaires NHS, il passe par le proxy `/nhs` du backend-client, qui relaie vers le backend-nhs.

```
Frontend → backend-client (/api, /nhs) → backend-nhs
```

## 🚀 Installation

### Prérequis
- Node.js 18+
- NPM ou Yarn
- Backend API en fonctionnement

### Configuration

1. **Clonage et installation**
```bash
git clone https://github.com/neostart-tech/frontend.git
cd frontend
npm install

```

2. **Configuration de l'environnement**
```bash
cp .env.example .env
```

3. **Variables d'environnement**
Éditez le fichier `.env` :
```env
# API URLs
NUXT_PUBLIC_API_BASE_URL=http://localhost:8000/api
NUXT_PUBLIC_NHS_API_BASE_URL=http://localhost:8001/api

# App Configuration
NUXT_PUBLIC_APP_NAME=Gestion Résidence
NUXT_PUBLIC_DEFAULT_LOCALE=fr

# Development
NUXT_PUBLIC_APP_ENV=development
```

## 🏃‍♂️ Lancement

### Serveur de développement
```bash
npm run dev
```

Le serveur sera accessible sur `http://localhost:3000`

### Compilation pour production
```bash
npm run build      # Build de production
npm run preview    # Preview du build
```

## 🎨 Système de Design

### Thème
- **Mode clair/sombre** avec toggle automatique
- **Couleurs principales** : Bleu (#0097b2) et dérivés
- **Police** : Inter (système par défaut)
- **Icônes** : Système d'icônes unifié

## 🔐 Authentification

### Système JWT
- Tokens stockés en localStorage
- Refresh automatique
- Gestion des rôles côté frontend

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
