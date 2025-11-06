<?php
/**
 * ANIME CARD COMPONENT
 * File: components/anime-card.php
 * 
 * Reusable component untuk menampilkan anime card
 * 
 * Usage:
 * include 'components/anime-card.php';
 * renderAnimeCard($animeData);
 * 
 * Required $animeData array keys:
 * - id (int)
 * - title (string)
 * - cover_image (string)
 * - rating (float)
 * - status (string: 'Ongoing' or 'Completed')
 * - total_episodes (int)
 * - release_year (int) - optional
 */

/**
 * Function to render single anime card
 * @param array $anime - Anime data array
 * @return void - Outputs HTML
 */
function renderAnimeCard($anime) {
    // Default values jika data tidak lengkap
    $id = $anime['id'] ?? 0;
    $title = $anime['title'] ?? 'Unknown Title';
    $coverImage = $anime['cover_image'] ?? 'assets/images/placeholders/no-image.jpg';
    $rating = $anime['rating'] ?? 0;
    $status = $anime['status'] ?? 'Unknown';
    $totalEpisodes = $anime['total_episodes'] ?? 0;
    $releaseYear = $anime['release_year'] ?? '';
    
    // Sanitize output
    $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $coverImage = htmlspecialchars($coverImage, ENT_QUOTES, 'UTF-8');
    $status = htmlspecialchars($status, ENT_QUOTES, 'UTF-8');
    
    // Badge class berdasarkan status
    $badgeClass = $status === 'Ongoing' ? 'badge-ongoing' : 'badge-completed';
    
    // Format rating
    $formattedRating = number_format($rating, 1);
    
    // URL ke detail page
    $detailUrl = "anime-detail.php?id=" . $id;
    ?>
    
    <article class="anime-card" data-anime-id="<?php echo $id; ?>">
        <a href="<?php echo $detailUrl; ?>" class="anime-card-link">
            <!-- Anime Cover Image -->
            <div class="anime-card-image">
                <img 
                    src="<?php echo $coverImage; ?>" 
                    alt="<?php echo $title; ?>"
                    loading="lazy"
                    onerror="this.src='assets/images/placeholders/no-image.jpg'"
                >
                
                <!-- Status Badge -->
                <span class="anime-card-badge badge <?php echo $badgeClass; ?>">
                    <?php echo $status; ?>
                </span>
                
                <!-- Rating Badge -->
                <?php if ($rating > 0): ?>
                <div class="anime-card-rating">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span><?php echo $formattedRating; ?></span>
                </div>
                <?php endif; ?>

                <!-- Play Overlay (muncul saat hover) -->
                <div class="anime-card-overlay">
                    <div class="play-button">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="white">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Anime Info -->
            <div class="anime-card-content">
                <h3 class="anime-card-title" title="<?php echo $title; ?>">
                    <?php echo $title; ?>
                </h3>
                
                <div class="anime-card-info">
                    <div class="anime-card-episodes">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect>
                            <line x1="7" y1="2" x2="7" y2="22"></line>
                            <line x1="17" y1="2" x2="17" y2="22"></line>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <line x1="2" y1="7" x2="7" y2="7"></line>
                            <line x1="2" y1="17" x2="7" y2="17"></line>
                            <line x1="17" y1="17" x2="22" y2="17"></line>
                            <line x1="17" y1="7" x2="22" y2="7"></line>
                        </svg>
                        <span>
                            <?php 
                            if ($status === 'Ongoing' && $totalEpisodes > 0) {
                                echo "Ep " . $totalEpisodes;
                            } elseif ($totalEpisodes > 0) {
                                echo $totalEpisodes . " Eps";
                            } else {
                                echo "N/A";
                            }
                            ?>
                        </span>
                    </div>
                    
                    <?php if ($releaseYear): ?>
                    <div class="anime-card-year">
                        <?php echo $releaseYear; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </a>

        <!-- Quick Actions (Bookmark, Share) -->
        <div class="anime-card-actions">
            <button class="card-action-btn bookmark-btn" data-anime-id="<?php echo $id; ?>" title="Tambah ke Watchlist">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                </svg>
            </button>
            <button class="card-action-btn share-btn" data-anime-id="<?php echo $id; ?>" title="Share">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="18" cy="5" r="3"></circle>
                    <circle cx="6" cy="12" r="3"></circle>
                    <circle cx="18" cy="19" r="3"></circle>
                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                </svg>
            </button>
        </div>
    </article>
    
    <?php
}

/**
 * Function to render multiple anime cards in a grid
 * @param array $animeList - Array of anime data
 * @param string $gridClass - Additional CSS class for grid (optional)
 * @return void - Outputs HTML
 */
