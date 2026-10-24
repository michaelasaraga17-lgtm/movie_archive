<?php
// Add a film (edit.php) or edit one (edit.php?id=3)
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$id = ($id && $id > 0) ? $id : null;

$allGenres = db()->query('SELECT id, name FROM genres ORDER BY name')->fetchAll();
$validGenreIds = array_map('intval', array_column($allGenres, 'id'));

$film = [
    'title' => '', 'release_date' => '', 'rating' => '', 'director' => '',
    'cast_list' => '', 'synopsis' => '', 'poster_url' => '', 'is_featured' => 0,
];
$selected = [];
$errors = [];

// Load the film being edited
if ($id) {
    $stmt = db()->prepare('SELECT title, release_date, rating, director, cast_list, synopsis, poster_url, is_featured FROM movies WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if (!$row) {
        flash('That film no longer exists.');
        header('Location: index.php');
        exit;
    }
    $film = $row;
    $film['rating'] = number_format((float)$row['rating'], 1, '.', '');
    $g = db()->prepare('SELECT genre_id FROM movie_genres WHERE movie_id = :id');
    $g->execute([':id' => $id]);
    $selected = array_map('intval', $g->fetchAll(PDO::FETCH_COLUMN));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $film = [
        'title'        => trim($_POST['title'] ?? ''),
        'release_date' => trim($_POST['release_date'] ?? ''),
        'rating'       => trim($_POST['rating'] ?? ''),
        'director'     => trim($_POST['director'] ?? ''),
        'cast_list'    => trim($_POST['cast_list'] ?? ''),
        'synopsis'     => trim($_POST['synopsis'] ?? ''),
        'poster_url'   => trim($_POST['poster_url'] ?? ''),
        'is_featured'  => isset($_POST['is_featured']) ? 1 : 0,
    ];
    $selected = array_values(array_intersect(array_map('intval', (array)($_POST['genres'] ?? [])), $validGenreIds));

    // Validation
    if ($film['title'] === '') $errors['title'] = 'Enter a title.';
    elseif (mb_strlen($film['title']) > 150) $errors['title'] = 'Keep the title under 150 characters.';

    $date = DateTime::createFromFormat('!Y-m-d', $film['release_date']);
    if (!$date || $date->format('Y-m-d') !== $film['release_date']) {
        $errors['release_date'] = 'Pick a valid release date.';
    } elseif ((int)$date->format('Y') < 1888 || (int)$date->format('Y') > (int)date('Y') + 5) {
        $errors['release_date'] = 'That year looks wrong. Use 1888 or later.';
    }

    if ($film['rating'] === '' || !is_numeric($film['rating']) || $film['rating'] < 0 || $film['rating'] > 10) {
        $errors['rating'] = 'Enter a rating from 0 to 10.';
    }

    if ($film['director'] === '') $errors['director'] = 'Enter the director.';
    elseif (mb_strlen($film['director']) > 120) $errors['director'] = 'Keep the director under 120 characters.';

    // Tidy the cast list: "a,b ,  c" becomes "a, b, c"
    $castNames = array_filter(array_map('trim', explode(',', $film['cast_list'])), fn($n) => $n !== '');
    $film['cast_list'] = implode(', ', $castNames);
    if (mb_strlen($film['cast_list']) > 500) $errors['cast_list'] = 'The cast list is too long (500 characters max).';

    if ($film['synopsis'] === '') $errors['synopsis'] = 'Write a short synopsis.';
    elseif (mb_strlen($film['synopsis']) > 3000) $errors['synopsis'] = 'Keep the synopsis under 3000 characters.';

    if ($film['poster_url'] !== '') {
        if (mb_strlen($film['poster_url']) > 500 || !filter_var($film['poster_url'], FILTER_VALIDATE_URL)
            || !preg_match('#^https?://#i', $film['poster_url'])) {
            $errors['poster_url'] = 'Use a full image link that starts with http:// or https://.';
        }
    }

    if (!$selected) $errors['genres'] = 'Pick at least one genre.';

    // Save
    if (!$errors) {
        $pdo = db();
        try {
            $pdo->beginTransaction();
            $values = [
                ':title'    => $film['title'],
                ':rd'       => $film['release_date'],
                ':rating'   => round((float)$film['rating'], 1),
                ':director' => $film['director'],
                ':cast'     => $film['cast_list'],
                ':syn'      => $film['synopsis'],
                ':poster'   => $film['poster_url'],
                ':feat'     => $film['is_featured'],
            ];
            if ($id) {
                $pdo->prepare('UPDATE movies SET title=:title, release_date=:rd, rating=:rating, director=:director,
                               cast_list=:cast, synopsis=:syn, poster_url=:poster, is_featured=:feat WHERE id=:id')
                    ->execute($values + [':id' => $id]);
            } else {
                $pdo->prepare('INSERT INTO movies (title, release_date, rating, director, cast_list, synopsis, poster_url, is_featured)
                               VALUES (:title, :rd, :rating, :director, :cast, :syn, :poster, :feat)')
                    ->execute($values);
                $id = (int)$pdo->lastInsertId();
            }

            // Only one featured film at a time
            if ($film['is_featured']) {
                $pdo->prepare('UPDATE movies SET is_featured = 0 WHERE id <> :id')->execute([':id' => $id]);
            }

            // Replace this film's genres
            $pdo->prepare('DELETE FROM movie_genres WHERE movie_id = :id')->execute([':id' => $id]);
            $ins = $pdo->prepare('INSERT INTO movie_genres (movie_id, genre_id) VALUES (:m, :g)');
            foreach ($selected as $gid) {
                $ins->execute([':m' => $id, ':g' => $gid]);
            }

            $pdo->commit();
            flash('Saved “' . $film['title'] . '”.');
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $errors['form'] = 'Could not save the film. Check that the database is running.';
        }
    }
}

$pageTitle = $id ? 'Edit film' : 'Add film';
$input = 'w-full rounded-lg bg-surface border border-line focus:border-accent px-4 py-3 text-sm placeholder:text-mute';
$err = fn(string $k) => isset($errors[$k]) ? '<p class="mt-2 text-sm text-danger" role="alert">' . e($errors[$k]) . '</p>' : '';
require __DIR__ . '/../includes/admin_header.php';
?>
<h1 class="font-display text-5xl tracking-tight leading-none mb-10"><?= $id ? 'Edit film' : 'Add a film' ?></h1>

<?php if (isset($errors['form'])): ?>
  <p class="mb-8 rounded-lg border border-danger/40 px-4 py-3 text-sm text-danger" role="alert"><?= e($errors['form']) ?></p>
<?php elseif ($errors): ?>
  <p class="mb-8 rounded-lg border border-danger/40 px-4 py-3 text-sm text-danger" role="alert">Fix the highlighted fields and save again.</p>
<?php endif; ?>

<form method="post" action="edit.php<?= $id ? '?id=' . $id : '' ?>" class="grid lg:grid-cols-[1fr_16rem] gap-12">
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

  <div class="space-y-7">
    <div>
      <label for="title" class="block text-sm text-mute mb-2">Title</label>
      <input id="title" name="title" value="<?= e($film['title']) ?>" required maxlength="150" class="<?= $input ?>">
      <?= $err('title') ?>
    </div>

    <div class="grid sm:grid-cols-2 gap-7">
      <div>
        <label for="release_date" class="block text-sm text-mute mb-2">Release date</label>
        <input id="release_date" name="release_date" type="date" value="<?= e($film['release_date']) ?>" required min="1888-01-01" class="<?= $input ?>">
        <?= $err('release_date') ?>
      </div>
      <div>
        <label for="rating" class="block text-sm text-mute mb-2">Rating (0 to 10)</label>
        <input id="rating" name="rating" type="number" step="0.1" min="0" max="10" value="<?= e((string)$film['rating']) ?>" required class="<?= $input ?>">
        <?= $err('rating') ?>
      </div>
    </div>

    <div>
      <label for="director" class="block text-sm text-mute mb-2">Director</label>
      <input id="director" name="director" value="<?= e($film['director']) ?>" required maxlength="120" class="<?= $input ?>">
      <?= $err('director') ?>
    </div>

    <div>
      <label for="cast_list" class="block text-sm text-mute mb-2">Cast (separate names with commas)</label>
      <input id="cast_list" name="cast_list" value="<?= e($film['cast_list']) ?>" maxlength="500" placeholder="Amy Adams, Jeremy Renner" class="<?= $input ?>">
      <?= $err('cast_list') ?>
    </div>

    <div>
      <label for="synopsis" class="block text-sm text-mute mb-2">Synopsis</label>
      <textarea id="synopsis" name="synopsis" rows="6" required maxlength="3000" class="<?= $input ?> leading-relaxed"><?= e($film['synopsis']) ?></textarea>
      <?= $err('synopsis') ?>
    </div>

    <fieldset>
      <legend class="block text-sm text-mute mb-3">Genres</legend>
      <div class="flex flex-wrap gap-2">
        <?php foreach ($allGenres as $g): ?>
          <label class="cursor-pointer">
            <input type="checkbox" name="genres[]" value="<?= (int)$g['id'] ?>" class="peer sr-only" <?= in_array((int)$g['id'], $selected, true) ? 'checked' : '' ?>>
            <span class="block rounded-full border border-line px-4 py-2 text-sm text-soft transition-colors peer-checked:bg-accent peer-checked:border-accent peer-checked:text-ink peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-accent"><?= e($g['name']) ?></span>
          </label>
        <?php endforeach; ?>
      </div>
      <?= $err('genres') ?>
    </fieldset>

    <label class="flex items-center gap-3 text-sm cursor-pointer">
      <input type="checkbox" name="is_featured" value="1" class="h-4 w-4 accent-[#d7bf94]" <?= $film['is_featured'] ? 'checked' : '' ?>>
      Show as the featured film on the home page
    </label>

    <div class="flex items-center gap-4 pt-2">
      <button class="rounded-full bg-accent hover:bg-bone text-ink font-medium px-7 py-3 min-h-[44px] text-sm transition-colors">Save film</button>
      <a href="index.php" class="text-sm text-soft hover:text-bone underline underline-offset-4">Cancel</a>
    </div>
  </div>

  <!-- Poster link + live preview -->
  <div>
    <label for="poster_url" class="block text-sm text-mute mb-2">Poster image link</label>
    <input id="poster_url" name="poster_url" type="url" value="<?= e($film['poster_url']) ?>" maxlength="500" placeholder="https://..." class="<?= $input ?>">
    <?= $err('poster_url') ?>
    <p class="mt-2 text-xs text-mute leading-relaxed">Paste a link to an image. Nothing is uploaded. Leave it empty to show a title card.</p>
    <div class="mt-5 aspect-[2/3] w-full max-w-[16rem] overflow-hidden rounded-[4px] bg-surface ring-1 ring-white/10">
      <img id="preview" alt="Poster preview" class="h-full w-full object-cover <?= $film['poster_url'] ? '' : 'hidden' ?>" src="<?= e($film['poster_url']) ?>">
    </div>
  </div>
</form>

<script>
  const urlInput = document.getElementById('poster_url');
  const preview = document.getElementById('preview');
  urlInput.addEventListener('input', () => {
    preview.classList.toggle('hidden', !urlInput.value);
    if (urlInput.value) preview.src = urlInput.value;
  });
  preview.addEventListener('error', () => preview.classList.add('hidden'));
  preview.addEventListener('load', () => preview.classList.remove('hidden'));
</script>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
