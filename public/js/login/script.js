function toggleFormInteraction(enableLogin) {
    const loginFormFields = document.getElementById('login-form').querySelectorAll('input, button, a'),
        registerFormFields = document.getElementById('register-form').querySelectorAll('input, button, a');

    if (enableLogin) {
        loginFormFields.forEach(function(element) {
            element.disabled = false;
        });
        registerFormFields.forEach(function(element) {
            element.disabled = true;
        });
    } else {
        loginFormFields.forEach(function(element) {
            element.disabled = true;
        });
        registerFormFields.forEach(function(element) {
            element.disabled = false;
        });
    }
}

const loginBtnSwitch = document.querySelector('#login'),
    registerBtnSwitch = document.querySelector('#register'),
    loginForm = document.querySelector('.login-form'),
    registerForm = document.querySelector('.register-form');

loginBtnSwitch.addEventListener('click', () => {
    loginBtnSwitch.style.backgroundColor = '#1930B0';
    registerBtnSwitch.style.backgroundColor = 'rgba(255, 255, 255, 0.2)';

    loginForm.style.left = "50%";
    registerForm.style.left = "-50%";

    loginForm.style.opacity = 1;
    registerForm.style.opacity = 0;

    document.querySelector(".col-1").style.borderRadius = "0% 30% 20% 0";
    toggleFormInteraction(true);
})

registerBtnSwitch.addEventListener('click', () => {
    loginBtnSwitch.style.backgroundColor = 'rgba(255, 255, 255, 0.2)';
    registerBtnSwitch.style.backgroundColor = '#1930B0';

    loginForm.style.left = "150%";
    registerForm.style.left = "50%";

    loginForm.style.opacity = 0;
    registerForm.style.opacity = 1;

    document.querySelector(".col-1").style.borderRadius = "0% 20% 30% 0";
    toggleFormInteraction(false);
})

toggleFormInteraction(true);

window.addEventListener("beforeunload", function (event) {
    const overlay = document.getElementById('overlay');
    overlay.classList.add('show');
    event.returnValue = '';
});

window.addEventListener("unload", function (event) {
    const overlay = document.getElementById('overlay');
    overlay.classList.remove('show');
});

