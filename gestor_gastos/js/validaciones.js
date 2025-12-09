document.addEventListener('DOMContentLoaded', () => {

    function validarEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function validarPassword(password) {
        // mínimo 6 caracteres, al menos 1 letra y 1 número
        return /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/.test(password);
    }

    // Validación para registro
    let registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            let nombre = document.getElementById('nombre').value;
            let email = document.getElementById('email').value;
            let password = document.getElementById('password').value;
            let confirmPassword = document.getElementById('confirmPassword').value;

            if (nombre.trim() === "") {
                alert("El nombre es obligatorio.");
                e.preventDefault();
                return;
            }

            if (!validarEmail(email)) {
                alert("El formato del correo electrónico no es válido.");
                e.preventDefault();
                return;
            }

            if (!validarPassword(password)) {
                alert("La contraseña debe tener al menos 6 caracteres, incluyendo una letra y un número.");
                e.preventDefault();
                return;
            }

            if (password !== confirmPassword) {
                alert("Las contraseñas no coinciden.");
                e.preventDefault();
                return;
            }
        });
    }

    // Validación para login
    let loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            let email = document.getElementById('email').value;
            let password = document.getElementById('password').value;

            if (!validarEmail(email)) {
                alert("El formato del correo electrónico no es válido.");
                e.preventDefault();
            }

            if (password.trim() === "") {
                alert("La contraseña no puede estar vacía.");
                e.preventDefault();
            }
        });
    }
});
