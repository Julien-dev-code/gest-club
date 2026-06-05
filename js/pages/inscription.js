const inscription_form = document.getElementById("inscription-form")

inscription_form.setAttribute("novalidate", true);

const fname = document.getElementById("firstname");
const lname = document.getElementById("lastname");
const numphone = document.getElementById("phone");
const email = document.getElementById("email");
const pwd = document.getElementById("pwd");
const pwdConfirm = document.getElementById("pwdConfirm");
const checkbox = document.getElementById("check")

const regExPhone = /^0[67](\s?\d{2}){4}$/;
const regExEmail = /^[a-zA-Z0-9_.-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

const criterias = {
    "length": {
        "regEx": /.{8,}/,        
        "label": "8 caratctéres minimum"
    },
    "special": {
        "regEx": /[#@!?$%&]+/,   
        "label": "Au moins 1 caractère spéciale"
    },
    "uppercase": {
        "regEx": /[A-Z]+/,       
        "label": "Au moins 1 majuscule"
    },
    "numeric": {
        "regEx": /[0-9]+/,       
        "label": "Au moins 1 caractère numerique"
    }
};

const div = document.createElement("div");
    const p = document.createElement("p");
    const text = document.createTextNode("Merci de respecter les critéres suivants :");
    p.append(text);
    div.append(p);

    const ul = document.createElement("ul");

    for (const [id, data] of Object.entries(criterias)) {
        const li = document.createElement("li");
        const text = document.createTextNode(data["label"]);

    li.id = "pwd-criteria-" + id;
    li.append(text);
    ul.append(li);
}

div.append(ul);
pwd.parentNode.append(div);


pwd.addEventListener("keyup", () => {
    for (const [id, data] of Object.entries(criterias)) {
        if (data["regEx"].test(pwd.value)) {
            document.getElementById("pwd-criteria-" + id).classList.add("success");
            document.getElementById("pwd-criteria-" + id).classList.remove("error");
        } else {
            document.getElementById("pwd-criteria-" + id).classList.remove("success");
        }
    }
});


inscription_form.addEventListener("submit", (event) => {
    inscription_form.querySelectorAll(".form-group.error").forEach((elt) => {
        elt.classList.remove("error");
        elt.querySelector(".error-message").remove();
    })

    let error = false;

    // ============================================================
    // 1. VERIFICATION QUE LES CHAMPS NOM ET PRENOM SONT BIEN REMPLI
    // ============================================================


    if(fname.value.trim().length == 0) {
        fname.closest(".form-group").classList.add("error");
        error = true;

        let span = document.createElement("span");
        span.innerText = "champ obligatoire...";
        span.classList.add("error-message");
        fname.closest(".form-group").append(span);
    }

    if(lname.value.trim().length == 0) {
        lname.closest(".form-group").classList.add("error");
        error = true;

        let span = document.createElement("span");
        span.innerText = "champ obligatoire...";
        span.classList.add("error-message");
        lname.closest(".form-group").append(span);
    }


    // ============================================================================
    // 2. VERIFICATION QUE LE CHAMP TELEPHONE SOIT REMPLI ET CORRECTEMENT FORMATE
    // ============================================================================

    if(numphone.value.trim().length == 0) {
        numphone.closest(".form-group").classList.add("error");
        error = true;

        let span = document.createElement("span");
        span.innerText = "champ obligatoire...";
        span.classList.add("error-message");
        numphone.closest(".form-group").append(span);
    } else if (!regExPhone.test(numphone.value)) {
        numphone.closest(".form-group").classList.add("error");
        error = true;
        
        let span = document.createElement("span");
        span.innerText = "Numéro de téléphone non valide...";
        span.classList.add("error-message");
        numphone.closest(".form-group").append(span);
    }

    // ============================================================================
    // 3. VERIFICATION QUE LE CHAMP EMAIL SOIT REMPLI ET CORRECTEMENT FORMATE
    // ============================================================================

   if (email.value.trim().length == 0) {
        email.closest(".form-group").classList.add("error")
        error = true;

        let span = document.createElement("span");
        span.innerText = "champ obligatoire...";
        span.classList.add("error-message");
        email.closest(".form-group").append(span);
    
    
    } else if (!regExEmail.test(email.value)) {
        email.closest(".form-group").classList.add("error");
        error = true;
        
        let span = document.createElement("span");
        span.innerText = "Adresse mail non valide...";
        span.classList.add("error-message");
        email.closest(".form-group").append(span);
    }

    // ===============================================================================
    // 4. VERIFICATION QUE LE CHAMP MOT DE PASSSE SOIT REMPLI ET RESPECTE LES CRITERES
    // ===============================================================================
   
    if (pwd.value.trim().length == 0) {
        pwd.closest(".form-group").classList.add("error")
        error = true;

        let span = document.createElement("span");
        span.innerText = "champ obligatoire...";
        span.classList.add("error-message");
        pwd.closest(".form-group").append(span);
    }

    // ===========================================================================================
    // 5. VERIFICATION QUE LE CHAMP CONFIRMATION MOT DE PASSSE SOIT REMPLI ET RESPECTE LES CRITERES
    // ============================================================================================

    if (pwdConfirm.value.length == 0) {
        pwdConfirm.closest(".form-group").classList.add("error")
        error = true;

        let span = document.createElement("span");
        span.innerText = "champ obligatoire...";
        span.classList.add("error-message");
        pwdConfirm.closest(".form-group").append(span);
    
    } else if(pwdConfirm.value != pwd.value) {
        pwdConfirm.closest(".form-group").classList.add("error")
        error = true;

        let span = document.createElement("span");
        span.innerText = "mot de passe différent...";
        span.classList.add("error-message");
        pwdConfirm.closest(".form-group").append(span);
    }

    // ==================================================
    // 6. VERIFICATION QUE LE CHAMP CHECKBOX SOIT REMPLI 
    // ==================================================


    if (checkbox.checked == false) {
        checkbox.closest(".form-group").classList.add("error");
        error = true;

        let span = document.createElement("span");
        span.innerText = "Veuillez accepter les conditions d'utilisation...";
        span.classList.add("error-message");
        checkbox.closest(".form-group").append(span);
    }

    if (error === true) {
            
        event.preventDefault();
    }
})