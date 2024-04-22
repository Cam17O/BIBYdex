const pseudo = document.getElementById('pseudo');
const mail = document.getElementById('mail');
const password = document.getElementById('password');
const confirm_password = document.getElementById('confirm_password');
const form = document.getElementById('form');
const errorElement = document.getElementById('error');

function doesNotContainNumber(str) {
    return !/\d/.test(str);
}

function doesNotContainMajuscule(str) {
    return !/[A-Z]/.test(str);
}

function doesNotContainMinuscule(str) {
    return !/[a-z]/.test(str);
}

form.addEventListener('submit', (e) => {
    const messages = [];

    if (pseudo.value.trim() === '') {
        messages.push("Veuillez renseigner votre nom.");
    }
    if (mail.value.trim() === '') {
        messages.push("Veuillez renseigner votre mail.");
    }
    if (password.value.trim() === '') {
        messages.push("Veuillez renseigner votre mot de passe.");
    }
    if (confirm_password.value.trim() === '') {
        messages.push("Veuillez confirmer votre mot de passe.");
    }
    if (password.value !== confirm_password.value) {
        messages.push("Les mots de passe ne correspondent pas.");
    }
    if (password.value.length < 8) {
        messages.push("Le mot de passe doit contenir au moins 8 caractères.");
    }
    if (doesNotContainMajuscule(password.value)) {
        messages.push("Le mot de passe doit contenir au moins une majuscule.");
    }
    if (doesNotContainMinuscule(password.value)) {
        messages.push("Le mot de passe doit contenir au moins une minuscule.");
    }
    if (doesNotContainNumber(password.value)) {
        messages.push("Le mot de passe doit contenir au moins un chiffre.");
    }

    if (messages.length > 0) {
        e.preventDefault();
        errorElement.innerText = messages.join('.\n');
    }
});