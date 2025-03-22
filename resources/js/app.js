import "./bootstrap";
import "@tabler/core/dist/js/tabler.min.js";
import "@tabler/core/dist/css/tabler.min.css";

console.log("app.js is loaded successfully!");

document.addEventListener("DOMContentLoaded", function () {
    const themeToggle = document.getElementById("theme-toggle"); // Бутон за превключване
    const htmlElement = document.documentElement; // <html> елемента

    // Проверяваме дали има запазена тема в LocalStorage и я прилагаме
    const savedTheme = localStorage.getItem("theme") || "light"; // Ако няма записана тема, използваме "light"
    htmlElement.setAttribute("data-bs-theme", savedTheme);

    // Добавяме събитие за смяна на темата
    themeToggle.addEventListener("click", function () {
        let currentTheme = htmlElement.getAttribute("data-bs-theme");

        if (currentTheme === "dark") {
            htmlElement.setAttribute("data-bs-theme", "light");
            localStorage.setItem("theme", "light"); // Запазваме темата
        } else {
            htmlElement.setAttribute("data-bs-theme", "dark");
            localStorage.setItem("theme", "dark");
        }
    });
});
