// @ts-check

const createChannelButton = $("#create-channel");
/**
 * @type {HTMLDialogElement|null}
 */
const createChannelDialog = document.querySelector(
  "dialog#create-channel-dialog"
);

createChannelButton.on("click", () => {
  createChannelDialog?.showModal();
});
