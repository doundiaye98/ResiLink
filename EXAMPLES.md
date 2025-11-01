# Exemples de données pour ResiLink

Ce fichier décrit comment importer des exemples de données dans votre installation ResiLink.

## 🚀 Import rapide

Exécutez simplement :

```bash
php import_examples.php
```

## 📊 Contenu des exemples

### Utilisateurs créés

Tous les comptes utilisent le mot de passe : **admin123**

| Username | Nom | Appartement | Email |
|----------|-----|-------------|-------|
| marie_dupont | Marie Dupont | A12 | marie.dupont@example.com |
| pierre_martin | Pierre Martin | B05 | pierre.martin@example.com |
| sophie_bernard | Sophie Bernard | C20 | sophie.bernard@example.com |
| lucas_roux | Lucas Roux | D08 | lucas.roux@example.com |
| emma_leclerc | Emma Leclerc | E15 | emma.leclerc@example.com |

### Posts créés

**Posts Généraux (2)**
- Bienvenue dans la résidence
- Réunion du conseil

**Posts de Vente (3)**
- Machine à laver (250€)
- Canapé convertible (350€)
- Vélo électrique (800€)

**Événements (3)**
- Fête de Noël (20 décembre, Salle commune)
- Petit déjeuner partagé (10 novembre, Jardin)
- Nettoyage collectif (25 novembre, Espaces communs)

**Problèmes signalés (4)**
- Lampe cassée dans l'ascenseur
- Chaudière bruyante
- Porte qui grince
- Ascenseur en panne

### Interactions

- **10 commentaires** sur les différents posts
- **19 likes** répartis sur les publications

## 🎯 Utilisation

1. Connectez-vous avec n'importe quel compte de test
2. Explorez les différents types de posts
3. Ajoutez des commentaires et likes
4. Testez les filtres par type
5. Connectez-vous en admin pour voir le panneau de modération

## 🔄 Réimporter

Si vous souhaitez réinitialiser les exemples :

```bash
php import_examples.php
```

Le script demandera confirmation si des utilisateurs existent déjà.

## 🗑️ Réinitialiser complètement

Pour tout effacer et recommencer :

1. Dans phpMyAdmin, supprimez toutes les données des tables :
   ```sql
   DELETE FROM notifications;
   DELETE FROM likes;
   DELETE FROM comments;
   DELETE FROM posts;
   DELETE FROM users WHERE username != 'admin';
   ```

2. Puis relancez `import_examples.php`

## ⚠️ Sécurité

**IMPORTANT** : Après avoir importé les exemples, supprimez le fichier `import_examples.php` pour des raisons de sécurité :

```bash
rm import_examples.php
# ou sur Windows :
del import_examples.php
```

---

**Bon test avec ResiLink ! 🎉**

---

🌐 **Développé par [S2NTech](https://s2ntech.com)** - Solutions numériques innovantes

