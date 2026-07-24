document.querySelectorAll(".tribune-option").forEach((button) => {
    button.addEventListener("click", () => {
        document.querySelectorAll(".tribune-option").forEach((button) => {
            button.classList.remove("tribune-option--selected")

            
        })
            
        button.classList.add("tribune-option--selected")

        const tribuneName = button.querySelector('.tribune-option__name').textContent.trim();
        document.getElementById('tribune-input').value = tribuneName;
        
    })
    
})

document.querySelectorAll(".niveau-option").forEach((button) => {
    button.addEventListener("click", () => {
        document.querySelectorAll(".niveau-option").forEach((button) => {
            button.classList.remove("niveau-option--selected")

            
        })
            
        button.classList.add("niveau-option--selected")

        document.getElementById('niveau-input').value = button.textContent.trim();
    })
    
})

const minus = document.querySelector(".places-counter__btn--minus")
const plus = document.querySelector(".places-counter__btn--plus")
const value = document.querySelector(".places-counter__value")

plus.addEventListener("click",() => {
    if (parseInt(value.textContent) < 2) {
        value.textContent = parseInt(value.textContent) + 1;
        document.getElementById('nombre-input').value = value.textContent;

    };

});

minus.addEventListener("click",() => {
    if(parseInt(value.textContent) > 1) {
        value.textContent = parseInt(value.textContent) - 1;
        document.getElementById('nombre-input').value = value.textContent;
    };
})

function remplirModaleConfirmation() {
   const tribune = document.getElementById('tribune-input').value;
   const niveau = document.getElementById('niveau-input').value;
   const nombre = Number(document.getElementById('nombre-input').value);
   const texte_place = nombre + (nombre > 1 ? " places" : " place");

    document.getElementById("modal-tribune").textContent = tribune.charAt(0).toUpperCase() + tribune.slice(1);
    document.getElementById("modal-niveau").textContent = niveau;
    document.getElementById("modal-places").textContent = texte_place;
}

document.querySelector('.btn--confirm').addEventListener('click', () => {
    remplirModaleConfirmation();
});