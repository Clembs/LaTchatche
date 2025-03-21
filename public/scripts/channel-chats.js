// @ts-check

const chatbox = $("#chatbox");
const chatForm = $("#chat-form");
const messageGroups = $(".message-groups");
/**
 * L'ID de l'utilisateur connecté
 * @type {string}
 */
const currentUserId = $(".content").data("user-id");
/**
 * L'ID du salon actuel
 * @type {string}
 */
const channelId = $("main").data("channel-id");

/**
 * @param {string} messageHtml
 */
function addMessage(messageHtml) {
  /**
   * L'ID de l'auteur du message à ajouter
   * @type {string}
   */
  const messageAuthorId = $(messageHtml).data("author-id");
  const isFromMe = messageAuthorId === currentUserId;

  let lastMessageGroup = messageGroups.children().last();
  let lastMessage = lastMessageGroup.children().last();

  // si le dernier message n'est pas envoyé par le même auteur que le message actuel
  // on crée un nouveau groupe et on l'ajoute aux groupes de messages
  if (lastMessage.data("author-id") !== messageAuthorId) {
    lastMessageGroup = $(
      `<div class='message-group ${isFromMe ? "me" : "not-me"}'></div>`
    );
    messageGroups.append(lastMessageGroup);
  }

  // on ajoute le message au groupe
  lastMessageGroup.append(messageHtml);

  // on anime le scroll pour descendre
  messageGroups.animate({
    scrollTop: messageGroups.prop("scrollHeight"),
  });
}

// Le fait d'utiliser un formulaire permet d'utiliser l'amélioration progressive
// Donc on peut quand même envoyer des messages sans JavaScript sur le client
chatForm.on("submit", (ev) => {
  // on empêche le formulaire de se soumettre
  ev.preventDefault();

  // on envoie le message au serveur de manière asynchrone
  $.ajax({
    url: chatForm.attr("action"),
    method: chatForm.attr("method"),
    data: {
      content: chatbox.val(),
    },
    // data est de l'HTML, on l'ajoute à la page
    success(data) {
      // on vide la zone de texte
      chatbox.val("");

      addMessage(data);
    },
  });
});

messageGroups.scrollTop(messageGroups.prop("scrollHeight"));

const FETCH_INTERVAL = 2000;

setInterval(() => {
  const lastMessageId = messageGroups
    .children()
    .last()
    .children()
    .last()
    .data("message-id");

  const fetchUrl = `/chats/${channelId}/messages?lastMessageId=${lastMessageId}`;

  $.ajax({
    url: fetchUrl,
    method: "GET",
    // tableau de messages en HTML
    success(
      /** @type {string[]} data */
      data
    ) {
      data.forEach((messageHtml) => {
        addMessage(messageHtml);
      });
    },
  });
}, FETCH_INTERVAL);
