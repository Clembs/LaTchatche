<dialog id="create-channel-dialog" data-open="<?= isset($_GET['error']) ? 'true' : 'false' ?>">
  <div class="header">
    <div class="title">
      <h2>Créer un salon</h2>

      <form method="dialog">
        <button class="button icon" aria-label="Fermer la boîte de dialogue">
          <!-- X -->
          <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M19.2806 18.7194C19.3502 18.789 19.4055 18.8718 19.4432 18.9628C19.4809 19.0539 19.5003 19.1514 19.5003 19.25C19.5003 19.3485 19.4809 19.4461 19.4432 19.5372C19.4055 19.6282 19.3502 19.7109 19.2806 19.7806C19.2109 19.8503 19.1281 19.9056 19.0371 19.9433C18.9461 19.981 18.8485 20.0004 18.7499 20.0004C18.6514 20.0004 18.5538 19.981 18.4628 19.9433C18.3717 19.9056 18.289 19.8503 18.2193 19.7806L11.9999 13.5603L5.78055 19.7806C5.63982 19.9213 5.44895 20.0004 5.24993 20.0004C5.05091 20.0004 4.86003 19.9213 4.7193 19.7806C4.57857 19.6399 4.49951 19.449 4.49951 19.25C4.49951 19.051 4.57857 18.8601 4.7193 18.7194L10.9396 12.5L4.7193 6.28061C4.57857 6.13988 4.49951 5.94901 4.49951 5.74999C4.49951 5.55097 4.57857 5.3601 4.7193 5.21936C4.86003 5.07863 5.05091 4.99957 5.24993 4.99957C5.44895 4.99957 5.63982 5.07863 5.78055 5.21936L11.9999 11.4397L18.2193 5.21936C18.36 5.07863 18.5509 4.99957 18.7499 4.99957C18.949 4.99957 19.1398 5.07863 19.2806 5.21936C19.4213 5.3601 19.5003 5.55097 19.5003 5.74999C19.5003 5.94901 19.4213 6.13988 19.2806 6.28061L13.0602 12.5L19.2806 18.7194Z"
              fill="currentColor" />
          </svg>
        </button>
      </form>
    </div>

    <p>
      Un salon textuel de discussion vous permet de discuter avec vos amis ou de parfaits inconnus !
    </p>
  </div>

  <form id="channel-form" method="post" action="/channels/create">
    <label for="name" class="input full">
      <div class="label">
        Nom du salon (préfixé par un #)
      </div>
      <input type="text" id="name" name="name" required maxlength="30">
    </label>
  </form>

  <p class="notice"></p>

  <button class="button primary" type="submit">
    Créer
  </button>

  <?php if (isset($_GET['error'])): ?>
    <p class="error">
      <?= $_GET['error'] ?>
    </p>
  <?php endif ?>
</dialog>

<script src="/scripts/create-channel-dialog.js"></script>

<?php ob_start(); ?>

<style data-file="chat/dialog">
  #create-channel-dialog {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-end;

    background-color: var(--color-surface-light);
    border: 1px solid var(--color-outline);
    border-radius: 2rem;

    margin: auto;
    padding: 1.5rem;
    max-width: 500px;
  }

  #create-channel-dialog[open] {
    display: flex;
  }

  #create-channel-dialog .header {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  #create-channel-dialog .title {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  #channel-form {
    display: contents;
  }

  #create-channel-dialog::backdrop {
    background-color: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(8px);
  }

  #create-channel-dialog .notice {
    width: 100%;
    color: orangered;
  }

  #create-channel-dialog .notice:empty {
    display: none;
  }
</style>

<?php $head = !isset($head) ? ob_get_clean() : $head . ob_get_clean(); ?>