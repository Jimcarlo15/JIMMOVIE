<?php
require_once 'db.php';

$genreFilter     = trim($_GET['genre'] ?? '');
$directorFilter  = trim($_GET['director'] ?? '');
$categoryFilter  = trim($_GET['category'] ?? '');
$titleFilter     = trim($_GET['title_filter'] ?? '');
$searchKeyword   = trim($_GET['search'] ?? '');
$sortBy          = $_GET['sort_by'] ?? 'title';
$order           = strtoupper($_GET['order'] ?? 'ASC');
if (!in_array($order, ['ASC', 'DESC'])) {
    $order = 'ASC';
}

$where  = [];
$params = [];

if ($genreFilter !== '') {
    $where[] = "genre LIKE :genre";
    $params[':genre'] = '%' . $genreFilter . '%';
}
if ($directorFilter !== '') {
    $where[] = "director LIKE :director";
    $params[':director'] = '%' . $directorFilter . '%';
}
if ($categoryFilter !== '') {
    $where[] = "category = :cat";
    $params[':cat'] = $categoryFilter;
}
if ($titleFilter !== '') {
    $where[] = "title LIKE :title_filter";
    $params[':title_filter'] = '%' . $titleFilter . '%';
}
if ($searchKeyword !== '') {
    $where[] = "(title LIKE :kw OR genre LIKE :kw OR director LIKE :kw OR description LIKE :kw)";
    $params[':kw'] = '%' . $searchKeyword . '%';
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$validSort = [
    'title'  => 'title',
    'rating' => 'rating',
    'year'   => 'year'
];
$col = $validSort[$sortBy] ?? 'title';
$orderSQL = "ORDER BY $col $order";

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM movies $whereSQL");
$countStmt->execute($params);
$totalMovies = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalMovies / $perPage));

