# Guide de démarrage rapide - ResiLink 🚀

## ✅ Déjà installé et configuré !

Votre installation ResiLink est complète avec :
- ✅ Base de données créée
- ✅ 6 utilisateurs avec avatars
- ✅ 12+ posts avec images
- ✅ Commentaires et likes
- ✅ Tous les exemples chargés

## 🌐 Accès à l'application

**URL principale :** http://localhost/app/

## 👥 Comptes disponibles

Tous les comptes utilisent le mot de passe : **admin123**

| Compte | Rôle | Description |
|--------|------|-------------|
| **admin** | Administrateur | Gestion de la plateforme |
| **marie_dupont** | Utilisateur | Marie Dupont, Appartement A12 |
| **pierre_martin** | Utilisateur | Pierre Martin, Appartement B05 |
| **sophie_bernard** | Utilisateur | Sophie Bernard, Appartement C20 |
| **lucas_roux** | Utilisateur | Lucas Roux, Appartement D08 |
| **emma_leclerc** | Utilisateur | Emma Leclerc, Appartement E15 |

## 🎯 Fonctionnalités à tester

### Pour tous les utilisateurs
1. **Se connecter** avec n'importe quel compte
2. **Naviguer** sur la page d'accueil
3. **Voir** les différents types de posts :
   - 📝 Général (annonces)
   - 💰 Vente (objets)
   - 🎉 Événements (dates/lieux)
   - ⚠️ Problèmes (signalements)
4. **Cliquer** sur un post pour voir les détails
5. **Commenter** et **liker** des posts
6. **Ajouter** un nouveau post
7. **Modifier** son profil et changer d'avatar
8. **Utiliser** les filtres par type

### Pour l'administrateur
1. **Se connecter** avec le compte `admin`
2. **Accéder** au panneau de modération
3. **Voir** les statistiques générales
4. **Modérer** les publications :
   - Supprimer un post
   - Fermer un post
   - Marquer un problème comme résolu

## 🔄 Si vous voulez réinitialiser

Exécutez :
```bash
php import_examples.php
```

Puis récupérez les avatars et images :
```bash
php setup_complete.php
```

## 🧹 Nettoyage (important !)

Pour des raisons de sécurité, supprimez ces fichiers après utilisation :

```bash
# Fichiers à supprimer
rm install.php
rm import_examples.php
rm setup_complete.php
```

**Sur Windows :**
```cmd
del install.php
del import_examples.php
del setup_complete.php
```

## 📂 Fichiers importants

### Pages principales
- `index.php` - Page d'accueil avec fil d'actualité
- `login.php` - Connexion
- `register.php` - Inscription
- `profile.php` - Profil utilisateur
- `admin.php` - Panneau administrateur
- `create_post.php` - Créer un post
- `post_detail.php` - Voir un post

### Documentation
- `README.md` - Documentation principale
- `INSTALLATION.md` - Guide d'installation détaillé
- `EXAMPLES.md` - Guide des exemples
- `FEATURES.md` - Liste des fonctionnalités
- `QUICKSTART.md` - Ce fichier

### Configuration
- `config/database.php` - Configuration MySQL
- `database/schema.sql` - Structure de la base de données
- `.htaccess` - Sécurité Apache

## ❓ Problèmes courants

### Page blanche ou erreur 500
→ Vérifiez que WAMP est démarré et que MySQL fonctionne

### Erreur de connexion MySQL
→ Vérifiez `config/database.php` (host, user, password)

### Images ne s'affichent pas
→ Vérifiez les permissions des dossiers `assets/uploads/`

### Compte admin ne fonctionne pas
→ Réexécutez `setup_complete.php`

## 📊 Statistiques actuelles

Votre installation contient :
- **6 utilisateurs** (dont 1 admin)
- **12+ posts** variés
- **Commentaires et likes**
- **Avatars et images** pour tous

## 🎉 C'est prêt !

**Amusez-vous bien avec ResiLink !** 🌆

Pour toute question, consultez la documentation complète dans `README.md`

---

🌐 **Développé par [S2NTech](https://s2ntech.com)** - Solutions numériques innovantes

