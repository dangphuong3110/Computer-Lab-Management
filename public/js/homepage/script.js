window.addEventListener("beforeunload", function (event) {
    const overlay = document.getElementById('overlay');
    overlay.classList.add('show');
    event.returnValue = '';
});

window.addEventListener("unload", function (event) {
    const overlay = document.getElementById('overlay');
    overlay.classList.remove('show');
});

const body = document.querySelector('body'),
    sidebar = body.querySelector('.sidebar'),
    toggle = body.querySelector('.toggle'),
    // searchBtn = body.querySelector('.search-box'),
    modeSwitch = body.querySelector('.toggle-switch'),
    modeText = body.querySelector('.mode-text'),
    navLinks = body.querySelectorAll('.nav-link'),
    searchBtn = document.getElementById('search-button');

document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('sidebarState') !== 'close') {
        sidebar.classList.remove('close');
    }
});

toggle.addEventListener("click", () => {
    if (sidebar.classList.contains('close')) {
        localStorage.setItem('sidebarState', 'open');
    } else {
        localStorage.setItem('sidebarState', 'close');
    }
    sidebar.classList.toggle('close');
});

// searchBtn.addEventListener("click", () => {
//     sidebar.classList.remove('close');
//     localStorage.setItem('sidebarState', 'open');
// });

navLinks.forEach(navLink => {
    navLink.addEventListener("click", () => {
        navLinks.forEach(navLink => {
            navLink.classList.remove('active');
        });

        navLink.classList.add('active');
        const overlay = document.getElementById('overlay');
        overlay.classList.add('show');
    });
});

window.addEventListener('load', () => {
    if (localStorage.getItem('darkMode') === 'enabled') {
        body.classList.add('dark');
        if (searchBtn) {
            searchBtn.classList.remove('btn-outline-dark');
            searchBtn.classList.add('btn-outline-light');
        }
        modeText.innerText = "Chế độ sáng";
    } else {
        body.classList.remove('dark');
        if (searchBtn) {
            searchBtn.classList.remove('btn-outline-light');
            searchBtn.classList.add('btn-outline-dark');
        }
        modeText.innerText = "Chế độ tối";
    }
});

modeSwitch.addEventListener("click", () => {
    body.classList.toggle('dark');

    if(body.classList.contains('dark')){
        localStorage.setItem('darkMode', 'enabled');
        if (searchBtn) {
            searchBtn.classList.remove('btn-outline-dark');
            searchBtn.classList.add('btn-outline-light');
        }
        modeText.innerText = "Chế độ sáng";
    }
    else {
        localStorage.setItem('darkMode', 'disabled');
        if (searchBtn) {
            searchBtn.classList.remove('btn-outline-light');
            searchBtn.classList.add('btn-outline-dark');
        }
        modeText.innerText = "Chế độ tối";
    }
});
