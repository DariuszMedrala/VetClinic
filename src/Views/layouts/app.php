<?php
$roleLabel = match ($user['role'] ?? '') {
    'vet' => 'Lekarz weterynarii',
    'admin' => 'Recepcja',
    'client' => 'Klient',
    default => '',
};
$initials = strtoupper(mb_substr($user['name'] ?? 'VC', 0, 2));
$active = $active ?? '';
$isClient = ($user['role'] ?? '') === 'client';
$navClass = static fn (string $key): string => 'nav-item' . ($active === $key ? ' nav-item--active' : '');
$tabClass = static fn (string $key): string => 'bottom-nav__item' . ($active === $key ? ' bottom-nav__item--active' : '');
?>
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
<body class="app">

  <aside class="sidebar">
    <div class="sidebar__brand">
      <span class="brand-logo" aria-hidden="true">
        <svg class="icon" viewBox="0 0 24 24" fill="currentColor"><circle cx="6" cy="10" r="2"></circle><circle cx="10.5" cy="6" r="2"></circle><circle cx="15.5" cy="6" r="2"></circle><circle cx="19" cy="10.5" r="2"></circle><path d="M12.5 12c-2.2 0-4 1.7-4.7 3.4-.6 1.5-2 2.3-2 3.8 0 1.4 1.2 2.3 2.6 2.1 1.2-.2 2.6-.7 4.1-.7s2.9.5 4.1.7c1.4.2 2.6-.7 2.6-2.1 0-1.5-1.4-2.3-2-3.8C16.5 13.7 14.7 12 12.5 12z"></path></svg>
      </span>
      <span class="brand-name">VetClinic</span>
    </div>

    <nav class="sidebar__nav">
<?php if ($isClient): ?>
      <a class="<?= $navClass('dashboard') ?>" href="/dashboard">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1.5"></rect><rect x="14" y="3" width="7" height="5" rx="1.5"></rect><rect x="14" y="12" width="7" height="9" rx="1.5"></rect><rect x="3" y="16" width="7" height="5" rx="1.5"></rect></svg>
        Mój pulpit
      </a>
      <a class="<?= $navClass('profil') ?>" href="/profil">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c0-4 4-6 8-6s8 2 8 6"></path></svg>
        Edytuj profil
      </a>
<?php else: ?>
      <a class="<?= $navClass('pulpit') ?>" href="/pulpit">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1.5"></rect><rect x="14" y="3" width="7" height="5" rx="1.5"></rect><rect x="14" y="12" width="7" height="9" rx="1.5"></rect><rect x="3" y="16" width="7" height="5" rx="1.5"></rect></svg>
        Pulpit
      </a>
      <a class="<?= $navClass('kalendarz') ?>" href="/kalendarz">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="2"></rect><path d="M3 9h18M8 2v4M16 2v4"></path></svg>
        Kalendarz
      </a>
      <a class="<?= $navClass('pacjenci') ?>" href="/pacjenci">
        <svg class="icon" viewBox="0 0 24 24" fill="currentColor"><circle cx="6" cy="10" r="2"></circle><circle cx="10.5" cy="6" r="2"></circle><circle cx="15.5" cy="6" r="2"></circle><circle cx="19" cy="10.5" r="2"></circle><path d="M12.5 12c-2.2 0-4 1.7-4.7 3.4-.6 1.5-2 2.3-2 3.8 0 1.4 1.2 2.3 2.6 2.1 1.2-.2 2.6-.7 4.1-.7s2.9.5 4.1.7c1.4.2 2.6-.7 2.6-2.1 0-1.5-1.4-2.3-2-3.8C16.5 13.7 14.7 12 12.5 12z"></path></svg>
        Klienci i zwierzęta
      </a>
      <a class="<?= $navClass('platnosci') ?>" href="/platnosci">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"></rect><circle cx="12" cy="12" r="2.5"></circle><path d="M6 9v6M18 9v6"></path></svg>
        Płatności
      </a>
<?php endif; ?>
    </nav>

    <div class="sidebar__spacer"></div>

    <form method="post" action="/logout">
      <input type="hidden" name="_csrf" value="<?= e(\App\Core\Csrf::token()) ?>">
      <button class="nav-item" type="submit">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><path d="M16 17l5-5-5-5M21 12H9"></path></svg>
        Wyloguj się
      </button>
    </form>
  </aside>

  <div class="app__main">

    <header class="topbar">
      <div class="topbar__title"><?= e($title ?? 'VetClinic') ?></div>
      <div class="topbar__actions">
        <span class="topbar__divider"></span>
        <div class="user">
          <span class="user__meta">
            <span class="user__name"><?= e($user['name'] ?? '') ?></span><br>
            <span class="user__role"><?= e($roleLabel) ?></span>
          </span>
          <span class="user__avatar" style="background:#117a6d;color:#fff;border-radius:50%;display:grid;place-items:center;font-weight:800;font-size:13px;"><?= e($initials) ?></span>
        </div>
      </div>
    </header>

    <header class="mobile-header">
      <div class="mobile-header__left">
        <span class="mobile-header__brand">VetClinic</span>
      </div>
      <div class="mobile-header__actions">
        <span class="user__avatar" style="background:#117a6d;color:#fff;border-radius:50%;display:grid;place-items:center;font-weight:800;font-size:12px;"><?= e($initials) ?></span>
      </div>
    </header>

    <main class="content">
<?= $content ?>
    </main>
  </div>

  <nav class="bottom-nav">
<?php if ($isClient): ?>
    <a class="<?= $tabClass('dashboard') ?>" href="/dashboard">
      <span class="bottom-nav__icon"><svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 11 12 4l8 7"></path><path d="M6 10v9h12v-9"></path></svg></span>
      Start
    </a>
    <a class="<?= $tabClass('profil') ?>" href="/profil">
      <span class="bottom-nav__icon"><svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c0-4 4-6 8-6s8 2 8 6"></path></svg></span>
      Profil
    </a>
<?php else: ?>
    <a class="<?= $tabClass('pulpit') ?>" href="/pulpit">
      <span class="bottom-nav__icon"><svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 11 12 4l8 7"></path><path d="M6 10v9h12v-9"></path></svg></span>
      Start
    </a>
    <a class="<?= $tabClass('kalendarz') ?>" href="/kalendarz">
      <span class="bottom-nav__icon"><svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="2"></rect><path d="M3 9h18M8 2v4M16 2v4"></path></svg></span>
      Kalendarz
    </a>
    <a class="<?= $tabClass('pacjenci') ?>" href="/pacjenci">
      <span class="bottom-nav__icon"><svg class="icon icon--sm" viewBox="0 0 24 24" fill="currentColor"><circle cx="6" cy="10" r="1.8"></circle><circle cx="10.5" cy="6.5" r="1.8"></circle><circle cx="15.5" cy="6.5" r="1.8"></circle><circle cx="19" cy="10.5" r="1.8"></circle><path d="M12.5 12c-2 0-3.6 1.5-4.2 3-.5 1.3-1.8 2-1.8 3.4 0 1.2 1 2 2.3 1.8 1-.2 2.3-.6 3.7-.6s2.7.4 3.7.6c1.3.2 2.3-.6 2.3-1.8 0-1.4-1.3-2.1-1.8-3.4-.6-1.5-2.2-3-4.2-3z"></path></svg></span>
      Klienci
    </a>
    <a class="<?= $tabClass('platnosci') ?>" href="/platnosci">
      <span class="bottom-nav__icon"><svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"></rect><circle cx="12" cy="12" r="2.5"></circle></svg></span>
      Płatności
    </a>
<?php endif; ?>
  </nav>

</body>
</html>
