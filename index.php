<?php
/**
 * HOMEPAGE - AnimeStream
 * File: index.php
 * 
 * Main landing page dengan banner slider dan anime sections
 */

// Start session
session_start();

// Include database connection
require_once 'config/database.php';

// Include anime card component
require_once 'components/anime-card.php';

// Page settings
$pageTitle = "Home - AnimeStream | Nonton Anime Streaming Subtitle Indonesia";
$pageDescription = "Nonton anime terbaru dan terpopuler dengan subtitle Indonesia. Streaming anime ongoing dan completed dengan kualitas HD.";

// Fetch data for banner slider (anime populer untuk slider)
$queryBanner = "SELECT a.*, GROUP_CONCAT(g.name SEPARATOR ', ') as genres 
                FROM anime a 
                LEFT JOIN anime_genres ag ON a.id = ag.anime_id 
                LEFT JOIN genres g ON ag.genre_id = g.id 
                WHERE a.rating >= 8.5 
                GROUP BY a.id 
                ORDER BY a.rating DESC 
                LIMIT 5";
$stmtBanner = executeQuery($conn, $queryBanner);
$bannerAnime = fetchAll($stmtBanner);

// Fetch ongoing anime
$queryOngoing = "SELECT a.* FROM anime a 
                 WHERE a.status = 'Ongoing' 
                 ORDER BY a.created_at DESC 
                 LIMIT 12";
$stmtOngoing = executeQuery($conn, $queryOngoing);
$ongoingAnime = fetchAll($stmtOngoing);

// Fetch completed anime
$queryCompleted = "SELECT a.* FROM anime a 
                   WHERE a.status = 'Completed' 
                   ORDER BY a.rating DESC 
                   LIMIT 12";
$stmtCompleted = executeQuery($conn, $queryCompleted);
$completedAnime = fetchAll($stmtCompleted);

// Fetch popular anime (based on rating)
$queryPopular = "SELECT a.* FROM anime a 
                 ORDER BY a.rating DESC 
                 LIMIT 12";
$stmtPopular = executeQuery($conn, $queryPopular);
$popularAnime = fetchAll($stmtPopular);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $pageDescription; ?>">
    <meta name="keywords" content="anime, streaming, subtitle indonesia, nonton anime, anime terbaru, anime ongoing">
    <meta name="author" content="AnimeStream">
    
    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo $pageTitle; ?>">
    <meta property="og:description" content="<?php echo $pageDescription; ?>">
    <meta property="og:image" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . '/assets/images/og-image.jpg'; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    
    <title><?php echo $pageTitle; ?></title>
