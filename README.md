# gestion_stage

Projet de gestion de stages — application PHP légère pour la gestion des utilisateurs,
sessions, tâches, rapports et affectations.

Table des matières
- Description
- Fonctionnalités
- Architecture & dépendances
- Prérequis
- Installation locale
- Configuration
- Base de données
- Déploiement
- Sécurité
- Structure du projet
- Contribution
- Licence
- Contact

Description
-----------
`gestion_stage` est une application PHP monolithique destinée à gérer le cycle
de vie des stagiaires : enregistrement, affectation aux sessions, dépôt de
rapports, suivi des tâches, évaluations et gestion administrative. L'application
est conçue pour être simple à déployer sur un serveur LAMP/LEMP ou via Laragon
en local.

Fonctionnalités
---------------
- Gestion des utilisateurs (administrateurs, encadrants, stagiaires)
- Création et gestion de sessions de formation
- Affectation manuelle et en masse des stagiaires
- Gestion des tâches (création, affectation, validation, clôture)
- Dépôt et gestion de rapports (upload PDF/Docs)
- Tableau de bord et statistiques basiques
- Authentification, rôles et permissions simples

Architecture & dépendances
--------------------------
- PHP (7.4+ recommandé, 8.0+ compatible)
- Serveur web : Apache ou Nginx
- Base de données : MySQL / MariaDB
- Gestion des dépendances : Composer (le dossier `vendor/` est inclus)
- Bibliothèques notables : `dompdf` pour génération de PDF

Prérequis
---------
- PHP 7.4 ou supérieur avec extensions `pdo_mysql`, `gd`, `mbstring` activées
- MySQL / MariaDB
- Composer (pour installer ou mettre à jour les dépendances)
- Un serveur local (Laragon, XAMPP, MAMP) ou un hébergement LAMP/LEMP

Installation locale
--------------------
1. Cloner le dépôt dans le répertoire racine de votre serveur web :

	git clone <repo-url> gestion_stage

2. Installer les dépendances PHP (si vous modifiez `composer.json`) :

	composer install

3. Assurez-vous que les dossiers d'upload ont les permissions en écriture :

	- `uploads/cv/`
	- `uploads/rapports/`

4. Copier le fichier de configuration DB si nécessaire et adapter les valeurs
	de connexion (voir section Configuration).

Configuration
-------------
Configurer la connexion à la base de données dans `config/db.php`.

Exemple (structure attendue) :

```php
<?php
$host = '127.0.0.1';
$db   = 'gestion_stage';
$user = 'db_user';
$pass = 'db_pass';
$charset = 'utf8mb4';
// Adapter selon l'implémentation du fichier existant
```

Base de données
---------------
L'application attend plusieurs tables (utilisateurs, sessions, tâches, rapports,
affectations...). Si le projet ne contient pas de script SQL d'initialisation,
créez une base et importez un schéma minimal adapté à vos besoins.

Exemple de commandes MySQL :

```sql
CREATE DATABASE gestion_stage CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Puis créer les tables nécessaires ou importer un fichier SQL:
-- mysql -u root -p gestion_stage < db/schema.sql
```

Usage
-----
1. Démarrer votre serveur local (Laragon/Apache + PHP).
2. Placer le projet comme site dans Laragon ou dans le dossier `www`
3. Ouvrir le navigateur et accéder à l'URL (ex. `http://localhost/gestion_stage/`)
4. Créer un compte administrateur depuis l'interface ou via un script d'insert
	SQL pour remplir la table `users`.

Déploiement
-----------
- Veiller à désactiver l'affichage des erreurs en production (`display_errors = Off`).
- Configurer HTTPS (certificat TLS).
- Protéger `config/` et autres fichiers sensibles par des règles serveur
  (ex: `.htaccess` ou configuration Nginx).
- S'assurer des permissions minimales sur les uploads (pas d'exécution).

Sécurité
--------
- Utiliser des requêtes préparées (PDO) pour éviter les injections SQL.
- Valider et normaliser toutes les entrées utilisateurs côté serveur.
- Restreindre les types et tailles des fichiers uploadés et scanner si possible.
- Mettre en place des sauvegardes régulières de la base de données.

Structure du projet (vue rapide)
-------------------------------
- `assets/` : CSS, JS et images
- `config/` : configuration de la base de données
- `includes/` : en-tête, pied de page et contrôles d'accès
- `uploads/` : dossiers d'upload `cv/` et `rapports/`
- `vendor/` : dépendances Composer
- fichiers PHP principaux en racine : gestion des sessions, taches, utilisateurs

Contribution
------------
Les contributions sont bienvenues. Ouvrez une issue pour discuter d'une
nouvelle fonctionnalité ou d'un bug, puis soumettez une pull request.

Checklist avant PR :
- Respecter le style PHP du projet
- Éviter de casser la compatibilité existante
- Fournir une description claire des changements

Licence
-------
Précisez ici la licence du projet (MIT, GPL, propriétaire, etc.).

Contact
-------
Pour toute question, ouvrir une issue dans le dépôt ou contacter l'auteur
du projet via les coordonnées internes.

Annexes
-------
- Pour faciliter la mise en route, il est recommandé d'ajouter un script
  `db/schema.sql` et un fichier `env.example` avec les paramètres de
  configuration à copier en `config/db.php`.

----

Ce README offre un guide pragmatique pour installer, configurer et déployer
`gestion_stage`. Si vous voulez, je peux :
- générer un `db/schema.sql` de démarrage basique,
- ajouter un `env.example` et un script d'installation,
- ou traduire et détailler des parties spécifiques (sécurité, tests).
