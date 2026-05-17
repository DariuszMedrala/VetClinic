<?php use App\Core\Csrf; ?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? 'VetClinic') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body style="background:#f5f7f9;min-height:100vh;">

  <header style="background:#fff;border-bottom:1px solid var(--line);padding:14px 22px;display:flex;align-items:center;justify-content:space-between;gap:16px;">
    <span style="display:flex;align-items:center;gap:10px;font-weight:800;color:var(--ink-900);">
      <span class="brand-logo" aria-hidden="true">
        <svg class="icon" viewBox="0 0 24 24" fill="currentColor"><circle cx="6" cy="10" r="2"></circle><circle cx="10.5" cy="6" r="2"></circle><circle cx="15.5" cy="6" r="2"></circle><circle cx="19" cy="10.5" r="2"></circle><path d="M12.5 12c-2.2 0-4 1.7-4.7 3.4-.6 1.5-2 2.3-2 3.8 0 1.4 1.2 2.3 2.6 2.1 1.2-.2 2.6-.7 4.1-.7s2.9.5 4.1.7c1.4.2 2.6-.7 2.6-2.1 0-1.5-1.4-2.3-2-3.8C16.5 13.7 14.7 12 12.5 12z"></path></svg>
      </span>
      VetClinic
    </span>
    <div style="display:flex;align-items:center;gap:16px;">
      <span style="font-weight:700;color:var(--ink-700);"><?= e($user['name'] ?? '') ?></span>
      <form method="post" action="/logout" style="margin:0;">
        <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
        <button class="btn btn--soft btn--sm" type="submit">Wyloguj się</button>
      </form>
    </div>
  </header>

  <main style="max-width:980px;margin:0 auto;padding:28px 20px 64px;">
<?= $content ?>
  </main>

</body>
</html>
