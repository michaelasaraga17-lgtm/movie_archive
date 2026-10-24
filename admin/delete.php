<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
csrf_check();

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
if ($id && $id > 0) {
    try {
        $stmt = db()->prepare('SELECT title FROM movies WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $title = $stmt->fetchColumn();

        if ($title !== false) {
            // movie_genres and watchlist rows are removed automatically (ON DELETE CASCADE)
            db()->prepare('DELETE FROM movies WHERE id = :id')->execute([':id' => $id]);
            flash('Deleted “' . $title . '”.');
        }
    } catch (PDOException $e) {
        flash('Could not delete the film. Check that the database is running.');
    }
}
header('Location: index.php');
exit;
