const connexion_form = document.getElementById("connexion-form")

connexion_form.setAttribute("novalidate", true);

const email = document.getElementById("email");
const pwd = document.getElementById("mot_de_passe");


connexion_form.addEventListener("submit", (event) => {
    connexion_form.querySelectorAll(".form-group.error").forEach((elt) => {
        elt.classList.remove("error");
        elt.querySelector(".error-message").remove();
    });
    let error = false;

    const regEx = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (email.value.trim().length == 0) {
        email.closest(".form-group").classList.add("error")
        error = true;

        let span = document.createElement("span");
        span.innerText = "champ obligatoire...";
        span.classList.add("error-message");
        email.closest(".form-group").append(span);
    

    

    } else if (!regEx.test(email.value)) {
        email.closest(".form-group").classList.add("error");
        error = true;
        
        let span = document.createElement("span");
        span.innerText = "Adresse mail non valide...";
        span.classList.add("error-message");
        email.closest(".form-group").append(span);
        
    }



    if (pwd.value.trim().length == 0) {
        pwd.closest(".form-group").classList.add("error")
        error = true;

        let span = document.createElement("span");
        span.innerText = "champ obligatoire...";
        span.classList.add("error-message");
        pwd.closest(".form-group").append(span);

    } else if(pwd.value.trim().length < 8) {
        pwd.closest(".form-group").classList.add("error")
        error = true;

        let span = document.createElement("span");
        span.innerText = "Votre mot de passe doit contenir au moins 8 caractéres...";
        span.classList.add("error-message");
        pwd.closest(".form-group").append(span);
    }
    if (error === true) {
            
        event.preventDefault();
    }
}); 