// @ts-check

const chatbox = $("#chatbox");
const chatForm = $("#chat-form");
const messageGroups = $(".message-groups");
/**
 * L'ID de l'utilisateur connecté
 * @type {string}
 */
const currentUserId = $("#chat-content").data("user-id");
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

  const fetchUrl =
    `/chats/${channelId}/messages` +
    (lastMessageId ? `?lastMessageId=${lastMessageId}` : "");

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

const copyInviteButton = $("#copy-invite");

copyInviteButton.on("click", () => {
  $.ajax({
    url: `/channels/${channelId}/invite`,
    // data est du JSON
    success(data) {
      console.log(JSON.stringify(data));
      navigator.clipboard.writeText(
        window.location.origin + "/join/" + data.token
      );

      // copy invite button inner html
      const copyInviteButtonHtml = $("#copy-invite").html();

      copyInviteButton.removeClass("primary");
      copyInviteButton.addClass("success");
      copyInviteButton.html(
        `
<!-- Check -->
<svg width="18" height="19" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M16.1479 5.96047L7.14792 14.9605C7.09567 15.0128 7.03364 15.0543 6.96535 15.0826C6.89706 15.1109 6.82387 15.1254 6.74995 15.1254C6.67602 15.1254 6.60283 15.1109 6.53454 15.0826C6.46626 15.0543 6.40422 15.0128 6.35198 14.9605L2.41448 11.023C2.30893 10.9174 2.24963 10.7743 2.24963 10.625C2.24963 10.4757 2.30893 10.3326 2.41448 10.227C2.52003 10.1215 2.66318 10.0622 2.81245 10.0622C2.96171 10.0622 3.10487 10.1215 3.21042 10.227L6.74995 13.7673L15.352 5.16453C15.4575 5.05898 15.6007 4.99969 15.7499 4.99969C15.8992 4.99969 16.0424 5.05898 16.1479 5.16453C16.2535 5.27008 16.3128 5.41323 16.3128 5.5625C16.3128 5.71177 16.2535 5.85492 16.1479 5.96047Z" fill="currentColor"/>
</svg>
Copié !
`
      );

      setTimeout(() => {
        copyInviteButton.removeClass("success");
        copyInviteButton.addClass("primary");
        copyInviteButton.html(copyInviteButtonHtml);
      }, 1000);
    },
  });
});
