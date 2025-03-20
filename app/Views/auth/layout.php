<?php ob_start(); ?>

<main>

  <div class="contents">
    <?= $slot ?>
  </div>

</main>

<?php $content = ob_get_clean(); ?>


<?php ob_start(); ?>

<style data-file="auth/layout">
  main {
    display: grid;
    place-items: center;
    height: 100vh;
  }

  .contents {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: flex-end;

    border: 1px solid var(--color-outline);
    padding: 2rem;
    border-radius: 2rem;

    max-width: 400px;
  }

  form {
    display: contents;
  }

  .header {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  h1 {
    font-weight: 500;
    font-size: 1.5rem;
  }
</style>

<?php $head .= ob_get_clean(); ?>