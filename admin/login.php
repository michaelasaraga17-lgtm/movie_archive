<?php
require_once __DIR__ . '/../includes/auth.php';

if (is_admin()) {
    header('Location: index.php');
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Enter your username and password.';
    } else {
        try {
            $stmt = db()->prepare('SELECT id, password_hash FROM admins WHERE username = :u');
            $stmt->execute([':u' => $username]);
            $row = $stmt->fetch();

            if ($row && password_verify($password, $row['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['admin_id'] = (int)$row['id'];
                header('Location: index.php');
                exit;
            }
            usleep(500000); // slows down password guessing
            $error = 'Wrong username or password.';
        } catch (PDOException $e) {
            $error = 'Could not reach the database. Is MySQL running?';
        }
    }
}

$pageTitle = 'Sign in';
require __DIR__ . '/../includes/admin_header.php';
?>
<div class="max-w-sm mt-10">
  <h1 class="font-display text-5xl tracking-tight mb-3">Sign in</h1>
  <p class="text-soft mb-10">Manage the films in the archive.</p>

  <?php if ($error): ?>
    <p class="mb-6 rounded-lg border border-danger/40 px-4 py-3 text-sm text-danger" role="alert"><?= e($error) ?></p>
  <?php endif; ?>

  <form method="post" class="space-y-6">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div>
      <label for="username" class="block text-sm text-mute mb-2">Username</label>
      <input id="username" name="username" value="<?= e($username) ?>" required maxlength="50" autocomplete="username" autofocus
             class="w-full rounded-lg bg-surface border border-line focus:border-accent px-4 py-3 text-sm">
    </div>
    <div>
      <label for="password" class="block text-sm text-mute mb-2">Password</label>
      <input id="password" name="password" type="password" required autocomplete="current-password"
             class="w-full rounded-lg bg-surface border border-line focus:border-accent px-4 py-3 text-sm">
    </div>
    <button class="rounded-full bg-accent hover:bg-bone text-ink font-medium px-7 py-3 min-h-[44px] text-sm transition-colors">Sign in</button>
  </form>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
