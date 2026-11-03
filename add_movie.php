<?php
require_once 'db.php';

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $stmt = $pdo->prepare("
        INSERT INTO movies (title, director, genre, year, rating, poster_url, description)
        VALUES (:title, :director, :genre, :year, :rating, :poster_url, :description)
    ");
    $stmt->execute([
        ':title'       => trim($_POST['title']),
        ':director'    => trim($_POST['director'] ?? 'Unknown'),
        ':genre'       => trim($_POST['genre']),
        ':year'        => (int)$_POST['year'],
        ':rating'      => (float)$_POST['rating'],
        ':poster_url'  => trim($_POST['poster_url']),
        ':description' => trim($_POST['description']),
    ]);
    header('Location: index.php?msg=added');
    exit;
}

if ($action === 'edit') {
    $stmt = $pdo->prepare("
        UPDATE movies
        SET title = :title,
            director = :director,
            genre = :genre,
            year = :year,
            rating = :rating,
            poster_url = :poster_url,
            description = :description
        WHERE id = :id
    ");
    $stmt->execute([
        ':id'          => (int)$_POST['id'],
        ':title'       => trim($_POST['title']),
        ':director'    => trim($_POST['director'] ?? 'Unknown'),
        ':genre'       => trim($_POST['genre']),
        ':year'        => (int)$_POST['year'],
        ':rating'      => (float)$_POST['rating'],
        ':poster_url'  => trim($_POST['poster_url']),
        ':description' => trim($_POST['description']),
    ]);
    header('Location: index.php?msg=updated');
    exit;
}

if ($action === 'delete') {
    $stmt = $pdo->prepare("DELETE FROM movies WHERE id = :id");
    $stmt->execute([':id' => (int)$_POST['id']]);
    header('Location: index.php?msg=deleted');
    exit;
}

header('Location: index.php');
exit;
?>
