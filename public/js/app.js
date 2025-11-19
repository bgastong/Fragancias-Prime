//Carousel marcas
document.addEventListener('DOMContentLoaded', function () {

    const track = document.getElementById('carouselMarcaTrack');
    const slides = document.querySelectorAll('.carousel-marca-slide');

    const btnPrev = document.getElementById('carouselMarcaPrev');
    const btnNext = document.getElementById('carouselMarcaNext');

    let index = 0;

    function updateSlide() {
        track.style.transform = `translateX(-${index * 100}%)`;
    }

    btnNext.addEventListener('click', () => {
        index = (index + 1) % slides.length;
        updateSlide();
    });

    btnPrev.addEventListener('click', () => {
        index = (index - 1 + slides.length) % slides.length;
        updateSlide();
    });

});

