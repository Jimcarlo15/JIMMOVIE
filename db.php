<?php
$db_path = __DIR__ . '/movies.db';
$pdo = new PDO('sqlite:' . $db_path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$pdo->exec("
    CREATE TABLE IF NOT EXISTS movies (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        title       TEXT    NOT NULL,
        category    TEXT    NOT NULL DEFAULT 'Hollywood',
        genre       TEXT    NOT NULL,
        year        INTEGER NOT NULL,
        rating      REAL    NOT NULL DEFAULT 0,
        poster_url  TEXT,
        description TEXT,
        created_at  TEXT    DEFAULT (datetime('now','localtime'))
    )
");

$count = $pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn();
if ($count == 0) {
    $seeds = [
        [
            'title'       => 'Inception',
            'category'    => 'Hollywood',
            'genre'       => 'Sci-Fi / Thriller',
            'year'        => 2010,
            'rating'      => 8.8,
            'poster_url'  => 'https://image.tmdb.org/t/p/w500/9gk7adHYeDvHkCSEqAvQNLV5Uge.jpg',
            'description' => 'Dom Cobb is a skilled thief who specializes in stealing secrets from deep within the subconscious mind during the dream state. When offered a chance to regain his old life as payment for a task considered to be impossible — inception — he assembles a team to pull off the ultimate mind heist.',
        ],
        [
            'title'       => 'Interstellar',
            'category'    => 'Hollywood',
            'genre'       => 'Sci-Fi / Drama',
            'year'        => 2014,
            'rating'      => 8.6,
            'poster_url'  => 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
            'description' => 'A team of explorers travel through a wormhole near Saturn in search of a new home for humanity. As Earth slowly dies, astronaut Cooper must choose between seeing his children again or saving the entire human race from extinction.',
        ],
        [
            'title'       => 'The Dark Knight',
            'category'    => 'Hollywood',
            'genre'       => 'Action / Crime',
            'year'        => 2008,
            'rating'      => 9.0,
            'poster_url'  => 'https://image.tmdb.org/t/p/w500/qJ2tW6WMUDux911r6m7haRef0WH.jpg',
            'description' => 'When the Joker emerges from the Gotham City underworld, he wreaks havoc and chaos on its people. Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.',
        ],
        [
            'title'       => 'Demon Slayer: Mugen Train',
            'category'    => 'Anime',
            'genre'       => 'Action / Fantasy',
            'year'        => 2020,
            'rating'      => 8.2,
            'poster_url'  => 'https://image.tmdb.org/t/p/w500/h8Rb9gBr48ODIwYUttZNYeMWeUU.jpg',
            'description' => 'Tanjiro and his companions board the Mugen Train to assist the Flame Hashira Rengoku on a mission against a demon who has been killing demon slayers.',
        ],
        [
            'title'       => 'Your Name',
            'category'    => 'Anime',
            'genre'       => 'Romance / Fantasy',
            'year'        => 2016,
            'rating'      => 8.4,
            'poster_url'  => 'https://image.tmdb.org/t/p/w500/q719jXXEzOoYaps6babgKnONONX.jpg',
            'description' => 'Two teenagers share a profound, magical connection upon discovering they are swapping bodies. Things become more complicated when the two try to meet in person.',
        ],
        [
            'title'       => 'Spirited Away',
            'category'    => 'Anime',
            'genre'       => 'Fantasy / Adventure',
            'year'        => 2001,
            'rating'      => 8.6,
            'poster_url'  => 'https://image.tmdb.org/t/p/w500/39wmItIWsg5sZMyRUHLkWBcuVCM.jpg',
            'description' => 'During her family\'s move to the suburbs, ten-year-old Chihiro stumbles into a spirit world ruled by gods and monsters and must work in a magical bathhouse to free her parents.',
        ],
    ];

    $stmt = $pdo->prepare("
        INSERT INTO movies (title, category, genre, year, rating, poster_url, description)
        VALUES (:title, :category, :genre, :year, :rating, :poster_url, :description)
    ");
    foreach ($seeds as $m) {
        $stmt->execute($m);
    }
}
?>
