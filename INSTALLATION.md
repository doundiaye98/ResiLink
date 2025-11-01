# Guide d'installation - ResiLink 🚀

## Prérequis

- WAMP/XAMPP installé et fonctionnel
- PHP 7.4 ou supérieur
- MySQL/MariaDB activé
- Navigateur web moderne

## Installation en 5 étapes

### 1. Vérifier WAMP
Assurez-vous que WAMP est démarré et que les services Apache et MySQL sont actifs (icône verte dans la barre des tâches).

### 2. Importer la base de données

1. Ouvrez phpMyAdmin : http://localhost/phpmyadmin
2. Cliquez sur "Nouvelle base de données"
3. Nommez-la `resilink` et sélectionnez `utf8mb4_unicode_ci`
4. Cliquez sur "Créer"
5. Sélectionnez la base de données `resilink` à gauche
6. Cliquez sur l'onglet "SQL" en haut
7. Ouvrez le fichier `database/schema.sql` dans un éditeur de texte
8. Copiez tout son contenu
9. Collez-le dans la zone de texte de phpMyAdmin
10. Cliquez sur "Exécuter"

✅ La base de données est créée avec toutes les tables et un compte admin !

### 3. Vérifier les permissions

Les dossiers suivants doivent être accessibles en écriture :
- `assets/uploads/posts/`
- `assets/uploads/avatars/`

Sur WAMP, ces dossiers devraient fonctionner automatiquement. Si vous avez des erreurs d'upload :
1. Clic droit sur le dossier → Propriétés
2. Onglet "Sécurité"
3. Modifier les permissions pour donner accès complet à "Utilisateurs"

### 4. Lancer l'installation

1. Ouvrez votre navigateur
2. Allez sur : http://localhost/app/install.php
3. Vérifiez que toutes les vérifications passent ✅

### 5. Se connecter

1. Allez sur : http://localhost/app/
2. Cliquez sur "Connexion"
3. Utilisez :
   - **Utilisateur** : `admin`
   - **Mot de passe** : `admin123`

🎉 **ResiLink est prêt !**

## Accès aux pages

- **Page d'accueil** : http://localhost/app/
- **Connexion** : http://localhost/app/login.php
- **Inscription** : http://localhost/app/register.php
- **Panneau admin** : http://localhost/app/admin.php

## Sécurité

⚠️ **IMPORTANT** : Après la première connexion admin, supprimez le fichier `install.php` pour des raisons de sécurité.

## Changement du mot de passe admin

1. Connectez-vous avec `admin / admin123`
2. Allez dans votre profil
3. Changez le mot de passe
4. Sauvegardez

## Configuration personnalisée

Pour changer les paramètres de connexion à la base de données, modifiez le fichier `config/database.php` :

```php
define('DB_HOST', 'localhost');      // Hôte MySQL
define('DB_USER', 'root');           // Utilisateur
define('DB_PASS', '');               // Mot de passe
define('DB_NAME', 'resilink');       // Nom de la base
```

## Support

Si vous rencontrez des problèmes :

1. Vérifiez que WAMP est bien démarré
2. Vérifiez les logs d'erreur PHP dans WAMP
3. Vérifiez que la base de données existe
4. Vérifiez les permissions des dossiers d'upload

## Fonctionnalités disponibles

✅ Création de compte utilisateur
✅ Publication de posts (Général, Vente, Événement, Problème)
✅ Ajout de commentaires et likes
✅ Upload d'images
✅ Notifications en temps réel
✅ Filtrage par type de post
✅ Pagination
✅ Gestion de profil
✅ Panneau de modération admin
✅ Interface responsive

---

**Bon réseau avec ResiLink ! 🌆**

---

🌐 **Développé par [S2NTech](https://s2ntech.com)** - Solutions numériques innovantes

