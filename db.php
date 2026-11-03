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
        director    TEXT    DEFAULT 'Unknown',
        genre       TEXT    NOT NULL,
        year        INTEGER NOT NULL,
        rating      REAL    NOT NULL DEFAULT 0,
        poster_url  TEXT,
        description TEXT,
        created_at  TEXT    DEFAULT (datetime('now','localtime'))
    )
");

$cols = $pdo->query("PRAGMA table_info(movies)")->fetchAll();
$colNames = array_column($cols, 'name');
if (!in_array('category', $colNames)) {
    $pdo->exec("ALTER TABLE movies ADD COLUMN category TEXT DEFAULT 'Hollywood'");
}
if (!in_array('director', $colNames)) {
    $pdo->exec("ALTER TABLE movies ADD COLUMN director TEXT DEFAULT 'Unknown'");
}

$count = $pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn();
if ($count == 0) {
    $seeds = [
        // 10 from Reference Screenshot
        [
            '"Police Squad!"',
            'Hollywood',
            'David Zucker',
            'Comedy',
            1982,
            8.5,
            'assets/posters/police_squad.jpg',
            'In Color! Detective Frank Drebin of Police Squad solves crimes in deadpan spoof comedic fashion with absurd slapstick humor and bizarre wordplay.'
        ],
        [
            'Harry Potter and the Deathly Hallows',
            'Hollywood',
            'David Yates',
            'Fantasy',
            2010,
            7.7,
            'assets/posters/harry_potter.jpg',
            'Harry, Ron, and Hermione race against time and evil to destroy the Horcruxes. As Voldemort gains control, they uncover the ancient secrets of the Deathly Hallows.'
        ],
        [
            'Inception (2010)',
            'Hollywood',
            'Christopher Nolan',
            'Sci-Fi',
            2010,
            8.8,
            'assets/posters/inception.jpg',
            'A skilled thief who steals corporate secrets through dream-sharing technology is given the inverse task of planting an idea into the mind of a CEO.'
        ],
        [
            'Pirates of the Caribbean',
            'Hollywood',
            'Gore Verbinski',
            'Adventure',
            2006,
            7.3,
            'assets/posters/pirates.jpg',
            'Jack Sparrow races to recover the heart of Davy Jones to avoid enslaving his soul to Jones\' service, as other friends and foes seek the heart for their own agenda.'
        ],
        [
            'Pulp Fiction (1994)',
            'Hollywood',
            'Quentin Tarantino',
            'Crime',
            1994,
            8.9,
            'assets/posters/pulp_fiction.jpg',
            'The lives of two mob hitmen, a boxer, a gangster and his wife, and a pair of diner bandits intertwine in four tales of violence and redemption.'
        ],
        [
            'Scary Movie 5 (2013)',
            'Hollywood',
            'Malcolm D. Lee',
            'Comedy',
            2013,
            3.5,
            'assets/posters/scary_movie_5.jpg',
            'A married couple begins to examine bizarre activity in their home after they bring their newborn son home from the hospital in this parody sequel.'
        ],
        [
            'The Dark Knight',
            'Hollywood',
            'Christopher Nolan',
            'Action',
            2008,
            9.0,
            'assets/posters/dark_knight.jpg',
            'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests.'
        ],
        [
            'The Matrix Reloaded',
            'Hollywood',
            'Andy Wachowski',
            'Sci-Fi',
            2003,
            7.2,
            'assets/posters/matrix_reloaded.jpg',
            'Neo and the rebel leaders estimate that they have 72 hours until 250,000 probes discover Zion and destroy it. Neo must decide how he can save Trinity from a dark fate.'
        ],
        [
            'The Naked Gun 2½: The Smell of Fear',
            'Hollywood',
            'David Zucker',
            'Comedy',
            1991,
            6.9,
            'assets/posters/naked_gun_2.jpg',
            'Lieutenant Frank Drebin discovers that his ex-girlfriend\'s new boyfriend is involved in a plot to kidnap a scientist who advocates solar energy.'
        ],
        [
            'The Pink Panther',
            'Hollywood',
            'Shawn Levy',
            'Comedy',
            2006,
            5.7,
            'assets/posters/pink_panther.jpg',
            'Bumbling French police Inspector Jacques Clouseau is assigned to solve the murder of a famous soccer coach and the theft of the famous Pink Panther diamond.'
        ],

        // Mystery Movies
        [
            'Knives Out',
            'Hollywood',
            'Rian Johnson',
            'Mystery',
            2019,
            7.9,
            'assets/posters/knives_out.jpg',
            'A master detective investigates the death of the patriarch of an eccentric, combative family after he turns up dead under mysterious circumstances.'
        ],
        [
            'Shutter Island',
            'Hollywood',
            'Martin Scorsese',
            'Mystery',
            2010,
            8.2,
            'assets/posters/shutter_island.jpg',
            'U.S. Marshal Teddy Daniels investigates the disappearance of a murderess who escaped from a hospital for the criminally insane on a remote windswept island.'
        ],

        // Family Movies
        [
            'Paddington 2',
            'Hollywood',
            'Paul King',
            'Family',
            2017,
            7.8,
            'assets/posters/paddington_2.jpg',
            'Paddington takes on a series of odd jobs to buy the perfect present for his Aunt Lucy\'s 100th birthday, only for the gift to be stolen by a cunning thief.'
        ],
        [
            'Coco',
            'Hollywood',
            'Lee Unkrich',
            'Family',
            2017,
            8.4,
            'assets/posters/coco.jpg',
            'Aspiring musician Miguel, confronted with his family\'s ancestral ban on music, enters the Land of the Dead to find his great-great-grandfather, a legendary singer.'
        ],

        // 3 Anime Movies
        [
            'Demon Slayer: Mugen Train',
            'Anime',
            'Haruo Sotozaki',
            'Action',
            2020,
            8.2,
            'assets/posters/demon_slayer.jpg',
            'Tanjiro and his comrades join Flame Hashira Kyojuro Rengoku aboard the Mugen Train to battle a powerful demonic threat consuming innocent passengers.'
        ],
        [
            'Your Name',
            'Anime',
            'Makoto Shinkai',
            'Drama',
            2016,
            8.4,
            'assets/posters/your_name.jpg',
            'Two teenagers mysteriously swap bodies and form a profound emotional connection, discovering a cosmic secret that transcends space and time.'
        ],
        [
            'Suzume',
            'Anime',
            'Makoto Shinkai',
            'Fantasy',
            2022,
            7.9,
            'assets/posters/suzume.jpg',
            'A 17-year-old girl named Suzume embarks on a journey across Japan to close mysterious supernatural doors that release devastating disasters upon the earth.'
        ],

        // 3 Hollywood Additions
        [
            'Oppenheimer',
            'Hollywood',
            'Christopher Nolan',
            'Biography',
            2023,
            8.9,
            'assets/posters/oppenheimer.jpg',
            'The story of J. Robert Oppenheimer and his role in the development of the atomic bomb during World War II with the Manhattan Project.'
        ],
        [
            'Interstellar',
            'Hollywood',
            'Christopher Nolan',
            'Sci-Fi',
            2014,
            8.7,
            'assets/posters/interstellar.jpg',
            'A team of courageous explorers travel through a wormhole in space in an attempt to ensure humanity\'s survival as Earth faces catastrophic famine.'
        ],
        [
            'Dune: Part Two',
            'Hollywood',
            'Denis Villeneuve',
            'Sci-Fi',
            2024,
            8.6,
            'assets/posters/dune_two.jpg',
            'Paul Atreides unites with Chani and the Fremen while seeking revenge against the conspirators who destroyed his family, facing a choice between love and the universe.'
        ]
    ];

    $stmt = $pdo->prepare("
        INSERT INTO movies (title, category, director, genre, year, rating, poster_url, description)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    foreach ($seeds as $s) {
        $stmt->execute($s);
    }
}
?>
