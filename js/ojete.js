document.querySelectorAll("[data-toggle-password]").forEach((toggle) => {
    toggle.addEventListener("click", () => {
        const container = toggle.closest(".input-password-container");
        const input = container ? container.querySelector('input[type="password"], input[type="text"]') : null;
        const icon = toggle.querySelector("img");

        if (!input || !icon) {
            return;
        }

        const isHidden = input.getAttribute("type") === "password";

        input.setAttribute("type", isHidden ? "text" : "password");
        icon.setAttribute("src", isHidden ? "../img/ojo.png" : "../img/ojo_cerrado.png");
        icon.setAttribute("alt", isHidden ? "Ocultar contrasena" : "Mostrar contrasena");
    });
});
