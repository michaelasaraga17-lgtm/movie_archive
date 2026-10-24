<?php
// GET api/movies.php?q=&genre=&year=&featured=1&watchlist=1&id=
require_once __DIR__ . '/../includes/functions.php';

$cols = "m.id, m.title, m.release_date, YEAR(m.release_date) AS year, m.rating,
         m.director, m.cast_list, m.synopsis, m.poster_url, m.is_featured,
         GROUP_CONCAT(g.name ORDER BY g.name SEPARATOR '|') AS genres";
$from = "FROM movies m
         LEFT JOIN movie_genres mg ON mg.movie_id = m.id
         LEFT JOIN genres g ON g.id = mg.genre_id";

try {
    // Single movie (detail view)
    if ($id = get_int('id')) {
        $stmt = db()->prepare("SELECT $cols $from WHERE m.id = :id GROUP BY m.id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) json_error('Movie not found.', 404);
        json_response(format_movie($row));
    }

    $where  = [];
    $params = [];

    if (get_int('featured')) {
        $where[] = 'm.is_featured = 1';
    }
    if (get_int('watchlist')) {
        $where[]       = 'EXISTS (SELECT 1 FROM watchlist w WHERE w.movie_id = m.id AND w.visitor_id = :vid)';
        $params[':vid'] = visitor_id();
    }
    if (($q = get_str('q')) !== '') {
        $where[]      = 'm.title LIKE :q';
        $params[':q'] = '%' . addcslashes($q, '%_\\') . '%';
    }
    if ($genre = get_int('genre')) {
        $where[]          = 'EXISTS (SELECT 1 FROM movie_genres x WHERE x.movie_id = m.id AND x.genre_id = :genre)';
        $params[':genre'] = $genre;
    }
    if ($year = get_int('year')) {
        $where[]         = 'YEAR(m.release_date) = :year';
        $params[':year'] = $year;
    }

    $sql = "SELECT $cols $from"
         . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
         . ' GROUP BY m.id ORDER BY m.release_date DESC, m.title ASC';

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    json_response(array_map('format_movie', $stmt->fetchAll()));

} catch (PDOException $e) {
    json_error('Database error. Is MySQL running and is database.sql imported?', 500);
}
