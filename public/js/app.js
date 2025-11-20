// public/js/app.js

document.addEventListener("DOMContentLoaded", function () {
    const carousel = document.getElementById("heroCarousel");
    if (!carousel) return; // por si esta vista no tiene carrusel

    const items = carousel.querySelectorAll(".carousel-item");
    const indicators = carousel.querySelectorAll(".carousel-indicators button");
    const prevBtn = carousel.querySelector(".carousel-control-prev");
    const nextBtn = carousel.querySelector(".carousel-control-next");

    let currentIndex = 0;
    const total = items.length;
    let autoSlideInterval = null;
    const AUTO_TIME = 5000; // 5 segundos

    function setActiveSlide(index) {
        // normalizar índice (para que rote)
        if (index < 0) index = total - 1;
        if (index >= total) index = 0;

        // quitar active actual
        items.forEach(item => item.classList.remove("active"));
        indicators.forEach(ind => ind.classList.remove("active"));

        // activar nuevo
        items[index].classList.add("active");
        indicators[index].classList.add("active");

        currentIndex = index;
    }

    function nextSlide() {
        setActiveSlide(currentIndex + 1);
    }

    function prevSlide() {
        setActiveSlide(currentIndex - 1);
    }

    // Eventos flechas
    if (nextBtn) {
        nextBtn.addEventListener("click", function () {
            nextSlide();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener("click", function () {
            prevSlide();
        });
    }

    // Eventos indicadores (puntitos)
    indicators.forEach((btn, index) => {
        btn.addEventListener("click", function () {
            setActiveSlide(index);
        });
    });

    // Autoplay
    function startAutoSlide() {
        autoSlideInterval = setInterval(nextSlide, AUTO_TIME);
    }

    function stopAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
            autoSlideInterval = null;
        }
    }

    // Pausar al pasar el mouse por encima
    carousel.addEventListener("mouseenter", stopAutoSlide);
    carousel.addEventListener("mouseleave", startAutoSlide);

    // Iniciar estado
    setActiveSlide(0);
    startAutoSlide();
});
