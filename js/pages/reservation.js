document.querySelectorAll(".tribune-option").forEach((button) => {
    button.addEventListener("click", () => {
        document.querySelectorAll(".tribune-option").forEach((button) => {
            button.classList.remove("tribune-option--selected")

            
        })
            
        button.classList.add("tribune-option--selected")
        
    })
    
})

document.querySelectorAll(".niveau-option").forEach((button) => {
    button.addEventListener("click", () => {
        document.querySelectorAll(".niveau-option").forEach((button) => {
            button.classList.remove("niveau-option--selected")

            
        })
            
        button.classList.add("niveau-option--selected")
    })
    
})

const minus = document.querySelector(".places-counter__btn--minus")
const plus = document.querySelector(".places-counter__btn--plus")
const value = document.querySelector(".places-counter__value")

plus.addEventListener("click",() => {
    if (parseInt(value.textContent) < 2) {
        value.textContent = parseInt(value.textContent) + 1;
    };

});

minus.addEventListener("click",() => {
    if(parseInt(value.textContent) > 1) {
        value.textContent = parseInt(value.textContent) - 1;
    };
})