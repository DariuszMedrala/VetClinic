<?php $sent = $sent ?? false; $error = $error ?? null; $email = $email ?? ''; ?>
  <main class="auth">

    <section class="auth__brand">
      <div class="auth__logo">
        <span class="brand-logo" aria-hidden="true">
          <svg class="icon" viewBox="0 0 24 24" fill="currentColor"><circle cx="6" cy="10" r="2"></circle><circle cx="10.5" cy="6" r="2"></circle><circle cx="15.5" cy="6" r="2"></circle><circle cx="19" cy="10.5" r="2"></circle><path d="M12.5 12c-2.2 0-4 1.7-4.7 3.4-.6 1.5-2 2.3-2 3.8 0 1.4 1.2 2.3 2.6 2.1 1.2-.2 2.6-.7 4.1-.7s2.9.5 4.1.7c1.4.2 2.6-.7 2.6-2.1 0-1.5-1.4-2.3-2-3.8C16.5 13.7 14.7 12 12.5 12z"></path></svg>
        </span>
      </div>
      <h1 class="auth__brand-name">VetClinic</h1>
      <p class="auth__tagline">Odzyskaj dostęp do konta i wróć do&nbsp;opieki nad pacjentami.</p>
      <div class="auth__art" style="overflow:hidden;">
        <img src="/assets/img/happy_pets.webp" alt="Pies i kot pod opieką VetClinic" style="width:100%;height:100%;display:block;object-fit:cover;border-radius:inherit;">
      </div>
    </section>

    <section class="auth__panel">
      <form class="auth__form" action="/reset-hasla" method="post">
        <input type="hidden" name="_csrf" value="<?= e(\App\Core\Csrf::token()) ?>">

        <div class="auth__mobile-head">
          <span class="brand-logo" aria-hidden="true">
            <svg class="icon" viewBox="0 0 24 24" fill="currentColor"><circle cx="6" cy="10" r="2"></circle><circle cx="10.5" cy="6" r="2"></circle><circle cx="15.5" cy="6" r="2"></circle><circle cx="19" cy="10.5" r="2"></circle><path d="M12.5 12c-2.2 0-4 1.7-4.7 3.4-.6 1.5-2 2.3-2 3.8 0 1.4 1.2 2.3 2.6 2.1 1.2-.2 2.6-.7 4.1-.7s2.9.5 4.1.7c1.4.2 2.6-.7 2.6-2.1 0-1.5-1.4-2.3-2-3.8C16.5 13.7 14.7 12 12.5 12z"></path></svg>
          </span>
          <span class="auth__brand-name">VetClinic</span>
        </div>

        <h2 class="auth__title">Reset hasła</h2>
        <p class="auth__subtitle">Podaj adres e-mail powiązany z kontem, a wyślemy link do zmiany hasła.</p>

<?php if ($sent): ?>
        <div class="details__alert" style="margin-bottom:18px;color:var(--teal-700);align-items:flex-start;">
          <svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="9"></circle><path d="m8 12 3 3 5-5"></path></svg>
          Jeśli konto o tym adresie istnieje w naszej bazie, wysłaliśmy na nie link do zmiany hasła. Sprawdź skrzynkę.
        </div>
        <p class="auth__register"><a href="/login">← Wróć do logowania</a></p>
<?php else: ?>
<?php if ($error !== null): ?>
        <div class="details__alert" style="margin-bottom:18px;">
          <svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 3 2 20h20L12 3z"></path><path d="M12 9v5M12 17.5v.2"></path></svg>
          <?= e($error) ?>
        </div>
<?php endif; ?>
        <div class="field">
          <div class="field__row"><label class="field__label" for="email">Adres e-mail</label></div>
          <div class="input-wrap">
            <span class="input-wrap__icon" aria-hidden="true">
              <svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>
            </span>
            <input class="input" type="email" id="email" name="email" placeholder="twoj@email.pl" autocomplete="email" value="<?= e($email) ?>" required>
          </div>
        </div>

        <button class="btn btn--primary btn--block btn--lg" type="submit">Wyślij link do resetu</button>
        <p class="auth__register">Pamiętasz hasło? <a href="/login">Zaloguj się</a></p>
<?php endif; ?>
      </form>
    </section>

  </main>
