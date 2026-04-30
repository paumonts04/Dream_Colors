const siteHeader = document.querySelector(".site-header");

if (siteHeader) {
    const updateHeader = () => {
        const opacity = Math.min(window.scrollY / 220, 1);

        siteHeader.style.setProperty("--header-bg-opacity", opacity.toFixed(2));
        siteHeader.classList.toggle("is-scrolled", window.scrollY > 80);
    };

    updateHeader();
    window.addEventListener("scroll", updateHeader, { passive: true });
}
