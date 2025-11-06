/**
 * BANNER SLIDER FUNCTIONALITY
 * File: assets/js/slider.js
 * 
 * Auto-playing banner slider dengan navigation controls
 */

class BannerSlider {
    constructor(options = {}) {
        // Default options
        this.options = {
            container: options.container || '.hero-banner',
            slideClass: options.slideClass || '.banner-slide',
            prevBtn: options.prevBtn || '.banner-prev',
            nextBtn: options.nextBtn || '.banner-next',
            dotsContainer: options.dotsContainer || '.banner-dots',
            autoPlay: options.autoPlay !== undefined ? options.autoPlay : true,
            interval: options.interval || 5000,
            pauseOnHover: options.pauseOnHover !== undefined ? options.pauseOnHover : true,
            swipe: options.swipe !== undefined ? options.swipe : true
        };

        this.container = document.querySelector(this.options.container);
        
        if (!this.container) {
            console.warn('Slider container not found');
            return;
        }

        this.slides = this.container.querySelectorAll(this.options.slideClass);
        this.currentSlide = 0;
        this.isPlaying = this.options.autoPlay;
        this.autoPlayTimer = null;
        this.touchStartX = 0;
        this.touchEndX = 0;

        this.init();
    }

    init() {
        if (this.slides.length === 0) {
            console.warn('No slides found');
            return;
        }

        // Set first slide as active
        this.slides[0].classList.add('active');

        // Create navigation dots
        this.createDots();

        // Setup event listeners
        this.setupControls();
        this.setupSwipe();
        this.setupKeyboard();

        // Start autoplay if enabled
        if (this.options.autoPlay) {
            this.startAutoPlay();
        }

        // Pause on hover
        if (this.options.pauseOnHover) {
            this.setupPauseOnHover();
        }

        // Preload next image
        this.preloadImages();
    }

    createDots() {
        const dotsContainer = this.container.querySelector(this.options.dotsContainer);
        
        if (!dotsContainer) return;

        dotsContainer.innerHTML = '';

        this.slides.forEach((slide, index) => {
            const dot = document.createElement('button');
            dot.className = 'banner-dot';
            dot.setAttribute('aria-label', `Go to slide ${index + 1}`);
            
            if (index === 0) {
                dot.classList.add('active');
            }

            dot.addEventListener('click', () => {
                this.goToSlide(index);
            });

            dotsContainer.appendChild(dot);
        });

        this.dots = dotsContainer.querySelectorAll('.banner-dot');
    }

    setupControls() {
        const prevBtn = this.container.querySelector(this.options.prevBtn);
        const nextBtn = this.container.querySelector(this.options.nextBtn);

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                this.prevSlide();
                this.resetAutoPlay();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                this.nextSlide();
                this.resetAutoPlay();
            });
        }
    }

    setupSwipe() {
        if (!this.options.swipe) return;

        this.container.addEventListener('touchstart', (e) => {
            this.touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        this.container.addEventListener('touchend', (e) => {
            this.touchEndX = e.changedTouches[0].screenX;
            this.handleSwipe();
        }, { passive: true });

        // Mouse drag support
        let isDragging = false;
        let startX = 0;

        this.container.addEventListener('mousedown', (e) => {
            isDragging = true;
            startX = e.clientX;
            this.container.style.cursor = 'grabbing';
        });

        document.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            e.preventDefault();
        });

        document.addEventListener('mouseup', (e) => {
            if (!isDragging) return;
            
            isDragging = false;
            this.container.style.cursor = 'grab';
            
            const endX = e.clientX;
            const diff = startX - endX;

            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    this.nextSlide();
                } else {
                    this.prevSlide();
                }
                this.resetAutoPlay();
            }
        });
    }

    handleSwipe() {
        const swipeThreshold = 50;
        const diff = this.touchStartX - this.touchEndX;

        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                // Swipe left - next slide
                this.nextSlide();
            } else {
                // Swipe right - previous slide
                this.prevSlide();
            }
            this.resetAutoPlay();
        }
    }

    setupKeyboard() {
        document.addEventListener('keydown', (e) => {
            // Only handle if slider is in viewport
            if (!this.isInViewport()) return;

            if (e.key === 'ArrowLeft') {
                this.prevSlide();
                this.resetAutoPlay();
            } else if (e.key === 'ArrowRight') {
                this.nextSlide();
                this.resetAutoPlay();
            }
        });
    }

    setupPauseOnHover() {
        this.container.addEventListener('mouseenter', () => {
            this.pauseAutoPlay();
        });

        this.container.addEventListener('mouseleave', () => {
            if (this.isPlaying) {
                this.startAutoPlay();
            }
        });
    }

    goToSlide(index) {
        // Remove active class from current slide
        this.slides[this.currentSlide].classList.remove('active');
        
        if (this.dots) {
            this.dots[this.currentSlide].classList.remove('active');
        }

        // Set new current slide
        this.currentSlide = index;

        // Add active class to new slide
        this.slides[this.currentSlide].classList.add('active');
        
        if (this.dots) {
            this.dots[this.currentSlide].classList.add('active');
        }

        // Preload next image
        this.preloadImages();

        // Trigger custom event
        this.container.dispatchEvent(new CustomEvent('slideChange', {
            detail: { currentSlide: this.currentSlide }
        }));
    }

    nextSlide() {
        const next = (this.currentSlide + 1) % this.slides.length;
        this.goToSlide(next);
    }

    prevSlide() {
        const prev = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
        this.goToSlide(prev);
    }

    startAutoPlay() {
        if (this.autoPlayTimer) {
            clearInterval(this.autoPlayTimer);
        }

        this.autoPlayTimer = setInterval(() => {
            this.nextSlide();
        }, this.options.interval);
    }

    pauseAutoPlay() {
        if (this.autoPlayTimer) {
            clearInterval(this.autoPlayTimer);
            this.autoPlayTimer = null;
        }
    }

    resetAutoPlay() {
        if (this.isPlaying) {
            this.pauseAutoPlay();
            this.startAutoPlay();
        }
    }

    preloadImages() {
        // Preload next slide image
        const nextIndex = (this.currentSlide + 1) % this.slides.length;
        const nextSlide = this.slides[nextIndex];
        const img = nextSlide.querySelector('img');
        
        if (img && !img.complete) {
            const preloadImg = new Image();
            preloadImg.src = img.src;
        }
    }

    isInViewport() {
        const rect = this.container.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    // Public methods to control slider
    play() {
        this.isPlaying = true;
        this.startAutoPlay();
    }

    pause() {
        this.isPlaying = false;
        this.pauseAutoPlay();
    }

    destroy() {
        this.pauseAutoPlay();
        // Remove event listeners if needed
    }
}

