<?php $title = '404'; ?>

<?php ob_start(); ?>

<main>
  <h1>404</h1>

  <p>Cette page n'a pas pu être trouvée.</p>

  <a class="button primary" href="/">
    Retour à l'accueil
  </a>
</main>

<?php $content = ob_get_clean(); ?>

<?php ob_start(); ?>

<style>
  main {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
  }

  h1 {
    font-size: 4rem;
    font-weight: bold;
    margin-bottom: 0;
  }
</style>

<?php $head .= ob_get_clean(); ?>

<?php require 'layout.php'; ?>