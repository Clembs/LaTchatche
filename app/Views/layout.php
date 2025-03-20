<!--
Fichier de mise en page avec le boilerplate HTML nécessaire pour toutes les pages de l'application.
Ce fichier est inclus dans toutes les pages de l'application.
-->

<?php
$completeTitle = "{$title} | La Tchatche";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Chargement du CSS -->
  <link rel="stylesheet" href="/styles/globals.css" />

  <!-- Ajout des tags méta, de l'OpenGraph, du titre, etc (le SEO) -->
  <title><?= $completeTitle ?></title>
  <meta name="title" content="<?= $completeTitle ?>" />
  <meta name="og:title" content="<?= $completeTitle ?>" />
  <meta name="og:type" content="website" />
  <!-- <meta property="theme-color" content="#e1ff00" /> -->
  <meta name="copyright" content=<?= date('Y') ?> />
  <meta name="robots" content="index, follow" />
  <link rel="canonical" href=<?= $_SERVER['REQUEST_URI'] ?> />
  <!-- TODO: ajouter un favicon -->

  <!-- 
  Corrigé pour le "flash of unstyled content" sur Firefox
  cf. https://stackoverflow.com/a/57888310
  -->
  <script>0</script>

  <!-- Ajout de l'HTML contenu dans la variable globale $head -->
  <?= $head ?>
</head>

<body>
  <?= $content ?>
</body>

</html>