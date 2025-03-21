<?php

/**
 * @var ?\App\Models\Channel $channel
 * @var \App\Models\Channel[] $channels
 * @var \App\Models\User $currentUser
 */

?>

<?php ob_start() ?>

<div class="content" data-user-id="<?= $currentUser->id ?>">
  <aside>
    <div class="top">
      <div class="header">
        <h1>Discussions</h1>

        <button id="create-channel" class="button icon">
          <!-- NotePencil -->
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M21.5306 5.46938L18.5306 2.46938C18.461 2.39964 18.3783 2.34432 18.2872 2.30658C18.1962 2.26884 18.0986 2.24941 18 2.24941C17.9014 2.24941 17.8038 2.26884 17.7128 2.30658C17.6217 2.34432 17.539 2.39964 17.4694 2.46938L8.46937 11.4694C8.39975 11.5391 8.34454 11.6218 8.3069 11.7129C8.26926 11.8039 8.24992 11.9015 8.25 12V15C8.25 15.1989 8.32902 15.3897 8.46967 15.5303C8.61032 15.671 8.80109 15.75 9 15.75H12C12.0985 15.7501 12.1961 15.7307 12.2871 15.6931C12.3782 15.6555 12.4609 15.6003 12.5306 15.5306L21.5306 6.53063C21.6004 6.46097 21.6557 6.37825 21.6934 6.28721C21.7312 6.19616 21.7506 6.09856 21.7506 6C21.7506 5.90144 21.7312 5.80384 21.6934 5.7128C21.6557 5.62175 21.6004 5.53903 21.5306 5.46938ZM11.6897 14.25H9.75V12.3103L15.75 6.31031L17.6897 8.25L11.6897 14.25ZM18.75 7.18969L16.8103 5.25L18 4.06031L19.9397 6L18.75 7.18969ZM21 12V19.5C21 19.8978 20.842 20.2794 20.5607 20.5607C20.2794 20.842 19.8978 21 19.5 21H4.5C4.10218 21 3.72064 20.842 3.43934 20.5607C3.15804 20.2794 3 19.8978 3 19.5V4.5C3 4.10218 3.15804 3.72064 3.43934 3.43934C3.72064 3.15804 4.10218 3 4.5 3H12C12.1989 3 12.3897 3.07902 12.5303 3.21967C12.671 3.36032 12.75 3.55109 12.75 3.75C12.75 3.94891 12.671 4.13968 12.5303 4.28033C12.3897 4.42098 12.1989 4.5 12 4.5H4.5V19.5H19.5V12C19.5 11.8011 19.579 11.6103 19.7197 11.4697C19.8603 11.329 20.0511 11.25 20.25 11.25C20.4489 11.25 20.6397 11.329 20.7803 11.4697C20.921 11.6103 21 11.8011 21 12Z"
              fill="currentColor" />
          </svg>
        </button>
      </div>

      <ul>
        <?php foreach ($channels as $ch): ?>
          <li>
            <a href="/chats/<?= $ch->id ?>" aria-current="<?= $channel && $channel->id === $ch->id ? 'page' : 'false' ?>">
              <div class="channel-name">
                #<?= $ch->name ?>
              </div>
            </a>
          </li>
        <?php endforeach ?>
      </ul>
    </div>
  </aside>

  <?= $slot ?>
</div>

<?php include 'CreateChannelDialog.php' ?>

<?php $content = ob_get_clean() ?>

<?php ob_start() ?>

<style data-file="chat/layout">
  .content {
    display: grid;
    grid-template-columns: 350px 1fr;
    height: 100vh;
  }

  aside {
    background-color: var(--color-surface);
    padding: 1rem 1.25rem;
    border-right: 1px solid var(--color-outline);
  }

  .top {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .top .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  ul {
    display: flex;
    flex-direction: column;
    list-style: none;
    padding: 0;
    margin: 0;
  }

  li {
    margin: 0;
  }

  a {
    display: flex;
    text-decoration: none;
    padding: 0.75rem;
    border-radius: 0.75rem;
  }

  a[aria-current="page"],
  a:hover {
    background-color: var(--color-surface-variant);
  }

  a .channel-name {
    font-weight: 500;
  }
</style>

<?php $head = !isset($head) ? ob_get_clean() : $head . ob_get_clean(); ?>