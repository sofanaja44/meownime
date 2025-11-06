/**
 * MAIN JAVASCRIPT
 * File: assets/js/main.js
 * 
 * General functionality untuk website anime streaming
 */

// Global state management
const AppState = {
    isLoggedIn: false,
    watchlist: [],
    searchHistory: [],
    theme: 'dark'
};

// ==========================================
// INITIALIZATION
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('AnimeStream initialized');
    
    // Load saved data from localStorage
    loadAppState();
    
    // Initialize features
    initLazyLoading();
    initScrollAnimations();
    initTooltips();
    initLoadMore();
    initQuickView();
    initThemeToggle();
    
    // Performance monitoring
    logPageLoadTime();
});

// ==========================================
// LOCAL STORAGE MANAGEMENT
// ==========================================

function loadAppState() {
    try {
        // Load watchlist
        const savedWatchlist = localStorage.getItem('watchlist');
        if (savedWatchlist) {
            AppState.watchlist = JSON.parse(savedWatchlist);
            updateWatchlistUI();
        }
        
        // Load search history
        const savedSearchHistory = localStorage.getItem('searchHistory');
        if (savedSearchHistory) {
            AppState.searchHistory = JSON.parse(savedSearchHistory);
        }
        
        // Load theme preference
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            AppState.theme = savedTheme;
            applyTheme(savedTheme);
        }
    } catch (error) {
        console.error('Error loading app state:', error);
    }
}

function saveWatchlist() {
    try {
        localStorage.setItem('watchlist', JSON.stringify(AppState.watchlist));
    } catch (error) {
        console.error('Error saving watchlist:', error);
    }
}

function addToWatchlist(animeId) {
    if (!AppState.watchlist.includes(animeId)) {
        AppState.watchlist.push(animeId);
        saveWatchlist();
        return true;
    }
    return false;
}

function removeFromWatchlist(animeId) {
    const index = AppState.watchlist.indexOf(animeId);
    if (index > -1) {
        AppState.watchlist.splice(index, 1);
        saveWatchlist();
        return true;
    }
    return false;
}

function updateWatchlistUI() {
    // Update all bookmark buttons based on watchlist
    document.querySelectorAll('.bookmark-btn').forEach(btn => {
        const animeId = parseInt(btn.getAttribute('data-anime-id'));
        if (AppState.watchlist.includes(animeId)) {
            btn.classList.add('active');
        }
    });
}

// ==========================================
// LAZY LOADING IMAGES
// ==========================================

function initLazyLoading() {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                
                // Load the image
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                }
                
                // Add loaded class for animation
                img.addEventListener('load', () => {
                    img.classList.add('loaded');
                });
                
                // Stop observing this image
                observer.unobserve(img);
            }
        });
    }, {
        rootMargin: '50px'
    });
    
    // Observe all images with data-src attribute
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// ==========================================
// SCROLL ANIMATIONS
// ==========================================

function initScrollAnimations() {
    const animationObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    // Observe elements with animate class
    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        animationObserver.observe(el);
    });
    
    // Observe anime cards for stagger animation
    document.querySelectorAll('.anime-card').forEach((card, index) => {
        card.style.animationDelay = `${index * 0.05}s`;
        animationObserver.observe(card);
    });
}

// ==========================================
// TOOLTIPS
// ==========================================

function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function(e) {
            const tooltipText = this.getAttribute('data-tooltip');
            const tooltip = createTooltip(tooltipText);
            document.body.appendChild(tooltip);
            positionTooltip(tooltip, this);
        });
        
        element.addEventListener('mouseleave', function() {
            const tooltip = document.querySelector('.custom-tooltip');
            if (tooltip) {
                tooltip.remove();
            }
        });
    });
}

function createTooltip(text) {
    const tooltip = document.createElement('div');
    tooltip.className = 'custom-tooltip';
    tooltip.textContent = text;
    return tooltip;
}

function positionTooltip(tooltip, target) {
    const rect = target.getBoundingClientRect();
    tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
    tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
}

// ==========================================
// LOAD MORE FUNCTIONALITY
// ==========================================

function initLoadMore() {
    const loadMoreBtns = document.querySelectorAll('.load-more-btn');
    
    loadMoreBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const section = this.getAttribute('data-section');
            const currentPage = parseInt(this.getAttribute('data-page') || 1);
            const nextPage = currentPage + 1;
            
            // Show loading state
            this.classList.add('loading');
            this.textContent = 'Loading...';
            
            // Simulate AJAX call (replace with actual API call)
            setTimeout(() => {
                loadMoreAnime(section, nextPage, this);
            }, 1000);
        });
    });
}

function loadMoreAnime(section, page, btn) {
    // TODO: Replace with actual AJAX call to PHP
    console.log(`Loading more anime for ${section}, page ${page}`);
    
    // Mock data - akan diganti dengan fetch ke backend
    const mockAnime = generateMockAnimeData(6);
    
    // Find the grid to append to
    const grid = btn.previousElementSibling;
    
    if (grid && grid.classList.contains('anime-grid')) {
        mockAnime.forEach(anime => {
            const card = createAnimeCardElement(anime);
            grid.appendChild(card);
        });
        
        // Update button state
        btn.classList.remove('loading');
        btn.textContent = 'Load More';
        btn.setAttribute('data-page', page);
        
        // Re-initialize lazy loading for new images
        initLazyLoading();
        
        // If no more data, hide button
        // if (page >= maxPages) {
        //     btn.style.display = 'none';
        // }
    }
}

// ==========================================
// QUICK VIEW MODAL
// ==========================================

function initQuickView() {
    // Add quick view buttons to anime cards
    document.querySelectorAll('.anime-card').forEach(card => {
        card.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            const animeId = this.getAttribute('data-anime-id');
            showQuickView(animeId);
        });
    });
}

