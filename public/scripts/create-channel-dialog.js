// @ts-check

const dialogTriggerButton = $("#create-channel");
/**
 * @type {JQuery<HTMLDialogElement>}
 */
const createChannelDialog = $("dialog#create-channel-dialog");
const nameInput = $("#name");

dialogTriggerButton.on("click", () => {
  createChannelDialog[0].showModal();
});

if (createChannelDialog.data("open") === true) {
  createChannelDialog[0].showModal();
}

nameInput.on("input", () => {
  /** @type {string|undefined} */
  const name = nameInput.val()?.toString();
  // le nom normalisé, càd sans caractères spéciaux et avec des tirets
  const normalizedName = name
    ?.toLowerCase()
    // on retire les caractères spéciaux (en ne comptant pas les accents)
    .replace(/[^a-z0-9éèàêëôöîïùûüç\-_\s]/g, "")
    // on remplace les espaces par des tirets
    .replace(/\s/g, "-")
    // on retire les tirets en fin de chaîne
    .replace(/-+$/, "");

  $(".notice").text(
    name !== normalizedName
      ? `Certains caractères ne peuvent pas être utilisés. Le salon sera créé avec le nom #${normalizedName}`
      : ""
  );
});
