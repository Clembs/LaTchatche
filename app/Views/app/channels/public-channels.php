<?php

/**
 * @var \App\Models\Channel[] $channels
 */


$popularChannels = $channels;
usort($popularChannels, fn($a, $b) => $b->messageCount - $a->messageCount);

$newestChannels = $channels;
usort($newestChannels, fn($a, $b) => $b->createdAt->getTimestamp() - $a->createdAt->getTimestamp());

$biggestChannels = $channels;
usort($biggestChannels, fn($a, $b) => $b->memberCount - $a->memberCount);
?>

<?php ob_start() ?>

<main>
  <div id="channels-content">

    <div class="header">
      <h1>Découvrir des salons publics</h1>

      <p>
        Discutez avec de vraies personnes du monde entier des passions et autres sujets qui vous animent.
      </p>
    </div>

    <section id="popular-channels">
      <h2>Salons populaires</h2>

      <ul>
        <?php foreach ($popularChannels as $ch): ?>
          <li>
            <a href="/channels/<?= $ch->id ?>" class="channel">
              <div class="icon">
                <?php include __DIR__ . '/../../../Icons/Hashtag.php'; ?>
              </div>
              <div class="channel-info">
                <div class="channel-name"><?= $ch->name ?></div>
                <div class="subtext">
                  <?= $ch->memberCount ?> membre(s) • <?= $ch->messageCount ?> message(s) au total
                </div>
              </div>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>

    <section id="recent-channels">
      <h2>Récemment créés</h2>

      <ul>
        <?php foreach ($newestChannels as $ch): ?>
          <li>
            <a href="/channels/<?= $ch->id ?>" class="channel">
              <div class="icon">
                <?php include __DIR__ . '/../../../Icons/Hashtag.php'; ?>
              </div>
              <div class="channel-info">
                <div class="channel-name"><?= $ch->name ?></div>
                <div class="subtext">
                  <?= $ch->memberCount ?> membre(s) • Créé le <?= $ch->createdAt->format('d/m/Y H:i:s') ?>
                </div>
              </div>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>

    <section id="biggest-channels">
      <h2>Les plus grands salons</h2>

      <ul>
        <?php foreach ($popularChannels as $ch): ?>
          <li>
            <a href="/channels/<?= $ch->id ?>" class="channel">
              <div class="icon">
                <?php include __DIR__ . '/../../../Icons/Hashtag.php'; ?>
              </div>
              <div class="channel-info">
                <div class="channel-name"><?= $ch->name ?></div>
                <div class="subtext">
                  <?= $ch->memberCount ?> membre(s) • <?= $ch->messageCount ?> message(s) au total
                </div>
              </div>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  </div>
</main>

<?php $slot = ob_get_clean() ?>

<?php ob_start() ?>

<style data-file="channels/public-channels">
  main {
    display: grid;
    place-items: center;
    height: 100vh;
    overflow-y: scroll;
  }

  #channels-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    align-items: center;
  }

  #channels-content .header {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    text-align: center;
    margin: 3rem 0;
  }

  #channels-content section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    width: 100%;
  }

  #channels-content section ul {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 0.5rem;
    width: 100%;
    list-style: none;
    padding: 0;
    margin: 0;
  }

  #channels-content section .channel {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem 0.75rem 0.75rem;
    border-radius: 0.75rem;
    background-color: var(--color-surface);
    text-decoration: none;
  }

  #channels-content section .channel .icon {
    padding: 0.5rem;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background-color: var(--color-surface-variant);
  }

  #channels-content section .channel .channel-name {
    font-size: 1.25rem;
  }

  #channels-content section .channel:hover {
    background-color: var(--color-surface-variant);
  }

  #channels-content section .channel:hover .icon {
    background-color: var(--color-surface);
  }
</style>