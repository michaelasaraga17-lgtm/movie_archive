<?php
// includes/functions.php - small helpers shared by the API files.
require_once __DIR__ . '/db.php';

function json_response($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function json_error(string $message, int $status = 400): void {
    json_response(['error' => $message], $status);
}

// Trimmed string from $_GET, or '' if missing.
function get_str(string $key, int $max = 100): string {
    $v = trim((string)($_GET[$key] ?? ''));
    return mb_substr($v, 0, $max);
}

// Positive int from $_GET, or null if missing/invalid.
function get_int(string $key): ?int {
    $v = filter_var($_GET[$key] ?? null, FILTER_VALIDATE_INT);
    return ($v !== false && $v > 0) ? $v : null;
}

// Turns a DB row into the shape the frontend expects.
function format_movie(array $row): array {
    $row['id']          = (int)$row['id'];
    $row['rating']      = (float)$row['rating'];
    $row['year']        = (int)$row['year'];
    $row['is_featured'] = (int)($row['is_featured'] ?? 0);
    $row['genres']      = $row['genres'] ? explode('|', $row['genres']) : [];
    if (isset($row['cast_list'])) {
        $row['cast'] = array_values(array_filter(array_map('trim', explode(',', $row['cast_list']))));
        unset($row['cast_list']);
    }
    return $row;
}

// Anonymous visitor token kept in a cookie for a year. The watchlist is stored against it.
function visitor_id(): string {
    $id = $_COOKIE['visitor'] ?? '';
    if (!preg_match('/^[a-f0-9]{32}$/', $id)) {
        $id = bin2hex(random_bytes(16));
        setcookie('visitor', $id, [
            'expires'  => time() + 31536000,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $_COOKIE['visitor'] = $id;
    }
    return $id;
}