function renderAnimeGrid($animeList, $gridClass = '') {
    if (empty($animeList)) {
        echo '<div class="no-anime-found">';
        echo '<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
        echo '<circle cx="12" cy="12" r="10"></circle>';
        echo '<line x1="12" y1="8" x2="12" y2="12"></line>';
        echo '<line x1="12" y1="16" x2="12.01" y2="16"></line>';
        echo '</svg>';
        echo '<p>Tidak ada anime ditemukan</p>';
        echo '</div>';
        return;
    }
    
    echo '<div class="anime-grid ' . htmlspecialchars($gridClass) . '">';
    foreach ($animeList as $anime) {
        renderAnimeCard($anime);
    }
    echo '</div>';
}
?>

<style>
/* Additional Anime Card Specific Styles */

.anime-card-link {
    display: block;
    color: inherit;
}

.anime-card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity var(--transition-base);
}

.anime-card:hover .anime-card-overlay {
    opacity: 1;
}

.play-button {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    display: flex;
    align-items: center;
    justify-content: center;
    transform: scale(0.8);
    transition: transform var(--transition-base);
    box-shadow: 0 4px 20px rgba(99, 102, 241, 0.5);
}

.anime-card:hover .play-button {
    transform: scale(1);
}

.anime-card-actions {
    position: absolute;
    bottom: 60px;
    right: var(--spacing-xs);
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    opacity: 0;
    transform: translateX(10px);
    transition: all var(--transition-base);
}

.anime-card:hover .anime-card-actions {
    opacity: 1;
    transform: translateX(0);
}

.card-action-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(10px);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all var(--transition-fast);
}

.card-action-btn:hover {
    background-color: var(--primary-color);
    transform: scale(1.1);
}

.bookmark-btn.active {
    background-color: var(--secondary-color);
}

.bookmark-btn.active svg {
    fill: currentColor;
}

/* No Anime Found State */
.no-anime-found {
    grid-column: 1 / -1;
    text-align: center;
    padding: var(--spacing-xl) 0;
    color: var(--text-muted);
}

.no-anime-found svg {
    margin: 0 auto var(--spacing-md);
    opacity: 0.5;
}

.no-anime-found p {
    font-size: 1.1rem;
}

/* Skeleton Loading for Anime Cards */
.anime-card.skeleton .anime-card-image {
    background: linear-gradient(90deg, var(--bg-secondary) 25%, var(--bg-hover) 50%, var(--bg-secondary) 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

.anime-card.skeleton .anime-card-title,
.anime-card.skeleton .anime-card-info {
    background: var(--bg-hover);
    color: transparent;
    border-radius: var(--radius-sm);
}

/* Responsive adjustments */
@media (max-width: 480px) {
    .anime-grid {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: var(--spacing-sm);
    }
    
    .anime-card-title {
        font-size: 0.85rem;
    }
    
    .anime-card-info {
        font-size: 0.75rem;
    }
    
    .play-button {
        width: 50px;
        height: 50px;
    }
}
</style>

<script>
// Anime Card Interactive Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Bookmark functionality
    const bookmarkBtns = document.querySelectorAll('.bookmark-btn');
    
    bookmarkBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const animeId = this.getAttribute('data-anime-id');
            this.classList.toggle('active');
            
            // TODO: Save to database/localStorage
            if (this.classList.contains('active')) {
                console.log('Added to watchlist:', animeId);
                showNotification('Ditambahkan ke Watchlist');
            } else {
                console.log('Removed from watchlist:', animeId);
                showNotification('Dihapus dari Watchlist');
            }
        });
    });
    
    // Share functionality
    const shareBtns = document.querySelectorAll('.share-btn');
    
    shareBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const animeId = this.getAttribute('data-anime-id');
            const animeCard = this.closest('.anime-card');
            const animeTitle = animeCard.querySelector('.anime-card-title').textContent;
            const animeUrl = window.location.origin + '/anime-detail.php?id=' + animeId;
            
            // Check if Web Share API is supported
            if (navigator.share) {
                navigator.share({
                    title: animeTitle,
                    text: 'Check out this anime: ' + animeTitle,
                    url: animeUrl
                }).catch(err => console.log('Error sharing:', err));
            } else {
                // Fallback: Copy to clipboard
                copyToClipboard(animeUrl);
                showNotification('Link copied to clipboard!');
            }
        });
    });
});

// Helper function to copy text to clipboard
function copyToClipboard(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
}

// Helper function to show notification
function showNotification(message) {
    // Simple notification (akan di-improve nanti dengan toast notification)
    const notification = document.createElement('div');
    notification.className = 'simple-notification';
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        background: var(--bg-secondary);
        color: var(--text-primary);
        padding: 1rem 2rem;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-xl);
        z-index: 9999;
        animation: slideUp 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideDown 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 2000);
}

// Animation for notification
const style = document.createElement('style');
style.textContent = `
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }
    
    @keyframes slideDown {
        from {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
        to {
            opacity: 0;
            transform: translateX(-50%) translateY(20px);
        }
    }
`;
document.head.appendChild(style);
</script>