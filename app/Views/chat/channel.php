<?php

/**
 * @var \App\Models\Channel $channel
 * @var \App\Models\Message[] $messages
 */

?>

<?php ob_start(); ?>

<main>
  <div class="header">
    <h2>#<?= $channel->name ?></h2>
  </div>

  <div class="messages">
    <?php foreach ($messages as $message):
      // On inclut le composant de message
      include 'Message.php';
    endforeach; ?>
  </div>

  <form id="chat-form" method="post" action="/chats/<?= $channel->id ?>/send-message">
    <input id="chatbox" type="text" name="content" minlength="1" maxlength="255"
      placeholder="Envoyer un message sur #<?= $channel->name ?>" autofocus required />
  </form>
</main>

<script src="/scripts/send-message.js"></script>

<?php $slot = ob_get_clean(); ?>

<?php ob_start(); ?>

<style data-file="home">
  main {
    height: 100dvh;
    display: flex;
    flex-direction: column;
  }

  .header {
    padding: 0.75rem 1rem;
    background-color: var(--color-surface);
    border-bottom: 1px solid var(--color-outline);
  }

  .messages {
    padding: 1rem 1rem 0 1rem;
    flex: 1;
    overflow-y: scroll;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .message {
    width: fit-content;
    padding: 0.5rem 0.75rem;
    background-color: var(--color-surface);
    border-radius: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-end;
  }

  .message.me {
    background-color: var(--color-primary);
    color: var(--color-on-primary);
    margin-left: auto;
  }

  .message .author,
  .message time {
    font-size: 0.75rem;
  }

  form {
    display: flex;
    padding: 0.75rem 1rem;
  }

  #chatbox {
    width: 100%;
    border: none;
    border-radius: 99rem;
    padding: 0.75rem 1rem;
    background-color: var(--color-surface);
    color: var(--color-on-surface);
  }
</style>

<?php $head = !isset($head) ? ob_get_clean() : $head . ob_get_clean(); ?>