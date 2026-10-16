<?php
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: index.php'); exit; }

$movie = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$movie->execute([$id]);
$m = $movie->fetch();
if (!$m) { header('Location: index.php'); exit; }

function stars(float $r): string {
    $full  = (int)floor($r / 2);
    $half  = ($r / 2 - $full) >= 0.25 ? 1 : 0;
    $empty = 5 - $full - $half;
    return str_repeat('★', $full) . str_repeat('½', $half) . str_repeat('☆', $empty);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($m['title']) ?> — JimMovie</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="grain"></div>

<header class="site-header">
    <div class="header-inner">
        <a href="index.php" class="logo">
            <span class="logo-icon">▶</span>
            <span class="logo-text">Jim<span class="logo-accent">Movie</span></span>
        </a>
        <nav class="back-nav">
            <a href="index.php" class="back-link">← Back to List</a>
        </nav>
    </div>
</header>

<!-- TODO: Show full movie detail here -->
<!-- Hint: use $m['title'], $m['poster_url'], $m['description'], $m['rating'], $m['genre'], $m['year'] -->

<main class="detail-wrap">
    <div class="detail-card">

        <div class="detail-left">
            <?php if ($m['poster_url']): ?>
                <img src="<?= htmlspecialchars($m['poster_url']) ?>"
                     alt="<?= htmlspecialchars($m['title']) ?> poster"
                     class="detail-poster">
            <?php else: ?>
                <div class="detail-no-poster">🎬<br>No Image</div>
            <?php endif; ?>

            <div class="score-panel">
                <div class="score-big"><?= number_format($m['rating'], 1) ?></div>
                <div class="score-outof">out of 10</div>
                <div class="stars-lg"><?= stars((float)$m['rating']) ?></div>
                <div class="rbar-track" style="margin-top:.6rem">
                    <div class="rbar-fill" style="width:<?= ($m['rating'] / 10) * 100 ?>%"></div>
                </div>
            </div>
        </div>

        <div class="detail-right">

            <span class="cat-tag cat-<?= strtolower($m['category']) ?>">
                <?= $m['category'] === 'Anime' ? '⛩️' : '🎬' ?> <?= htmlspecialchars($m['category']) ?>
            </span>

            <h1 class="detail-title"><?= htmlspecialchars($m['title']) ?></h1>

            <!-- TODO: Display Year and Genre as meta chips here -->

            <div class="detail-divider"></div>

            <!-- TODO: Display the full description here -->

            <!-- TODO: Display the date added here -->

            <!-- TODO: Add Edit and Delete buttons here -->

        </div>
    </div>
</main>

<footer class="site-footer">
    <div class="footer-inner">
        <span class="footer-logo">▶ JimMovie</span>
        <span>Movie Ratings Catalog &copy; <?= date('Y') ?> &nbsp;|&nbsp; PHP + SQLite</span>
    </div>
</footer>

</body>
</html>
