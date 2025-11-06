<!-- 
    HEADER / NAVBAR COMPONENT
    File: includes/header.php
-->

<header class="header">
    <div class="container">
        <nav class="navbar">
            <!-- Logo -->
            <a href="index.php" class="logo">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7L12 12L22 7L12 2Z" fill="url(#gradient1)"/>
                    <path d="M2 17L12 22L22 17V12L12 17L2 12V17Z" fill="url(#gradient2)"/>
                    <defs>
                        <linearGradient id="gradient1" x1="2" y1="2" x2="22" y2="12" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#6366f1"/>
                            <stop offset="1" stop-color="#ec4899"/>
                        </linearGradient>
                        <linearGradient id="gradient2" x1="2" y1="12" x2="22" y2="22" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#ec4899"/>
                            <stop offset="1" stop-color="#f59e0b"/>
                        </linearGradient>
                    </defs>
                </svg>
                <span>AnimeStream</span>
            </a>

            <!-- Desktop Navigation -->
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php" class="nav-link">Home</a></li>
                <li><a href="anime-list.php" class="nav-link">Daftar Anime</a></li>
                <li><a href="ongoing.php" class="nav-link">Sedang Tayang</a></li>
                <li><a href="schedule.php" class="nav-link">Jadwal</a></li>
                <li><a href="genres.php" class="nav-link">Genre</a></li>
                
                <!-- Search Bar -->
                <li class="search-bar">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input 
                        type="text" 
                        id="searchInput" 
                        placeholder="Cari anime..." 
                        autocomplete="off"
                    >
                </li>
            </ul>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </nav>
    </div>

    <!-- Search Results Dropdown (akan muncul saat search) -->
    <div class="search-results" id="searchResults" style="display: none;">
        <div class="container">
            <div class="search-results-content">
                <!-- Results akan di-populate via JavaScript -->
            </div>
        </div>
    </div>
</header>

<style>
/* Additional Header Specific Styles */

/* Mobile Menu Toggle Animation */
.mobile-menu-toggle.active span:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
}

.mobile-menu-toggle.active span:nth-child(2) {
    opacity: 0;
}

.mobile-menu-toggle.active span:nth-child(3) {
    transform: rotate(-45deg) translate(7px, -6px);
}

/* Search Results Dropdown */
.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background-color: var(--bg-secondary);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: var(--shadow-xl);
    max-height: 400px;
    overflow-y: auto;
    z-index: 999;
}

.search-results-content {
    padding: var(--spacing-md) 0;
}

.search-result-item {
    display: flex;
    gap: var(--spacing-sm);
    padding: var(--spacing-sm);
    border-radius: var(--radius-md);
    transition: background-color var(--transition-fast);
    cursor: pointer;
}

.search-result-item:hover {
    background-color: var(--bg-hover);
}

.search-result-image {
    width: 60px;
    height: 80px;
    object-fit: cover;
    border-radius: var(--radius-sm);
    flex-shrink: 0;
}

.search-result-info {
    flex: 1;
}

.search-result-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: var(--text-primary);
}

.search-result-meta {
    font-size: 0.875rem;
    color: var(--text-secondary);
    display: flex;
    gap: var(--spacing-sm);
    flex-wrap: wrap;
}

.search-no-results {
    padding: var(--spacing-lg);
    text-align: center;
    color: var(--text-muted);
}

/* Active Nav Link */
.nav-link.active {
    color: var(--primary-color);
    background-color: var(--bg-hover);
}

/* Responsive Styles for Header */
@media (max-width: 768px) {
    .mobile-menu-toggle {
        display: flex;
        z-index: 1001;
    }

    .nav-menu {
        position: fixed;
        top: 0;
        right: -100%;
        width: 280px;
        height: 100vh;
        background-color: var(--bg-secondary);
        flex-direction: column;
        padding: 5rem 2rem 2rem;
        gap: 0;
        transition: right var(--transition-base);
        box-shadow: var(--shadow-xl);
        overflow-y: auto;
        z-index: 1000;
    }

    .nav-menu.active {
        right: 0;
    }

    .nav-menu li {
        width: 100%;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .nav-link {
        display: block;
        padding: 1rem 0;
        width: 100%;
    }

    .search-bar {
        width: 100%;
        max-width: 100%;
        margin-top: var(--spacing-md);
        border-bottom: none;
    }

    /* Mobile Menu Overlay */
    .nav-menu.active::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 280px;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: -1;
    }
}

@media (min-width: 769px) {
    .mobile-menu-toggle {
        display: none;
    }
}
</style>

<script>
// Header Interactive Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.getElementById('navMenu');
    
    if (mobileMenuToggle && navMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            navMenu.classList.toggle('active');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.navbar')) {
                mobileMenuToggle.classList.remove('active');
                navMenu.classList.remove('active');
            }
        });

        // Close menu when clicking a link
        const navLinks = navMenu.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenuToggle.classList.remove('active');
                navMenu.classList.remove('active');
            });
        });
    }

    // Search Functionality (Basic)
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    if (searchInput && searchResults) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            // Debounce search
            searchTimeout = setTimeout(() => {
                performSearch(query);
            }, 300);
        });

        // Close search results when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.search-bar') && !event.target.closest('.search-results')) {
                searchResults.style.display = 'none';
            }
        });
    }

    // Set Active Nav Link based on current page
    setActiveNavLink();
});

// Function to perform search (akan integrate dengan backend nanti)
function performSearch(query) {
    const searchResults = document.getElementById('searchResults');
    const resultsContent = searchResults.querySelector('.search-results-content');
    
    // Show loading state
    resultsContent.innerHTML = '<div class="search-no-results">Mencari...</div>';
    searchResults.style.display = 'block';

    // TODO: Fetch dari database via AJAX
    // Untuk sekarang, kita simulate dengan setTimeout
    setTimeout(() => {
        // Mock data - nanti akan diganti dengan fetch ke PHP
        const mockResults = [
            {
                id: 1,
                title: 'Demon Slayer',
                image: 'assets/images/placeholders/demon-slayer.jpg',
                status: 'Completed',
                episodes: 26
            }
        ];

        if (mockResults.length > 0) {
            let html = '';
            mockResults.forEach(result => {
                html += `
                    <a href="anime-detail.php?id=${result.id}" class="search-result-item">
                        <img src="${result.image}" alt="${result.title}" class="search-result-image" onerror="this.src='assets/images/placeholders/no-image.jpg'">
                        <div class="search-result-info">
                            <div class="search-result-title">${result.title}</div>
                            <div class="search-result-meta">
                                <span>${result.status}</span>
                                <span>•</span>
                                <span>${result.episodes} Episodes</span>
                            </div>
                        </div>
                    </a>
                `;
            });
            resultsContent.innerHTML = html;
        } else {
            resultsContent.innerHTML = '<div class="search-no-results">Tidak ada hasil untuk "' + query + '"</div>';
        }
    }, 500);
}

// Function to set active nav link
function setActiveNavLink() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage) {
            link.classList.add('active');
        }
    });
}
</script>