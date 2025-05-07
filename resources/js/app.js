import './bootstrap';

document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.querySelector("[data-drawer-toggle]");
    const sidebar = document.getElementById("logo-sidebar");

    if (toggleButton && sidebar) {
        toggleButton.addEventListener("click", function () {
            sidebar.classList.toggle("-translate-x-full");
        });
    }
});