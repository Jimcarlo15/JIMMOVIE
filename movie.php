<?php
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: index.php'); exit; }

$movie = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$movie->execute([$id]);
$m = $movie->fetch();
if (!$m) { header('Location: index.php'); exit; }

function renderStars(float $rating): string {
    $full = (int)round($rating / 2);
    $full = max(0, min(5, $full));
    $empty = 5 - $full;
    return str_repeat('★', $full) . str_repeat('☆', $empty);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($m['title']) ?> — Movie Catalog</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header class="header-container">
    <div class="header-inner">
        <a href="index.php" class="header-logo">
            <span class="clapper-icon">🎬</span>
            <span class="logo-title">Movie Catalog</span>
        </a>
        <div>
            <a href="index.php" class="yellow-gloss-btn" style="text-decoration:none; padding:7px 18px; display:inline-block;">&laquo; Back to Catalog</a>
        </div>
    </div>
</header>

<div class="main-wrapper" style="margin-top:25px;">
    <div class="movie-detail-box">
        <div class="detail-left-col">
            <div class="detail-poster-frame">
                <?php if ($m['poster_url']): ?>
                <img src="<?= htmlspecialchars($m['poster_url']) ?>" alt="<?= htmlspecialchars($m['title']) ?>" class="detail-poster-img">
                <?php else: ?>
                <div class="no-poster-box" style="height:350px;">No Poster</div>
                <?php endif; ?>
            </div>
            
            <div class="detail-rating-widget">
                <div class="rating-stars-large"><?= renderStars((float)$m['rating']) ?></div>
                <div class="rating-score-large"><?= number_format($m['rating'], 1) ?> <span class="rating-max">/ 10</span></div>
                <div class="unplayable-notice">ℹ️ Catalog &amp; Rating only — Non-Playable</div>
            </div>
        </div>

        <div class="detail-right-col">
            <h1 class="detail-movie-title"><?= htmlspecialchars($m['title']) ?></h1>

            <table class="detail-meta-table">
                <tr>
                    <td class="meta-label">Category:</td>
                    <td class="meta-value"><strong><?= htmlspecialchars($m['category'] ?? 'Hollywood') ?></strong></td>
                </tr>
                <tr>
                    <td class="meta-label">Genre:</td>
                    <td class="meta-value"><strong><?= htmlspecialchars($m['genre']) ?></strong></td>
                </tr>
                <tr>
                    <td class="meta-label">Director:</td>
                    <td class="meta-value"><?= htmlspecialchars($m['director'] ?? 'Unknown') ?></td>
                </tr>
                <tr>
                    <td class="meta-label">Release Year:</td>
                    <td class="meta-value"><?= $m['year'] ?></td>
                </tr>
                <tr>
                    <td class="meta-label">Rating:</td>
                    <td class="meta-value"><span style="color:#f5b300; font-weight:bold;">★ <?= number_format($m['rating'], 1) ?></span> (out of 10)</td>
                </tr>
            </table>

            <div class="detail-section-title">Plot Synopsis &amp; Description</div>
            <div class="detail-description-content">
                <p><?= nl2br(htmlspecialchars($m['description'])) ?></p>
            </div>

            <div class="detail-actions-row">
                <a href="index.php?edit=<?= $m['id'] ?>" class="yellow-gloss-btn" style="text-decoration:none;">✏️ Edit This Movie</a>
                <form method="POST" action="add_movie.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this movie?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                    <button type="submit" class="btn-detail-delete">🗑️ Delete Movie</button>
                </form>
                <a href="index.php" class="btn-catalog-cancel" style="text-decoration:none;">Back to List</a>
            </div>
        </div>
    </div>
</div>

<footer class="catalog-footer">
    <p>Movie Catalog &copy; <?= date('Y') ?> &bull; Movies with Ratings, Images &amp; Descriptions &bull; Non-Playable</p>
</footer>

<script src="assets/script.js"></script>
</body>
</html>
