<?php
require_once 'db.php';

$flash = '';
if (($_GET['msg'] ?? '') === 'added') {
    $flash = '✅ Movie added successfully!';
}

$edit_movie = null;

$stmt = $pdo->query("SELECT * FROM movies ORDER BY rating DESC");
$movies = $stmt->fetchAll();
$total  = count($movies);

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
<title>JimMovie — Movie Ratings</title>
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
        <div class="header-right">
            <span class="total-badge"><?= $total ?> titles</span>
            <button class="btn-primary" onclick="openModal('addModal')">+ Add Movie</button>
        </div>
    </div>
</header>

<?php if ($flash): ?>
<div class="flash-bar"><?= $flash ?></div>
<?php endif; ?>

<section class="hero">
    <div class="hero-glow"></div>
    <div class="hero-content">
        <p class="hero-label">MOVIE CATALOG</p>
        <h1 class="hero-title">Discover & Rate<br>Great Cinema</h1>
        <p class="hero-sub">Hollywood blockbusters and top Anime — browse and rate. Not playable.</p>
    </div>
</section>

<main class="main-wrap">

    <!-- TODO: Add search bar here -->

    <!-- TODO: Add category filter tabs here (All / Hollywood / Anime) -->

    <!-- TODO: Add sort dropdown here (Top Rated, Newest, A-Z) -->

    <p class="result-info"><?= $total ?> movie<?= $total !== 1 ? 's' : '' ?> in the list</p>

    <?php if ($movies): ?>
    <ul class="movie-list">
        <?php foreach ($movies as $m): ?>
        <li class="movie-row">

            <a href="movie.php?id=<?= $m['id'] ?>" class="row-poster-link">
                <?php if ($m['poster_url']): ?>
                    <img src="<?= htmlspecialchars($m['poster_url']) ?>"
                         alt="<?= htmlspecialchars($m['title']) ?>"
                         class="row-poster" loading="lazy">
                <?php else: ?>
                    <div class="row-no-poster">🎬</div>
                <?php endif; ?>
                <div class="poster-overlay">
                    <span class="overlay-score"><?= number_format($m['rating'], 1) ?></span>
                </div>
            </a>

            <div class="row-body">
                <div class="row-head">
                    <span class="cat-tag cat-<?= strtolower($m['category']) ?>">
                        <?= $m['category'] === 'Anime' ? '⛩️' : '🎬' ?> <?= htmlspecialchars($m['category']) ?>
                    </span>
                    <span class="row-year"><?= $m['year'] ?></span>
                </div>

                <a href="movie.php?id=<?= $m['id'] ?>" class="row-title">
                    <?= htmlspecialchars($m['title']) ?>
                </a>

                <p class="row-genre"><?= htmlspecialchars($m['genre']) ?></p>

                <div class="row-rating">
                    <div class="stars-row"><?= stars((float)$m['rating']) ?></div>
                    <span class="score-chip"><?= number_format($m['rating'], 1) ?> <em>/ 10</em></span>
                </div>
                <div class="rbar-track">
                    <div class="rbar-fill" style="width:<?= ($m['rating'] / 10) * 100 ?>%"></div>
                </div>

                <p class="row-desc"><?= htmlspecialchars(mb_substr($m['description'], 0, 130)) ?>…</p>

                <div class="row-actions">
                    <a href="movie.php?id=<?= $m['id'] ?>" class="act-btn act-view">View Details</a>

                    <!-- TODO: Add Edit button here -->

                    <!-- TODO: Add Delete button/form here -->
                </div>
            </div>

        </li>
        <?php endforeach; ?>
    </ul>

    <?php else: ?>
    <div class="empty-wrap">
        <div class="empty-icon">🎞</div>
        <p class="empty-title">No movies found</p>
        <p class="empty-sub">Add your first movie using the button above.</p>
    </div>
    <?php endif; ?>

</main>

<div id="addModal" class="modal-veil" style="display:none">
    <div class="modal-panel">
        <div class="modal-top">
            <h3 class="modal-heading">Add New Movie</h3>
            <button onclick="closeModal('addModal')" class="modal-x">✕</button>
        </div>
        <form method="POST" action="add_movie.php">
            <input type="hidden" name="action" value="add">
            <?php include 'partials/movie_form.php'; ?>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('addModal')" class="btn-ghost">Cancel</button>
                <button type="submit" class="btn-primary">Save Movie</button>
            </div>
        </form>
    </div>
</div>

<!-- TODO: Add Edit modal here -->

<footer class="site-footer">
    <div class="footer-inner">
        <span class="footer-logo">▶ JimMovie</span>
        <span>Movie Ratings Catalog &copy; <?= date('Y') ?> &nbsp;|&nbsp; PHP + SQLite</span>
    </div>
</footer>

<script>
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = '';
}
document.querySelectorAll('.modal-veil').forEach(el => {
    el.addEventListener('click', e => {
        if (e.target === el) {
            el.style.display = 'none';
            document.body.style.overflow = '';
        }
    });
});
</script>
</body>
</html>
