/**
 * Lazy Load Images for Performance
 * Enhanced version with native lazy loading support
 */
(function() {
    'use strict';

    // Add loading="lazy" to all images without it
    function addLazyLoadingToImages() {
        const images = document.querySelectorAll('img:not([loading])');
        images.forEach(function(img) {
            // Skip images that are above the fold (first 2 images typically)
            const rect = img.getBoundingClientRect();
            const isAboveFold = rect.top >= 0 && rect.top <= window.innerHeight;

            if (!isAboveFold) {
                img.setAttribute('loading', 'lazy');
            }
        });
    }

    // Check if IntersectionObserver is supported
    if ('IntersectionObserver' in window) {
        const lazyImages = document.querySelectorAll('img[data-src], img[loading="lazy"]');

        const imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const img = entry.target;

                    // Load the image
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }

                    if (img.dataset.srcset) {
                        img.srcset = img.dataset.srcset;
                        img.removeAttribute('data-srcset');
                    }

                    img.classList.remove('lazy');
                    img.classList.add('lazyloaded');
                    imageObserver.unobserve(img);
                }
            });
        }, {
            rootMargin: '100px 0px',
            threshold: 0.01
        });

        lazyImages.forEach(function(img) {
            img.classList.add('lazyload');
            imageObserver.observe(img);
        });
    } else {
        // Fallback for browsers without IntersectionObserver
        const lazyImages = document.querySelectorAll('img[data-src]');
        lazyImages.forEach(function(img) {
            if (img.dataset.src) {
                img.src = img.dataset.src;
            }
            if (img.dataset.srcset) {
                img.srcset = img.dataset.srcset;
            }
        });
    }

    // Run on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', addLazyLoadingToImages);
    } else {
        addLazyLoadingToImages();
    }

    // Also run after dynamic content loads
    const observer = new MutationObserver(function(mutations) {
        addLazyLoadingToImages();
    });

    if (document.body) {
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
})();
