<?php ob_start(); ?>

<main>
  Coucou le monde (et Gérald 👀)
</main>

<?php $content = ob_get_clean(); ?>

<?php ob_start(); ?>

<style data-file="home">
  main {
    display: grid;
    place-items: center;
    height: 100dvh;
  }
</style>

<?php $head = !isset($head) ? ob_get_clean() : $head . ob_get_clean(); ?>