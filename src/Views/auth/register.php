<?php $old = $old ?? []; $role = $old['rola'] ?? 'lekarz'; ?>
  <main class="auth">

    <section class="auth__brand">
      <div class="auth__logo">
        <span class="brand-logo" aria-hidden="true">
          <svg class="icon" viewBox="0 0 24 24" fill="currentColor"><circle cx="6" cy="10" r="2"></circle><circle cx="10.5" cy="6" r="2"></circle><circle cx="15.5" cy="6" r="2"></circle><circle cx="19" cy="10.5" r="2"></circle><path d="M12.5 12c-2.2 0-4 1.7-4.7 3.4-.6 1.5-2 2.3-2 3.8 0 1.4 1.2 2.3 2.6 2.1 1.2-.2 2.6-.7 4.1-.7s2.9.5 4.1.7c1.4.2 2.6-.7 2.6-2.1 0-1.5-1.4-2.3-2-3.8C16.5 13.7 14.7 12 12.5 12z"></path></svg>
        </span>
      </div>
      <h1 class="auth__brand-name">VetClinic</h1>
      <p class="auth__tagline">Dołącz do zespołu i zarządzaj opieką nad pacjentami w&nbsp;jednym miejscu.</p>
      <div class="auth__art img-ph" role="img" aria-label="Ilustracja: zespół kliniki">
        ilustracja_powitalna.png<br>„Zdrowe zwierzęta, szczęśliwe życie"
      </div>
    </section>

    <section class="auth__panel">
      <form class="auth__form" action="/register" method="post">
        <input type="hidden" name="_csrf" value="<?= e(\App\Core\Csrf::token()) ?>">

        <div class="auth__mobile-head">
          <span class="brand-logo" aria-hidden="true">
            <svg class="icon" viewBox="0 0 24 24" fill="currentColor"><circle cx="6" cy="10" r="2"></circle><circle cx="10.5" cy="6" r="2"></circle><circle cx="15.5" cy="6" r="2"></circle><circle cx="19" cy="10.5" r="2"></circle><path d="M12.5 12c-2.2 0-4 1.7-4.7 3.4-.6 1.5-2 2.3-2 3.8 0 1.4 1.2 2.3 2.6 2.1 1.2-.2 2.6-.7 4.1-.7s2.9.5 4.1.7c1.4.2 2.6-.7 2.6-2.1 0-1.5-1.4-2.3-2-3.8C16.5 13.7 14.7 12 12.5 12z"></path></svg>
          </span>
          <span class="auth__brand-name">VetClinic</span>
        </div>

        <h2 class="auth__title">Załóż konto</h2>
        <p class="auth__subtitle">Utwórz konto pracownika, aby uzyskać dostęp do pulpitu kliniki.</p>

<?php if (!empty($errors)): ?>
        <div class="details__alert" style="flex-direction:column;align-items:flex-start;gap:6px;margin-bottom:18px;">
<?php foreach ($errors as $message): ?>
          <span><?= e($message) ?></span>
<?php endforeach; ?>
        </div>
<?php endif; ?>

        <div class="field-2">
          <div class="field">
            <div class="field__row"><label class="field__label" for="imie">Imię</label></div>
            <div class="input-wrap">
              <input class="input" type="text" id="imie" name="imie" placeholder="Anna" autocomplete="given-name" value="<?= e($old['imie'] ?? '') ?>" required>
            </div>
          </div>
          <div class="field">
            <div class="field__row"><label class="field__label" for="nazwisko">Nazwisko</label></div>
            <div class="input-wrap">
              <input class="input" type="text" id="nazwisko" name="nazwisko" placeholder="Kowalska" autocomplete="family-name" value="<?= e($old['nazwisko'] ?? '') ?>" required>
            </div>
          </div>
        </div>

        <div class="field">
          <div class="field__row"><label class="field__label" for="email">Adres e-mail</label></div>
          <div class="input-wrap">
            <span class="input-wrap__icon" aria-hidden="true">
              <svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>
            </span>
            <input class="input" type="email" id="email" name="email" placeholder="personel@vetclinic.pl" autocomplete="email" value="<?= e($old['email'] ?? '') ?>" required>
          </div>
        </div>

        <div class="field-2">
          <div class="field">
            <div class="field__row"><label class="field__label" for="haslo">Hasło</label></div>
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
              <input class="input" type="password" id="haslo2" name="haslo2" placeholder="Powtórz hasło" autocomplete="new-password" required>
            </div>
          </div>
        </div>

        <div class="field">
          <div class="field__row"><label class="field__label">Rola w klinice</label></div>
          <div class="role-options">
            <label class="role-option">
              <input type="radio" name="rola" value="lekarz"<?= $role === 'lekarz' ? ' checked' : '' ?>>
              <span class="role-option__radio"></span>
              Lekarz weterynarii
            </label>
            <label class="role-option">
              <input type="radio" name="rola" value="recepcja"<?= $role === 'recepcja' ? ' checked' : '' ?>>
              <span class="role-option__radio"></span>
              Recepcja
            </label>
          </div>
        </div>

        <label class="checkbox">
          <input type="checkbox" name="regulamin" required>
          Akceptuję <a class="field__link" href="#" style="display:inline">regulamin</a> i&nbsp;politykę prywatności (RODO).
        </label>

        <button class="btn btn--primary btn--block btn--lg" type="submit">
          Załóż konto
          <svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
        </button>

        <p class="auth__register">Masz już konto? <a href="/login">Zaloguj się</a></p>

        <div class="auth__security">
          <p class="auth__security-title">Bezpieczeństwo klasy korporacyjnej</p>
          <div class="auth__security-badges">
            <span>
              <svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l2.4 1.8 3 .2.9 2.9 2.2 2-1 2.9 1 2.9-2.2 2-.9 2.9-3 .2L12 22l-2.4-1.8-3-.2-.9-2.9-2.2-2 1-2.9-1-2.9 2.2-2 .9-2.9 3-.2z"></path><path d="m9 12 2 2 4-4"></path></svg>
              Zgodny z RODO
            </span>
            <span>
              <svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.5 8.5 8 11 4.5-2.5 8-6 8-11V5z"></path></svg>
              Szyfrowanie SSL
            </span>
          </div>
        </div>

      </form>
    </section>

  </main>