</head>
<body>
    
    <!-- Header / Navbar -->
    <?php include 'includes/header.php'; ?>
    
    <!-- Main Content -->
    <main>
        <div class="container">
            
            <!-- Hero Banner Slider -->
            <section class="hero-banner">
                <?php if (!empty($bannerAnime)): ?>
                    <?php foreach ($bannerAnime as $index => $anime): ?>
                    <div class="banner-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                        <img 
                            src="<?php echo htmlspecialchars($anime['banner_image']); ?>" 
                            alt="<?php echo htmlspecialchars($anime['title']); ?>"
                            class="banner-image"
                            loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>"
                        >
                        <div class="banner-overlay">
                            <div class="banner-content">
                                <h1 class="banner-title">
                                    <?php echo htmlspecialchars($anime['title']); ?>
                                </h1>
                                <p class="banner-description">
                                    <?php 
                                    $synopsis = htmlspecialchars($anime['synopsis']);
                                    echo strlen($synopsis) > 200 ? substr($synopsis, 0, 200) . '...' : $synopsis;
                                    ?>
                                </p>
                                <div class="banner-meta">
                                    <div class="banner-rating">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                        <span><?php echo number_format($anime['rating'], 1); ?></span>
                                    </div>
                                    <span class="badge <?php echo $anime['status'] === 'Ongoing' ? 'badge-ongoing' : 'badge-completed'; ?>">
                                        <?php echo htmlspecialchars($anime['status']); ?>
                                    </span>
                                    <?php if (!empty($anime['genres'])): ?>
                                    <span><?php echo htmlspecialchars(explode(', ', $anime['genres'])[0]); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="banner-buttons">
                                    <a href="anime-detail.php?id=<?php echo $anime['id']; ?>" class="btn btn-primary">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        Tonton Sekarang
                                    </a>
                                    <a href="anime-detail.php?id=<?php echo $anime['id']; ?>" class="btn btn-secondary">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="16" x2="12" y2="12"></line>
                                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                        </svg>
                                        Detail Info
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <!-- Banner Controls -->
                    <div class="banner-controls">
                        <button class="banner-control-btn banner-prev" aria-label="Previous slide">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M15 18l-6-6 6-6"/>
                            </svg>
                        </button>
                        <button class="banner-control-btn banner-next" aria-label="Next slide">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 18l6-6-6-6"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Banner Dots -->
                    <div class="banner-dots"></div>
                <?php else: ?>
                    <div class="banner-slide active">
                        <div class="banner-overlay">
                            <div class="banner-content">
                                <h1 class="banner-title">Selamat Datang di AnimeStream</h1>
                                <p class="banner-description">Platform streaming anime terbaik dengan koleksi lengkap</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
            
            <!-- Ad Space - Top Banner -->
            <div class="ad-container ad-banner">
                <div class="ad-label">Advertisement</div>
                <!-- Ad code akan ditaruh disini -->
                <div style="padding: 20px; color: var(--text-muted);">
                    [728x90 Top Banner Ad Space]
                </div>
            </div>
            
            <!-- Section: Sedang Tayang (Ongoing) -->
            <section class="section">
                <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg);">
                    <h2 class="section-title">Sedang Tayang</h2>
                    <a href="ongoing.php" class="btn btn-secondary" style="padding: 0.5rem 1rem;">
                        Lihat Semua
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                
                <?php if (!empty($ongoingAnime)): ?>
                    <?php renderAnimeGrid($ongoingAnime); ?>
                <?php else: ?>
                    <p style="text-align: center; color: var(--text-muted); padding: var(--spacing-xl) 0;">
                        Belum ada anime yang sedang tayang
                    </p>
                <?php endif; ?>
            </section>
            
            <!-- Ad Space - Inline -->
            <div class="ad-container ad-inline">
                <div class="ad-label">Advertisement</div>
                <div style="padding: 15px; color: var(--text-muted);">
                    [728x90 Inline Ad Space]
                </div>
            </div>
            
            <!-- Section: Anime Populer -->
            <section class="section">
                <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg);">
                    <h2 class="section-title">Anime Populer</h2>
                    <a href="anime-list.php" class="btn btn-secondary" style="padding: 0.5rem 1rem;">
                        Lihat Semua
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                
                <?php if (!empty($popularAnime)): ?>
                    <?php renderAnimeGrid($popularAnime); ?>
                <?php else: ?>
                    <p style="text-align: center; color: var(--text-muted); padding: var(--spacing-xl) 0;">
                        Belum ada anime populer
                    </p>
                <?php endif; ?>
            </section>
            
            <!-- Section: Anime Completed -->
            <section class="section">
                <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg);">
                    <h2 class="section-title">Anime Completed</h2>
                    <a href="completed.php" class="btn btn-secondary" style="padding: 0.5rem 1rem;">
                        Lihat Semua
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                
                <?php if (!empty($completedAnime)): ?>
                    <?php renderAnimeGrid($completedAnime); ?>
                <?php else: ?>
                    <p style="text-align: center; color: var(--text-muted); padding: var(--spacing-xl) 0;">
                        Belum ada anime completed
                    </p>
                <?php endif; ?>
            </section>
            
        </div>
    </main>
    
    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- JavaScript Files -->
    <script src="assets/js/slider.js"></script>
    <script src="assets/js/main.js"></script>
    
    <!-- Additional inline script for page-specific functionality -->
    <script>
        // Page load animation
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation class to sections
            const sections = document.querySelectorAll('.section');
            sections.forEach((section, index) => {
                section.style.animationDelay = `${index * 0.1}s`;
                section.classList.add('fade-in');
            });
            
            // Log page view (untuk analytics nanti)
            console.log('Homepage loaded at:', new Date().toLocaleString());
        });
    </script>
    
</body>
</html>

<?php
// Close database connection
$conn->close();
?>