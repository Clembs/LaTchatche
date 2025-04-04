# La Tchatche - Projet R4.A.10

Cette application web permet de discuter instantanément avec d'autres utilisateurs.

## Accéder en ligne

https://latchatche.clembs.com

Vous pouvez vous créer un compte puis créer un salon où accéder aux salons publics d'autres utilisateurs.


## Prérequis

- [PHP](https://www.php.net/downloads.php)
- [Composer](https://getcomposer.org/download/)

## Installation

Installer les dépendances du projet et préparer l'environnement de développement :

```
composer install
```

Lancer le serveur PHP :

```
php -S localhost:[port] -t public/
```

## Fonctionnalités & Grille d'évaluation

L'ensemble des fonctionnalités implémentées dans l'application se trouvent dans le fichier [grille-d'évaluation.md](./grille-d´évaluation.md).

## Structure du projet

Le projet utilise une architecture MVC (Modèle-Vue-Contrôleur) :

- `public/`: Dossier contenant les fichiers accessibles publiquement
- `public/index.php`: Routeur du projet, accède aux différentes pages
- `public/globals.css`: Feuilles de style globales
- `app/`: Dossier contenant les fichiers PHP
- `app/Controllers/`: Dossier contenant les contrôleurs (logique métier)
- `app/Models/`: Dossier contenant les modèles (accès à la base de données)
- `app/Views/`: Dossier contenant les vues (affichage)

## Configuration

Une base de données MySQL est nécessaire pour faire fonctionner le projet. Installez ou activez le module PDO MySQL pour pouvoir l'utiliser avec PHP.

Ensuite, exécutez le script `./sql/schema.sql` pour créer les tables nécessaires.

Enfin, créez un fichier `config.ini` à la racine qui contient les informations suivantes pour se connecter à la base de données :

```ini
[database]
driver = "pgsql"
host = "URL de l'hôte"
port = numéro de port
database = "nom de la base de données"
username = "nom d'utilisateur"
password = "mot de passe"
```

Par exemple :

```ini
[database]
driver = "mysql"
host = "localhost"
port = 3306
database = "latchatche"
username = "root"
password = "ontchatchebeaucoup"
```
