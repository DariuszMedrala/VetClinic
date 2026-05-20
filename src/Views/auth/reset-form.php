<?php $done = $done ?? false; $invalid = $invalid ?? false; $error = $error ?? null; $token = $token ?? ''; ?>
  <main class="auth">

    <section class="auth__brand">
      <div class="auth__logo">
        <span class="brand-logo" aria-hidden="true">
          <svg class="icon" viewBox="0 0 24 24" fill="currentColor"><circle cx="6" cy="10" r="2"></circle><circle cx="10.5" cy="6" r="2"></circle><circle cx="15.5" cy="6" r="2"></circle><circle cx="19" cy="10.5" r="2"></circle><path d="M12.5 12c-2.2 0-4 1.7-4.7 3.4-.6 1.5-2 2.3-2 3.8 0 1.4 1.2 2.3 2.6 2.1 1.2-.2 2.6-.7 4.1-.7s2.9.5 4.1.7c1.4.2 2.6-.7 2.6-2.1 0-1.5-1.4-2.3-2-3.8C16.5 13.7 14.7 12 12.5 12z"></path></svg>
        </span>
      </div>
      <h1 class="auth__brand-name">VetClinic</h1>
      <p class="auth__tagline">Ustaw nowe hasło i&nbsp;wróć do swojego konta.</p>
      <div class="auth__art" style="overflow:hidden;">
        <img src="/assets/img/happy_pets.webp" alt="Pies i kot pod opieką VetClinic" style="width:100%;height:100%;display:block;object-fit:cover;border-radius:inherit;">
      </div>
    </section>

    <section class="auth__panel">
      <form class="auth__form" action="/reset-hasla/<?= e($token) ?>" method="post">
        <input type="hidden" name="_csrf" value="<?= e(\App\Core\Csrf::token()) ?>">

        <div class="auth__mobile-head">
          <span class="brand-logo" aria-hidden="true">
            <svg class="icon" viewBox="0 0 24 24" fill="currentColor"><circle cx="6" cy="10" r="2"></circle><circle cx="10.5" cy="6" r="2"></circle><circle cx="15.5" cy="6" r="2"></circle><circle cx="19" cy="10.5" r="2"></circle><path d="M12.5 12c-2.2 0-4 1.7-4.7 3.4-.6 1.5-2 2.3-2 3.8 0 1.4 1.2 2.3 2.6 2.1 1.2-.2 2.6-.7 4.1-.7s2.9.5 4.1.7c1.4.2 2.6-.7 2.6-2.1 0-1.5-1.4-2.3-2-3.8C16.5 13.7 14.7 12 12.5 12z"></path></svg>
          </span>
          <span class="auth__brand-name">VetClinic</span>
        </div>

<?php if ($done): ?>
        <h2 class="auth__title">Hasło zmienione</h2>
        <p class="auth__subtitle">Twoje hasło zostało zaktualizowane. Możesz się teraz zalogować.</p>
        <a class="btn btn--primary btn--block btn--lg" href="/login">Przejdź do logowania</a>
<?php elseif ($invalid): ?>
        <h2 class="auth__title">Link nieaktywny</h2>
        <p class="auth__subtitle">Ten link do zmiany hasła jest nieprawidłowy lub wygasł.</p>
        <a class="btn btn--primary btn--block btn--lg" href="/reset-hasla">Poproś o nowy link</a>
<?php else: ?>
        <h2 class="auth__title">Nowe hasło</h2>
        <p class="auth__subtitle">Wprowadź i potwierdź nowe hasło do swojego konta.</p>

<?php if ($error !== null): ?>
        <div class="details__alert" style="margin-bottom:18px;">
          <svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 3 2 20h20L12 3z"></path><path d="M12 9v5M12 17.5v.2"></path></svg>
          <?= e($error) ?>
        </div>
<?php endif; ?>

        <div class="field">
          <div class="field__row"><label class="field__label" for="haslo">Nowe hasło</label></div>
          <div class="input-wrap">
            <span class="input-wrap__icon" aria-hidden="true">
              <svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="9" rx="2"></rect><path d="M8 11V8a4 4 0 0 1 8 0v3"></path></svg>
            </span>
            <input class="input" type="password" id="haslo" name="haslo" placeholder="Min. 8 znaków" autocomplete="new-password" required>
          </div>
        </div>

        <div class="field">
          <div class="field__row"><label class="field__label" for="haslo2">Powtórz hasło</label></div>
          <div class="input-wrap">
            <span class="input-wrap__icon" aria-hidden="true">
              <svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="11" width="16" height="9" rx="2"></rect><path d="M8 11V8a4 4 0 0 1 8 0v3"></path></svg>
            </span>
            <input class="input" type="password" id="haslo2" name="haslo2" placeholder="Powtórz nowe hasło" autocomplete="new-password" required>
          </div>
        </div>

        <button class="btn btn--primary btn--block btn--lg" type="submit">Ustaw nowe hasło</button>
        <p class="auth__register"><a href="/login">← Wróć do logowania</a></p>
<?php endif; ?>
      </form>
    </section>

  </main>
