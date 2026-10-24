<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$movies = [];
$dbError = false;
try {
    $movies = db()->query(
        "SELECT m.id, m.title, m.director, YEAR(m.release_date) AS year, m.rating, m.poster_url, m.is_featured,
                GROUP_CONCAT(g.name ORDER BY g.name SEPARATOR ', ') AS genres
         FROM movies m
         LEFT JOIN movie_genres mg ON mg.movie_id = m.id
         LEFT JOIN genres g ON g.id = mg.genre_id
         GROUP BY m.id ORDER BY m.title"
    )->fetchAll();
} catch (PDOException $e) {
    $dbError = true;
}

$flash = flash();
$pageTitle = 'Films';
require __DIR__ . '/../includes/admin_header.php';
?>
<div class="flex flex-wrap items-end justify-between gap-4 mb-10">
  <h1 class="font-display text-5xl tracking-tight leading-none">Films</h1>
  <a href="edit.php" class="rounded-full bg-accent hover:bg-bone text-ink font-medium px-6 py-3 min-h-[44px] text-sm transition-colors">Add a film</a>
</div>

<?php if ($flash): ?>
  <p class="mb-8 rounded-lg border border-accent/40 px-4 py-3 text-sm text-accent" role="status"><?= e($flash) ?></p>
<?php endif; ?>
<?php if ($dbError): ?>
  <p class="mb-8 rounded-lg border border-danger/40 px-4 py-3 text-sm text-danger" role="alert">Could not read the films. Is MySQL running?</p>
<?php endif; ?>

<?php if (!$movies && !$dbError): ?>
  <div class="py-16">
    <p class="font-display text-3xl mb-3">No films yet.</p>
    <p class="text-soft">Add the first one to see it on the site.</p>
  </div>
<?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
      <thead class="text-mute">
        <tr class="border-b border-line">
          <th class="py-3 pr-4 font-normal w-16"><span class="sr-only">Poster</span></th>
          <th class="py-3 pr-4 font-normal">Title</th>
          <th class="py-3 pr-4 font-normal">Year</th>
          <th class="py-3 pr-4 font-normal">Rating</th>
          <th class="py-3 pr-4 font-normal">Genres</th>
          <th class="py-3 font-normal text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($movies as $m): ?>
        <tr class="border-b border-line align-middle">
          <td class="py-3 pr-4">
            <?php if ($m['poster_url']): ?>
              <img src="<?= e($m['poster_url']) ?>" alt="" loading="lazy" class="h-14 w-10 object-cover rounded-[2px] bg-surface" onerror="this.style.visibility='hidden'">
            <?php else: ?>
              <div class="h-14 w-10 rounded-[2px] bg-surface"></div>
            <?php endif; ?>
          </td>
          <td class="py-3 pr-4">
            <span class="font-display text-lg"><?= e($m['title']) ?></span>
            <?php if ($m['is_featured']): ?>
              <span class="ml-2 rounded-full border border-accent/40 px-2 py-0.5 text-xs text-accent">Featured</span>
            <?php endif; ?>
            <div class="text-mute"><?= e($m['director']) ?></div>
          </td>
          <td class="py-3 pr-4 tabular-nums"><?= (int)$m['year'] ?></td>
          <td class="py-3 pr-4 tabular-nums text-accent"><?= number_format((float)$m['rating'], 1) ?></td>
          <td class="py-3 pr-4 text-soft"><?= e($m['genres'] ?? '') ?></td>
          <td class="py-3 text-right whitespace-nowrap">
            <a href="edit.php?id=<?= (int)$m['id'] ?>" class="text-soft hover:text-bone underline underline-offset-4 mr-5">Edit</a>
            <form method="post" action="delete.php" class="inline"
                  onsubmit="return confirm('Delete <?= e(addslashes($m['title'])) ?>? This cannot be undone.')">
              <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
              <button class="text-danger hover:text-bone underline underline-offset-4">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