// Initialize slider when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if hero banner exists
    const heroBanner = document.querySelector('.hero-banner');
    
    if (heroBanner) {
        // Initialize main banner slider
        const mainSlider = new BannerSlider({
            container: '.hero-banner',
            autoPlay: true,
            interval: 5000,
            pauseOnHover: true,
            swipe: true
        });

        // Optional: Add progress bar for autoplay
        addSliderProgressBar(mainSlider);

        // Optional: Listen to slide change events
        heroBanner.addEventListener('slideChange', function(e) {
            console.log('Slide changed to:', e.detail.currentSlide);
            
            // Add animation to banner content
            const activeSlide = heroBanner.querySelector('.banner-slide.active');
            const bannerContent = activeSlide.querySelector('.banner-content');
            
            if (bannerContent) {
                bannerContent.style.animation = 'none';
                setTimeout(() => {
                    bannerContent.style.animation = 'fadeInUp 0.6s ease forwards';
                }, 10);
            }
        });
    }
});

/**
 * Add progress bar to show autoplay progress
 * @param {BannerSlider} slider - Slider instance
 */
function addSliderProgressBar(slider) {
    const container = slider.container;
    const progressBar = document.createElement('div');
    progressBar.className = 'slider-progress-bar';
    progressBar.innerHTML = '<div class="slider-progress-fill"></div>';
    
    container.appendChild(progressBar);

    const progressFill = progressBar.querySelector('.slider-progress-fill');
    let startTime;
    let animationFrame;

    function updateProgress(timestamp) {
        if (!startTime) startTime = timestamp;
        const elapsed = timestamp - startTime;
        const progress = (elapsed / slider.options.interval) * 100;

        progressFill.style.width = Math.min(progress, 100) + '%';

        if (progress < 100 && slider.isPlaying) {
            animationFrame = requestAnimationFrame(updateProgress);
        }
    }

    // Start progress animation
    if (slider.isPlaying) {
        animationFrame = requestAnimationFrame(updateProgress);
    }

    // Reset on slide change
    container.addEventListener('slideChange', () => {
        cancelAnimationFrame(animationFrame);
        startTime = null;
        progressFill.style.width = '0%';
        if (slider.isPlaying) {
            animationFrame = requestAnimationFrame(updateProgress);
        }
    });

    // Pause/resume on hover
    container.addEventListener('mouseenter', () => {
        cancelAnimationFrame(animationFrame);
    });

    container.addEventListener('mouseleave', () => {
        if (slider.isPlaying) {
            startTime = null;
            progressFill.style.width = '0%';
            animationFrame = requestAnimationFrame(updateProgress);
        }
    });
}

// Add CSS for progress bar
const style = document.createElement('style');
style.textContent = `
    .slider-progress-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: rgba(255, 255, 255, 0.1);
        overflow: hidden;
        z-index: 10;
    }

    .slider-progress-fill {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        transition: width 0.1s linear;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Make container draggable */
    .hero-banner {
        cursor: grab;
        user-select: none;
    }

    .hero-banner:active {
        cursor: grabbing;
    }
`;
document.head.appendChild(style);

// Export for use in other modules if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BannerSlider;
}