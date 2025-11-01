# ResiLink 🌆

**ResiLink** est un mini-réseau social destiné aux résidents d'une résidence. Cette plateforme permet aux habitants d'échanger, vendre des objets, organiser des événements et signaler des problèmes dans l'immeuble.

## 🚀 Fonctionnalités

### Pour tous les utilisateurs
- ✅ **Création de profil** avec photo, numéro d'appartement, téléphone
- ✅ **Publication de posts** avec images (Général, Vente, Événement, Problème)
- ✅ **Système de commentaires** pour discuter sur les publications
- ✅ **Système de likes** pour interagir avec les posts
- ✅ **Notifications en temps réel** (nouvelles publications, likes, commentaires)
- ✅ **Filtres par type** de publication
- ✅ **Paginations** pour une navigation fluide

### Pour les administrateurs
- ✅ **Panneau de modération** complet
- ✅ **Gestion des publications** (supprimer, fermer, marquer comme résolu)
- ✅ **Statistiques** de la plateforme
- ✅ **Interface dédiée** à la modération

## 🛠️ Technologies utilisées

- **PHP** - Backend et logique serveur
- **MySQL** - Base de données
- **Bootstrap 5** - Interface responsive et moderne
- **Bootstrap Icons** - Icônes vectorielles
- **CSS personnalisé** - Styles supplémentaires

## 📋 Prérequis

- WAMP/XAMPP/LAMP avec PHP 7.4+
- MySQL/MariaDB
- Navigateur web moderne

## 🔧 Installation

> 📖 **Guide complet d'installation** : Voir [INSTALLATION.md](INSTALLATION.md) pour des instructions détaillées

### 1. Cloner le projet

```bash
cd C:\wamp\www\app
# Les fichiers sont déjà là
```

### 2. Créer la base de données

1. Ouvrir phpMyAdmin (http://localhost/phpmyadmin)
2. Exécuter le contenu du fichier `database/schema.sql`
3. La base de données `resilink` sera créée avec toutes les tables nécessaires

### 3. Configuration

Le fichier `config/database.php` contient déjà les configurations par défaut pour WAMP:
- Host: localhost
- User: root
- Password: (vide)
- Database: resilink

Modifiez si nécessaire selon votre environnement.

### 4. Permissions des dossiers

Assurez-vous que les dossiers suivants sont accessibles en écriture:
- `assets/uploads/posts/`
- `assets/uploads/avatars/`

Sur Windows avec WAMP, ces dossiers devraient être accessibles par défaut.

## 👤 Compte administrateur par défaut

Un compte administrateur est créé automatiquement :

- **Utilisateur:** `admin`
- **Mot de passe:** `admin123`
- **Email:** `admin@resilink.fr`

⚠️ **Important:** Changez ce mot de passe après la première connexion !

## 📁 Structure du projet

```
app/
├── api/                    # APIs pour AJAX
│   ├── toggle_like.php
│   ├── notifications.php
│   └── mark_notification_read.php
├── assets/
│   ├── css/
│   │   └── style.css       # Styles personnalisés
│   └── uploads/            # Images uploadées
│       ├── posts/
│       └── avatars/
├── config/
│   └── database.php        # Configuration BDD
├── database/
│   └── schema.sql          # Script de création BDD
├── includes/
│   ├── header.php          # En-tête + navbar
│   └── footer.php          # Pied de page
├── admin.php               # Panel admin
├── index.php               # Page d'accueil
├── login.php               # Connexion
├── register.php            # Inscription
├── logout.php              # Déconnexion
├── profile.php             # Profil utilisateur
├── create_post.php         # Créer un post
├── edit_post.php           # Modifier un post
├── post_detail.php         # Détail d'un post
└── README.md               # Documentation
```

## 🎨 Pages disponibles

### Pages publiques
- **index.php** - Fil d'actualité principal avec filtres
- **login.php** - Connexion
- **register.php** - Inscription

### Pages utilisateur (connecté)
- **create_post.php** - Créer une nouvelle publication
- **post_detail.php** - Voir les détails d'un post avec commentaires
- **edit_post.php** - Modifier un post existant
- **profile.php** - Gérer son profil

### Pages admin
- **admin.php** - Panneau de modération

## 📝 Types de posts

1. **Général** - Discussion libre
2. **Vente** - Vendre des objets avec prix
3. **Événement** - Organiser un événement avec date/lieu
4. **Problème** - Signaler un problème dans l'immeuble

## 🔔 Système de notifications

Les utilisateurs reçoivent des notifications pour:
- Nouveaux posts dans le fil d'actualité
- Likes sur leurs publications
- Commentaires sur leurs publications

Les notifications sont affichées en temps réel dans la navbar et se mettent à jour toutes les 30 secondes.

## 🎯 Fonctionnalités avancées

### Modération admin
- Vue d'ensemble des statistiques
- Liste complète de tous les posts
- Actions: Supprimer, Fermer, Marquer comme résolu
- Filtrage et recherche possibles (extension future)

### Sécurité
- Hashage des mots de passe (bcrypt)
- Protection CSRF (à améliorer)
- Validation côté serveur
- Escaping XSS avec `htmlspecialchars()`
- Permissions et vérifications d'accès

## 🚀 Utilisation

1. **Démarrer WAMP** et s'assurer que MySQL est actif
2. **Ouvrir** http://localhost/app dans un navigateur
3. **S'inscrire** ou se connecter avec le compte admin
4. **Créer** des posts, commenter, liker !

## 🔮 Améliorations futures possibles

- [ ] Système de messages privés
- [ ] Recherche avancée
- [ ] Modération des commentaires
- [ ] Système de signalement
- [ ] Historique des modifications
- [ ] Export des données
- [ ] API REST complète
- [ ] Application mobile

## 👨‍💻 Développement

### Ajouter une nouvelle fonctionnalité

1. Créer/modifier les fichiers PHP nécessaires
2. Mettre à jour la base de données si besoin (`schema.sql`)
3. Ajouter les routes dans la navbar si nécessaire
4. Tester sur différents navigateurs

### Debug

Activez l'affichage des erreurs PHP dans `config/database.php` si nécessaire:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

## 📄 Licence

Ce projet est fourni à des fins éducatives et de démonstration.

## 👥 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à proposer des améliorations.

---

**Créé avec ❤️ pour connecter les résidents**

---

🌐 **Développé par [S2NTech](https://s2ntech.com)** - Solutions numériques innovantes

