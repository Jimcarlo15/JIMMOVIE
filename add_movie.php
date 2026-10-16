<?php
require_once 'db.php';

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $stmt = $pdo->prepare("
        INSERT INTO movies (title, category, genre, year, rating, poster_url, description)
        VALUES (:title, :category, :genre, :year, :rating, :poster_url, :description)
    ");
    $stmt->execute([
        ':title'       => trim($_POST['title']),
        ':category'    => $_POST['category'],
        ':genre'       => trim($_POST['genre']),
        ':year'        => (int)$_POST['year'],
        ':rating'      => (float)$_POST['rating'],
        ':poster_url'  => trim($_POST['poster_url']),
        ':description' => trim($_POST['description']),
    ]);
    header('Location: index.php?msg=added');
    exit;
}

// TODO: Add the 'edit' action here (UPDATE query)

// TODO: Add the 'delete' action here (DELETE query)

header('Location: index.php');
exit;
?>
