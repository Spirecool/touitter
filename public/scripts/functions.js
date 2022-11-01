// afficher/masquer le mot de passe

const togglePassword = () => {
    const passwordInput = document.querySelector("#user_password")
    passwordInput.type = passwordInput.type === "text" ? "password" : "text"

    const eyeIcon = document.querySelector("#eye")
    eyeIcon.style.display = eyeIcon.style.display === "none" ? "block" : "none"

    eyeIcon.classList.contains("d-none") ? eyeIcon.classList.remove("d-none") : eyeIcon.classList.add("d-none")

    const eyeSlashIcon = document.querySelector("#eye-slash")
    eyeSlashIcon.classList.contains("d-none") ? eyeSlashIcon.classList.remove("d-none") : eyeSlashIcon.classList.add("d-none")
}
