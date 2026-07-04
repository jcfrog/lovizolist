# Lovizolist

Liste de courses partagée pour la famille : plusieurs listes, des articles qu'on ajoute et qu'on coche, synchronisés entre tous les téléphones qui ouvrent la page (rafraîchissement automatique).

Pensée pour tourner sur un réseau privé (maison) : il n'y a **aucune authentification**, l'accès à l'URL suffit.

## Stack

- PHP (7.3+) sans framework, API JSON simple dans `api/`
- MySQL / MariaDB
- Frontend en HTML + [Alpine.js](https://alpinejs.dev/) (chargé depuis un CDN), aucune étape de build

## Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/jcfrog/lovizolist.git
cd lovizolist
```

### 2. Créer la base de données

```sql
CREATE DATABASE lovizolist CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'lovizolist_app'@'localhost' IDENTIFIED BY 'un-mot-de-passe';
GRANT ALL PRIVILEGES ON lovizolist.* TO 'lovizolist_app'@'localhost';
```

Puis importer le schéma :

```bash
mysql lovizolist < sql/schema.sql
```

### 3. Configurer l'application

```bash
cp config.example.php config.php
```

Éditer `config.php` et renseigner les identifiants de la base créée à l'étape précédente. `config.php` n'est pas versionné (voir `.gitignore`), les identifiants réels restent locaux.

### 4. Servir l'application

N'importe quel serveur PHP+MySQL fait l'affaire (Apache, nginx+php-fpm...). La racine du site doit pointer sur le dossier du dépôt.

Pour un test rapide en local :

```bash
php -S localhost:8000
```

Puis ouvrir `http://localhost:8000/index.php`.

## Structure

```
index.php            Page principale (liste des listes + contenu d'une liste)
api/lists.php        API JSON : lister / créer / supprimer une liste
api/items.php        API JSON : lister / ajouter / cocher / supprimer un article
includes/db.php       Connexion PDO à la base
assets/js/app.js      Logique Alpine.js (appels API, état de l'UI)
assets/css/style.css  Styles
sql/schema.sql        Schéma de base pour une installation neuve
sql/migration_*.sql   Migrations à appliquer sur une base existante
```

## Mises à jour

Sur le serveur où l'app est installée :

```bash
git pull
```

Vérifier au passage s'il existe un nouveau fichier dans `sql/migration_*.sql` à appliquer sur la base.

## Icône iOS

Depuis Safari sur iPhone, "Partager" → "Sur l'écran d'accueil" utilise `assets/img/apple-touch-icon.png` comme icône de raccourci.