$stmt = $pdo->prepare("SELECT * FROM movies $whereSQL $orderSQL LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$movies = $stmt->fetchAll();

$allGenres = ['Action', 'Adventure', 'Biography', 'Comedy', 'Crime', 'Drama', 'Family', 'Fantasy', 'Mystery', 'Sci-Fi'];

$allDirectors = $pdo->query("SELECT DISTINCT director FROM movies WHERE director IS NOT NULL AND director != '' ORDER BY director ASC LIMIT 10")->fetchAll(PDO::FETCH_COLUMN);

function renderStars(float $rating): string {
    $full = (int)round($rating / 2);
    $full = max(0, min(5, $full));
    $empty = 5 - $full;
    return str_repeat('★', $full) . str_repeat('☆', $empty);
}

$msg_map = [
    'added'   => 'Movie added successfully!',
    'updated' => 'Movie updated successfully!',
    'deleted' => 'Movie deleted.',
];
$flash = $msg_map[$_GET['msg'] ?? ''] ?? '';

$edit_movie = null;
if (isset($_GET['edit'])) {
    $es = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
    $es->execute([(int)$_GET['edit']]);
    $edit_movie = $es->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Movie Catalog</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<header class="header-container">
    <div class="header-inner">
        <a href="index.php" class="header-logo">
            <span class="clapper-icon">🎬</span>
            <span class="logo-title">Movie Catalog</span>
        </a>

        <form method="GET" class="header-search-form">
            <input type="text" name="search" class="header-search-input"
                   placeholder="Enter your keywords"
                   value="<?= htmlspecialchars($searchKeyword) ?>">
            <button type="submit" class="yellow-gloss-btn btn-header-search">Search</button>
            <button type="button" class="yellow-gloss-btn btn-add-movie" onclick="openModal('addModal')">+ Add Movie</button>
        </form>
    </div>
</header>

<?php if ($flash): ?>
<div class="flash-banner"><?= $flash ?></div>
<?php endif; ?>

<div class="main-wrapper">
    <!-- LEFT SIDEBAR -->
    <aside class="sidebar-column">
        <!-- CATEGORY QUICK FILTER -->
        <div class="sidebar-block" style="padding-bottom:10px;">
            <h3 class="sidebar-heading">CATEGORY</h3>
            <ul class="genre-list" style="margin-bottom:0;">
                <li><a href="index.php" class="genre-item <?= $categoryFilter === '' ? 'active' : '' ?>">All Movies (<?= $totalMovies ?>)</a></li>
                <li><a href="?category=Hollywood" class="genre-item <?= $categoryFilter === 'Hollywood' ? 'active' : '' ?>">🎬 Hollywood</a></li>
                <li><a href="?category=Anime" class="genre-item <?= $categoryFilter === 'Anime' ? 'active' : '' ?>">⛩️ Anime</a></li>
            </ul>
        </div>

        <!-- GENRES BOX -->
        <div class="sidebar-block">
            <h3 class="sidebar-heading">GENRES</h3>
            
            <form method="GET" class="sidebar-filter-form">
                <input type="hidden" name="search" value="<?= htmlspecialchars($searchKeyword) ?>">
                <input type="hidden" name="title_filter" value="<?= htmlspecialchars($titleFilter) ?>">
                <input type="hidden" name="director" value="<?= htmlspecialchars($directorFilter) ?>">
                <input type="hidden" name="category" value="<?= htmlspecialchars($categoryFilter) ?>">
                <input type="hidden" name="sort_by" value="<?= htmlspecialchars($sortBy) ?>">
                <input type="hidden" name="order" value="<?= htmlspecialchars($order) ?>">
                
                <label class="filter-label">Filter</label>
                <input type="text" name="genre" class="sidebar-input" value="<?= htmlspecialchars($genreFilter) ?>">
                <button type="submit" class="yellow-gloss-btn sidebar-apply-btn">Apply</button>
            </form>

            <ul class="genre-list">
                <?php foreach ($allGenres as $g): ?>
                <li>
                    <a href="?genre=<?= urlencode($g) ?>&director=<?= urlencode($directorFilter) ?>&category=<?= urlencode($categoryFilter) ?>" 
                       class="genre-item <?= strtolower($genreFilter) === strtolower($g) ? 'active' : '' ?>">
                        <?= htmlspecialchars($g) ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>

            <div class="pagination-line">
                <span><?= $page ?> of <?= $totalPages ?></span>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&genre=<?= urlencode($genreFilter) ?>&director=<?= urlencode($directorFilter) ?>&category=<?= urlencode($categoryFilter) ?>&title_filter=<?= urlencode($titleFilter) ?>&sort_by=<?= $sortBy ?>&order=<?= $order ?>">&gt;</a>
                <?php else: ?>
                    <span class="disabled">&gt;</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- DIRECTORS BOX -->
        <div class="sidebar-block">
            <h3 class="sidebar-heading">DIRECTORS</h3>
            
            <form method="GET" class="sidebar-filter-form">
                <input type="hidden" name="search" value="<?= htmlspecialchars($searchKeyword) ?>">
                <input type="hidden" name="genre" value="<?= htmlspecialchars($genreFilter) ?>">
                <input type="hidden" name="category" value="<?= htmlspecialchars($categoryFilter) ?>">
                <input type="hidden" name="title_filter" value="<?= htmlspecialchars($titleFilter) ?>">
                <input type="hidden" name="sort_by" value="<?= htmlspecialchars($sortBy) ?>">
                <input type="hidden" name="order" value="<?= htmlspecialchars($order) ?>">

                <label class="filter-label">Filter</label>
                <input type="text" name="director" class="sidebar-input" value="<?= htmlspecialchars($directorFilter) ?>">
                <button type="submit" class="yellow-gloss-btn sidebar-apply-btn">Apply</button>
            </form>

            <ul class="director-list">
                <?php foreach ($allDirectors as $d): ?>
                <li>
                    <a href="?director=<?= urlencode($d) ?>&genre=<?= urlencode($genreFilter) ?>&category=<?= urlencode($categoryFilter) ?>"
                       class="director-item <?= strtolower($directorFilter) === strtolower($d) ? 'active' : '' ?>">
                        <?= htmlspecialchars($d) ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </aside>

    <!-- RIGHT CONTENT -->
    <main class="content-column">
        <h2 class="section-title">MOVIES</h2>

        <form method="GET" class="movie-controls-bar">
            <input type="hidden" name="search" value="<?= htmlspecialchars($searchKeyword) ?>">
            <input type="hidden" name="genre" value="<?= htmlspecialchars($genreFilter) ?>">
            <input type="hidden" name="director" value="<?= htmlspecialchars($directorFilter) ?>">
            <input type="hidden" name="category" value="<?= htmlspecialchars($categoryFilter) ?>">

            <div class="control-group">
                <label>Filter by Title</label>
                <input type="text" name="title_filter" class="control-input" value="<?= htmlspecialchars($titleFilter) ?>">
            </div>

            <div class="control-group">
                <label>Sort by</label>
                <select name="sort_by" class="control-select">
                    <option value="title" <?= $sortBy === 'title' ? 'selected' : '' ?>>Title</option>
                    <option value="rating" <?= $sortBy === 'rating' ? 'selected' : '' ?>>Rating</option>
                    <option value="year" <?= $sortBy === 'year' ? 'selected' : '' ?>>Year</option>
                </select>
            </div>

            <div class="control-group">
                <label>Order</label>
                <select name="order" class="control-select">
                    <option value="ASC" <?= $order === 'ASC' ? 'selected' : '' ?>>Asc</option>
                    <option value="DESC" <?= $order === 'DESC' ? 'selected' : '' ?>>Desc</option>
                </select>
            </div>

            <button type="submit" class="yellow-gloss-btn control-apply-btn">Apply</button>
        </form>

        <?php if ($genreFilter || $directorFilter || $titleFilter || $searchKeyword || $categoryFilter): ?>
        <div class="active-filters-bar">
            <span>Filtering:</span>
            <?php if ($categoryFilter): ?><span class="filter-tag"><?= htmlspecialchars($categoryFilter) ?></span><?php endif; ?>
            <?php if ($genreFilter): ?><span class="filter-tag">Genre: <?= htmlspecialchars($genreFilter) ?></span><?php endif; ?>
            <?php if ($directorFilter): ?><span class="filter-tag">Director: <?= htmlspecialchars($directorFilter) ?></span><?php endif; ?>
            <?php if ($titleFilter): ?><span class="filter-tag">Title: <?= htmlspecialchars($titleFilter) ?></span><?php endif; ?>
            <?php if ($searchKeyword): ?><span class="filter-tag">Keyword: <?= htmlspecialchars($searchKeyword) ?></span><?php endif; ?>
            <a href="index.php" class="reset-link">[Reset All]</a>
        </div>
        <?php endif; ?>

        <!-- 5-COLUMN POSTER GRID -->
        <?php if ($movies): ?>
        <div class="movie-grid">
            <?php foreach ($movies as $m): ?>
            <div class="movie-cell">
                <h4 class="movie-cell-title" title="<?= htmlspecialchars($m['title']) ?>">
                    <a href="movie.php?id=<?= $m['id'] ?>">
                        <?= htmlspecialchars($m['title']) ?>
                    </a>
                </h4>

                <a href="movie.php?id=<?= $m['id'] ?>" class="movie-cell-poster">
                    <?php if ($m['poster_url']): ?>
                    <img src="<?= htmlspecialchars($m['poster_url']) ?>" alt="<?= htmlspecialchars($m['title']) ?>">
                    <?php else: ?>
                    <div class="no-poster-box">No Poster</div>
                    <?php endif; ?>
                </a>

                <div class="movie-cell-rating">
                    <span class="stars-gold"><?= renderStars((float)$m['rating']) ?></span>
                    <span class="score-number"><?= number_format($m['rating'], 1) ?></span>
                </div>

                <div class="cell-admin-links">
                    <a href="movie.php?id=<?= $m['id'] ?>" class="btn-link">View Details</a>
                    <span>|</span>
                    <a href="index.php?edit=<?= $m['id'] ?>" class="btn-link">Edit</a>
                    <span>|</span>
                    <form method="POST" action="add_movie.php" style="display:inline" onsubmit="return confirm('Delete this movie?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $m['id'] ?>">
                        <button type="submit" class="btn-link-del">Del</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- BOTTOM PAGINATION -->
        <div class="bottom-pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&genre=<?= urlencode($genreFilter) ?>&director=<?= urlencode($directorFilter) ?>&category=<?= urlencode($categoryFilter) ?>&title_filter=<?= urlencode($titleFilter) ?>&sort_by=<?= $sortBy ?>&order=<?= $order ?>" class="page-link">&laquo; Prev</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>&genre=<?= urlencode($genreFilter) ?>&director=<?= urlencode($directorFilter) ?>&category=<?= urlencode($categoryFilter) ?>&title_filter=<?= urlencode($titleFilter) ?>&sort_by=<?= $sortBy ?>&order=<?= $order ?>" 
                   class="page-link <?= $page === $i ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&genre=<?= urlencode($genreFilter) ?>&director=<?= urlencode($directorFilter) ?>&category=<?= urlencode($categoryFilter) ?>&title_filter=<?= urlencode($titleFilter) ?>&sort_by=<?= $sortBy ?>&order=<?= $order ?>" class="page-link">Next &raquo;</a>
            <?php endif; ?>
        </div>

        <?php else: ?>
        <div class="empty-results">
            <p>No movies match your filter criteria.</p>
            <a href="index.php" class="yellow-gloss-btn" style="display:inline-block; margin-top:10px;">Show All Movies</a>
        </div>
        <?php endif; ?>
    </main>
</div>

<!-- ADD MODAL -->
<div id="addModal" class="catalog-modal-overlay" style="display:none">
    <div class="catalog-modal-box">
        <div class="catalog-modal-header">
            <h3>Add New Movie</h3>
            <button onclick="closeModal('addModal')" class="catalog-modal-close">✕</button>
        </div>
        <form method="POST" action="add_movie.php" class="catalog-modal-form">
            <input type="hidden" name="action" value="add">
            <?php include 'partials/movie_form.php'; ?>
            <div class="catalog-modal-footer">
                <button type="button" onclick="closeModal('addModal')" class="btn-catalog-cancel">Cancel</button>
                <button type="submit" class="yellow-gloss-btn">Save Movie</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<?php if ($edit_movie): ?>
<div id="editModal" class="catalog-modal-overlay">
    <div class="catalog-modal-box">
        <div class="catalog-modal-header">
            <h3>Edit Movie: <?= htmlspecialchars($edit_movie['title']) ?></h3>
            <a href="index.php" class="catalog-modal-close">✕</a>
        </div>
        <form method="POST" action="add_movie.php" class="catalog-modal-form">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= $edit_movie['id'] ?>">
            <?php include 'partials/movie_form.php'; ?>
            <div class="catalog-modal-footer">
                <a href="index.php" class="btn-catalog-cancel">Cancel</a>
                <button type="submit" class="yellow-gloss-btn">Update Movie</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<footer class="catalog-footer">
    <p>Movie Catalog &copy; <?= date('Y') ?> &bull; Movies with Ratings, Images &amp; Descriptions &bull; Non-Playable</p>
</footer>

<script src="assets/script.js"></script>
</body>
</html>
