// ============================================================
// 1. OUVERTURE DES MODALS
// ============================================================

document.querySelectorAll(".btn--success").forEach((button) => {

    button.addEventListener("click", () => {
        if (!document.getElementById("modal-qr")) return;

        document.getElementById("modal-qr").classList.add("modal-show");

        document.querySelector(".modal-show > div").focus();
    });
});

document.querySelectorAll(".btn--confirm").forEach((button) => {
    button.addEventListener("click", () => {
        document.getElementById("modal-confirm").classList.add("modal-show");
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

// ============================================================
// 3. OUVERTURE DE LA MODALE DE SUPPRESSION
// ============================================================

document.querySelectorAll(".btn--delete").forEach((button) => {
    button.addEventListener("click", () => {
        const carte = button.closest(".card");
        const modale = carte.nextElementSibling;

        modale.classList.add("modal-show");
        modale.querySelector(".modal-wrapper__content").focus();
    });
});

// ============================================================
// 4. FERMETURE DE LA MODALE DE SUPPRESSION (bouton "Non")
// ============================================================

document.querySelectorAll(".btn--annuler").forEach((button) => {
    button.addEventListener("click", () => {
        button.closest(".modal-wrapper").classList.remove("modal-show");
    });
});