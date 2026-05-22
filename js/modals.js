// ============================================================
// 1. OUVERTURE DES MODALS
// ============================================================

document.querySelectorAll(".btn--success").forEach((button) => {

    button.addEventListener("click", () => {
        document.getElementById("modal-test").classList.add("modal-show");

        document.querySelector(".modal-show > div").focus();
    });
});

// ============================================================
// 2. FERMETURE DES MODALS (bouton ✕)
// ============================================================

document.querySelectorAll(".modal-button-close").forEach((button) => {
    button.addEventListener("click", () => {
        let wrapper = button.closest(".modal-wrapper");
        wrapper.classList.remove("modal-show");
    })
})