<?php

/**
 * @var \App\Models\Channel[] $channels
 * @var \App\Models\User $currentUser
 */

?>

<?php ob_start() ?>

<div class="content">
  <aside>
    <h1>Discussions</h1>
    <ul>
      <?php foreach ($channels as $channel): ?>
        <li>
          <a href="/chats/<?= $channel->id ?>">
            #<?= $channel->name ?>
          </a>
        </li>
      <?php endforeach ?>
    </ul>
  </aside>

  <?= $slot ?>
</div>


<?php $content = ob_get_clean() ?>

<?php ob_start() ?>

<style data-file="chat/layout">
  .content {
    display: grid;
    grid-template-columns: 200px 1fr;
    height: 100vh;
  }

  aside {
    background-color: var(--color-surface);
    padding: 1rem;
    border-right: 1px solid var(--color-outline);
  }

  ul {
    list-style-type: none;
    padding: 0;
  }

  li {
    margin-bottom: 0.5rem;
  }

  a {
    text-decoration: none;
    color: inherit;
  }

  a:hover {
    text-decoration: underline;
  }
</style>

<?php $head = !isset($head) ? ob_get_clean() : $head . ob_get_clean(); ?>