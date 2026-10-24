<?php
// GET  api/watchlist.php                          -> {"ids":[3,1]}
// POST api/watchlist.php  action=add|remove&movie_id=3  -> {"ids":[...]}
require_once __DIR__ . '/../includes/functions.php';

$visitor = visitor_id();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action  = $_POST['action'] ?? '';
        $movieId = filter_var($_POST['movie_id'] ?? null, FILTER_VALIDATE_INT);

        if (!in_array($action, ['add', 'remove'], true) || !$movieId || $movieId < 1) {
            json_error('Invalid request.');
        }

        if ($action === 'add') {
            $check = db()->prepare('SELECT 1 FROM movies WHERE id = :id');
            $check->execute([':id' => $movieId]);
            if (!$check->fetchColumn()) json_error('Movie not found.', 404);

            db()->prepare('INSERT IGNORE INTO watchlist (visitor_id, movie_id) VALUES (:v, :m)')
                ->execute([':v' => $visitor, ':m' => $movieId]);
        } else {
            db()->prepare('DELETE FROM watchlist WHERE visitor_id = :v AND movie_id = :m')
                ->execute([':v' => $visitor, ':m' => $movieId]);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        json_error('Method not allowed.', 405);
    }

    $stmt = db()->prepare('SELECT movie_id FROM watchlist WHERE visitor_id = :v ORDER BY added_at DESC, id DESC');
    $stmt->execute([':v' => $visitor]);
    json_response(['ids' => array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN))]);

} catch (PDOException $e) {
    json_error('Database error.', 500);
}
