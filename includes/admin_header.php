<?php
// includes/admin_header.php - top of every admin page. Set $pageTitle before including.
require_once __DIR__ . '/auth.php';
$pageTitle = $pageTitle ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> | Movie Archive admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..600&family=Hanken+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: {
      colors: { ink: '#111315', surface: '#181b1e', line: '#2a2e33', bone: '#ece9e2', soft: '#b9bcc1',
                mute: '#8e9197', accent: '#d7bf94', danger: '#e58f8f' },
      fontFamily: { display: ['"Bodoni Moda"', 'Georgia', 'serif'], sans: ['"Hanken Grotesk"', 'system-ui', 'sans-serif'] }
    } } };
  </script>
  <style>
    html { background: #111315; color-scheme: dark; }
    ::selection { background: #d7bf94; color: #111315; }
    :focus-visible { outline: 2px solid #d7bf94; outline-offset: 3px; }
  </style>
</head>
<body class="bg-ink text-bone font-sans antialiased">
  <header class="max-w-5xl mx-auto px-6 pt-8 pb-10 flex flex-wrap items-center justify-between gap-4">
    <a href="<?= is_admin() ? 'index.php' : '../index.html' ?>" class="font-display text-2xl tracking-tight">Movie Archive <span class="text-sm font-sans text-mute ml-2">Admin</span></a>
    <?php if (is_admin()): ?>
      <nav class="flex items-center gap-6 text-sm">
        <a href="index.php" class="text-soft hover:text-bone">Films</a>
        <a href="edit.php" class="text-soft hover:text-bone">Add film</a>
        <a href="../index.html" class="text-soft hover:text-bone">View site</a>
        <form method="post" action="logout.php">
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
          <button class="rounded-full border border-line hover:border-accent px-4 py-2 transition-colors">Log out</button>
        </form>
      </nav>
    <?php endif; ?>
  </header>
  <main class="max-w-5xl mx-auto px-6 pb-24">