function showQuickView(animeId) {
    // TODO: Fetch anime data and show modal
    console.log('Quick view for anime:', animeId);
    
    const modal = document.createElement('div');
    modal.className = 'quick-view-modal';
    modal.innerHTML = `
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <button class="modal-close">&times;</button>
            <div class="modal-body">
                <h2>Quick View - Anime ID: ${animeId}</h2>
                <p>Loading anime details...</p>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close on overlay click
    modal.querySelector('.modal-overlay').addEventListener('click', () => {
        modal.remove();
    });
    
    // Close on button click
    modal.querySelector('.modal-close').addEventListener('click', () => {
        modal.remove();
    });
    
    // Close on ESC key
    document.addEventListener('keydown', function escHandler(e) {
        if (e.key === 'Escape') {
            modal.remove();
            document.removeEventListener('keydown', escHandler);
        }
    });
}

// ==========================================
// THEME TOGGLE
// ==========================================

function initThemeToggle() {
    const themeToggle = document.querySelector('.theme-toggle');
    
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const newTheme = AppState.theme === 'dark' ? 'light' : 'dark';
            AppState.theme = newTheme;
            applyTheme(newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }
}

function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    
    // Update theme toggle button if exists
    const themeToggle = document.querySelector('.theme-toggle');
    if (themeToggle) {
        themeToggle.innerHTML = theme === 'dark' 
            ? '<svg>...</svg>' // Sun icon
            : '<svg>...</svg>'; // Moon icon
    }
}

// ==========================================
// SEARCH FUNCTIONALITY ENHANCEMENT
// ==========================================

function saveSearchHistory(query) {
    if (query && query.length > 2) {
        AppState.searchHistory.unshift(query);
        
        // Keep only last 10 searches
        AppState.searchHistory = AppState.searchHistory.slice(0, 10);
        
        // Remove duplicates
        AppState.searchHistory = [...new Set(AppState.searchHistory)];
        
        localStorage.setItem('searchHistory', JSON.stringify(AppState.searchHistory));
    }
}

function getSearchSuggestions() {
    return AppState.searchHistory;
}

// ==========================================
// UTILITY FUNCTIONS
// ==========================================

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

function formatNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    }
    if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

function timeAgo(date) {
    const seconds = Math.floor((new Date() - new Date(date)) / 1000);
    
    let interval = seconds / 31536000;
    if (interval > 1) return Math.floor(interval) + ' tahun lalu';
    
    interval = seconds / 2592000;
    if (interval > 1) return Math.floor(interval) + ' bulan lalu';
    
    interval = seconds / 86400;
    if (interval > 1) return Math.floor(interval) + ' hari lalu';
    
    interval = seconds / 3600;
    if (interval > 1) return Math.floor(interval) + ' jam lalu';
    
    interval = seconds / 60;
    if (interval > 1) return Math.floor(interval) + ' menit lalu';
    
    return 'Baru saja';
}

function generateMockAnimeData(count) {
    const mockData = [];
    for (let i = 0; i < count; i++) {
        mockData.push({
            id: Date.now() + i,
            title: `Anime Title ${i + 1}`,
            cover_image: 'assets/images/placeholders/no-image.jpg',
            rating: (Math.random() * 3 + 7).toFixed(1),
            status: Math.random() > 0.5 ? 'Ongoing' : 'Completed',
            total_episodes: Math.floor(Math.random() * 24) + 1
        });
    }
    return mockData;
}

function createAnimeCardElement(anime) {
    const card = document.createElement('article');
    card.className = 'anime-card';
    card.setAttribute('data-anime-id', anime.id);
    
    const badgeClass = anime.status === 'Ongoing' ? 'badge-ongoing' : 'badge-completed';
    
    card.innerHTML = `
        <a href="anime-detail.php?id=${anime.id}" class="anime-card-link">
            <div class="anime-card-image">
                <img src="${anime.cover_image}" alt="${anime.title}" loading="lazy">
                <span class="anime-card-badge badge ${badgeClass}">${anime.status}</span>
                <div class="anime-card-rating">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span>${anime.rating}</span>
                </div>
                <div class="anime-card-overlay">
                    <div class="play-button">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="white">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="anime-card-content">
                <h3 class="anime-card-title">${anime.title}</h3>
                <div class="anime-card-info">
                    <div class="anime-card-episodes">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect>
                        </svg>
                        <span>${anime.total_episodes} Eps</span>
                    </div>
                </div>
            </div>
        </a>
        <div class="anime-card-actions">
            <button class="card-action-btn bookmark-btn" data-anime-id="${anime.id}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                </svg>
            </button>
        </div>
    `;
    
    return card;
}

// ==========================================
// PERFORMANCE MONITORING
// ==========================================

function logPageLoadTime() {
    window.addEventListener('load', function() {
        const perfData = window.performance.timing;
        const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
        console.log(`Page load time: ${pageLoadTime}ms`);
    });
}

// ==========================================
// SMOOTH SCROLL FOR ANCHOR LINKS
// ==========================================

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href !== '#' && href !== '#!') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// ==========================================
// GLOBAL ERROR HANDLER
// ==========================================

window.addEventListener('error', function(e) {
    console.error('Global error:', e.message);
    // Optional: Send error to logging service
});

// ==========================================
// EXPOSE GLOBAL API
// ==========================================

window.AnimeStreamApp = {
    addToWatchlist,
    removeFromWatchlist,
    saveSearchHistory,
    getSearchSuggestions,
    formatNumber,
    timeAgo,
    debounce,
    throttle
};

console.log('AnimeStream App ready!');