<?php ob_start(); ?>

<main>
  hello la tchatche
</main>

<?php $slot = ob_get_clean(); ?>

<?php ob_start(); ?>

<style data-file="channels/home">
  main {
    display: grid;
    place-items: center;
    height: 100dvh;
  }
</style>

<?php $head = !isset($head) ? ob_get_clean() : $head . ob_get_clean(); ?>