<?php
// GET api/filters.php -> genres (with film counts) and release years
require_once __DIR__ . '/../includes/functions.php';

try {
    $genres = db()->query(
        "SELECT g.id, g.name, COUNT(mg.movie_id) AS total
         FROM genres g
         LEFT JOIN movie_genres mg ON mg.genre_id = g.id
         GROUP BY g.id ORDER BY g.name"
    )->fetchAll();

    $years = db()->query(
        "SELECT DISTINCT YEAR(release_date) AS y FROM movies ORDER BY y DESC"
    )->fetchAll(PDO::FETCH_COLUMN);

    json_response([
        'genres' => array_map(fn($g) => ['id' => (int)$g['id'], 'name' => $g['name'], 'total' => (int)$g['total']], $genres),
        'years'  => array_map('intval', $years),
    ]);
} catch (PDOException $e) {
    json_error('Database error.', 500);
}
