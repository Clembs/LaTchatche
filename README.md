# Chat - Projet R4.A.10

Cette application web permet de discuter instantanément avec d'autres utilisateurs.

## Commandes

Lancer le serveur PHP pour ouvrir le projet :

```
php -S localhost:[port] -t public/
```

## Structure du projet

Le projet utilise une architecture MVC (Modèle-Vue-Contrôleur) :

- `public/`: Dossier contenant les fichiers accessibles publiquement
- `public/index.php`: Routeur du projet, accède aux différentes pages
- `public/globals.css`: Feuilles de style globales
- `src/`: Dossier contenant les fichiers PHP
- `src/controllers/`: Dossier contenant les contrôleurs (logique métier)
- `src/models/`: Dossier contenant les modèles (accès à la base de données)
- `src/views/`: Dossier contenant les vues (affichage)

## Configuration

Une base de données PostgreSQL est nécessaire pour faire fonctionner le projet. Installez le module PDO PGSQL pour pouvoir utiliser PostgreSQL avec PHP.

Ensuite, exécutez le script `./sql/create-tables.sql` pour créer les tables nécessaires.

Enfin, créez un fichier `config.ini` à la racine qui contient les informations suivantes pour se connecter à la base de données :

```ini
[database]
driver = "pgsql"
host = [hôte]
port = [port]
database = [base de données]
username = [utilisateur]
password = [mot de passe]
```
