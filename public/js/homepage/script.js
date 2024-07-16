// window.addEventListener("beforeunload", function (event) {
//     const overlay = document.getElementById('overlay');
//     overlay.classList.add('show');
//     event.returnValue = '';
// });

// window.addEventListener("unload", function (event) {
//     const overlay = document.getElementById('overlay');
//     overlay.classList.remove('show');
// });

// document.addEventListener('DOMContentLoaded', function () {
//     const overlay = document.getElementById('overlay');
//     overlay.classList.add('show');

//     // Hide the overlay after 1 second
//     setTimeout(function () {
//         overlay.classList.remove('show');
//     }, 1000);  // 1 second delay
// });

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
    const isDarkMode = localStorage.getItem('darkMode') === 'enabled';

    body.classList.toggle('dark', isDarkMode);

    if (searchBtn) {
        searchBtn.classList.toggle('btn-outline-dark', !isDarkMode);
        searchBtn.classList.toggle('btn-outline-light', isDarkMode);
    }

    modeText.innerText = isDarkMode ? "Chế độ sáng" : "Chế độ tối";
});

modeSwitch.addEventListener("click", () => {
    body.classList.toggle('dark');
    const isDarkMode = body.classList.contains('dark');

    localStorage.setItem('darkMode', isDarkMode ? 'enabled' : 'disabled');
    document.cookie = `theme=${isDarkMode ? 'dark' : 'light'}; path=/`;

    if (searchBtn) {
        searchBtn.classList.toggle('btn-outline-dark', !isDarkMode);
        searchBtn.classList.toggle('btn-outline-light', isDarkMode);
    }

    modeText.innerText = isDarkMode ? "Chế độ sáng" : "Chế độ tối";
});
