/**
 * Hero Slider - Auto-sliding background images
 */
(function() {
    function initHeroSlider() {
        var heroSlider = document.getElementById('heroSlider');
        if (!heroSlider) return;

        var slides = heroSlider.getElementsByClassName('hero-slide');
        var textSlider = document.querySelector('.hero-text-slider');
        var textSlides = textSlider ? textSlider.getElementsByClassName('hero-text-slide') : [];
        var dotsContainer = document.querySelector('.hero-slider-dots');
        var dots = dotsContainer ? dotsContainer.getElementsByClassName('hero-dot') : [];
        var totalSlides = slides.length;
        var currentSlide = 0;

        if (totalSlides < 2) return;

        function showSlide(n) {
            currentSlide = n;
            if (currentSlide >= totalSlides) currentSlide = 0;
            if (currentSlide < 0) currentSlide = totalSlides - 1;

            for (var i = 0; i < totalSlides; i++) {
                slides[i].className = slides[i].className.replace(' active', '');
                if (textSlides[i]) textSlides[i].className = textSlides[i].className.replace(' active', '');
                if (dots[i]) dots[i].className = dots[i].className.replace(' active', '');
            }

            slides[currentSlide].className += ' active';
            if (textSlides[currentSlide]) textSlides[currentSlide].className += ' active';
            if (dots[currentSlide]) dots[currentSlide].className += ' active';
        }

        setInterval(function() {
            showSlide(currentSlide + 1);
        }, 5000);

        for (var i = 0; i < dots.length; i++) {
            (function(index) {
                dots[index].onclick = function() {
                    showSlide(index);
                };
            })(i);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initHeroSlider);
    } else {
        initHeroSlider();
    }
})();
