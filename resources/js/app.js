import "./bootstrap";
import "@tabler/core/dist/css/tabler.min.css";
import "@tabler/core/dist/js/tabler.min.js";

// Initialize Bootstrap components
import { Dropdown } from "bootstrap";

console.log("app.js is loaded successfully!");

// Initialize all dropdowns
document.addEventListener("DOMContentLoaded", function () {
    // Initialize all dropdowns
    const dropdownElementList = document.querySelectorAll(
        '[data-bs-toggle="dropdown"]',
    );
    dropdownElementList.forEach((dropdownToggleEl) => {
        const dropdown = new Dropdown(dropdownToggleEl);

        // Add click event listener to ensure dropdown works
        dropdownToggleEl.addEventListener("click", function (e) {
            e.preventDefault();
            dropdown.toggle();
        });
    });

    // Theme toggle functionality
    const themeToggle = document.getElementById("theme-toggle");
    const htmlElement = document.documentElement;

    // Check for saved theme
    const savedTheme = localStorage.getItem("theme") || "light";
    htmlElement.setAttribute("data-bs-theme", savedTheme);

    // Theme toggle event
    if (themeToggle) {
        themeToggle.addEventListener("click", function () {
            let currentTheme = htmlElement.getAttribute("data-bs-theme");
            const newTheme = currentTheme === "dark" ? "light" : "dark";
            htmlElement.setAttribute("data-bs-theme", newTheme);
            localStorage.setItem("theme", newTheme);
        });
    }
});
