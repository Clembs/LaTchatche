## Fonctionnel

### Base de données

- [x] Une table gère le stockage des messages
- [x] La table contient déjà quelques enregistrements (au moins 20)
- [x] L'ensemble des messages est sauvegardé dans la base de données

### Enregistrement des messages

- [x] Un script (par exemple enregistrer.php) permet l'enregistrement d'un message dans la table
- [x] Le script d'enregistrement d'un message est appelé via une requête Ajax asynchrone
- [x] L'auteur et le contenu d'un message sont récupérés via des paramètres de la requête Ajax
- [x] L'estampille horaire du message est géré soit par le script d'enregistrement, soit par la base de données

### Récupération des messages

- [x] Un script (par exemple recuperer.php) permet l'obtention des messages
- [x] Seuls les 10 messages les plus récents sont affichés lors de l'arrivée de l'utilisateur dans la salle de chat
- [x] Le script d'obtention des messages est appelé via une requête Ajax asynchrone
- [x] Les messages sont affichés sans doublons
- [x] Les messages sont rafraîchis sans rechargement de la page
- [x] L'auteur, la date et l'heure de publication des messages sont affichés, en plus du contenu du message

### IHM

- [x] Un message est envoyé lors de l'appui de la touche Entrée
- [x] La zone de saisie d'un message se vide lorsqu'un message est envoyé
- [x] L'utilisateur ne doit saisir son pseudo qu'une seule fois

### Bonus

- [x] L'utilisateur doit se connecter à l'application (présence d'une page d'authentification et d'une page de création de compte)
- [x] L'application gère différentes salles de messagerie

## Technique

### Ergonomie

- [x] Une ou plusieurs feuilles de style permettent la mise en forme de l'application
- [ ] L'application est ergonomique

### Sécurité

- [x] L'accès à la base est sécurisé via un compte autre que root, et un mot de passe est requis pour ce compte
- [x] Les accès à la base de données sont sécurisés à l’aide des méthodes prepare et execute

### Code source

- [x] Le code source est suffisament commenté et correctement indenté
- [x] La bibliothèque jquery est utilisée
- [x] Les dossiers, fichiers, fonctions, classes, attributs, méthodes, suivent une nomenclature claire et explicite
