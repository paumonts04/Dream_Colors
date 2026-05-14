const siteHeader = document.querySelector(".site-header");

if (siteHeader) {
    const servicesToggle = siteHeader.querySelector(".services-toggle");
    const servicesMenu = siteHeader.querySelector(".services-menu");

    const updateHeader = () => {
        const isMenuOpen = siteHeader.classList.contains("has-open-menu");
        const opacity = isMenuOpen ? 1 : Math.min(window.scrollY / 220, 1);

        siteHeader.style.setProperty("--header-bg-opacity", opacity.toFixed(2));
        siteHeader.classList.toggle("is-scrolled", window.scrollY > 80);
    };

    if (servicesToggle && servicesMenu) {
        const closeServicesMenu = () => {
            servicesMenu.classList.remove("is-open");
            siteHeader.classList.remove("has-open-menu");
            servicesToggle.setAttribute("aria-expanded", "false");
            updateHeader();
        };

        servicesToggle.addEventListener("click", () => {
            const isOpen = servicesMenu.classList.toggle("is-open");
            siteHeader.classList.toggle("has-open-menu", isOpen);
            servicesToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
            updateHeader();
        });

        document.addEventListener("click", (event) => {
            if (!siteHeader.contains(event.target)) {
                closeServicesMenu();
            }
        });

        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape") {
                closeServicesMenu();
            }
        });
    }

    updateHeader();
    window.addEventListener("scroll", updateHeader, { passive: true });
}
