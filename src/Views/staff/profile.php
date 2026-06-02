<?php
use App\Core\Csrf;

$banner = static function (?array $msg): string {
    if ($msg === null) {
        return '';
    }
    $color = $msg['ok'] ? 'var(--teal-700)' : 'var(--danger-600)';

    return '<div class="details__alert" style="margin:0 30px 18px;color:' . $color . ';">' . e($msg['message']) . '</div>';
};
?>
      <section style="margin-bottom:24px;">
        <h1 style="font-size:26px;font-weight:800;color:var(--ink-900);margin-bottom:4px;">Edytuj profil</h1>
        <p style="color:var(--ink-500);">Zaktualizuj swoje dane<?= $isVet ? ', dane zawodowe' : '' ?> i hasło.</p>
      </section>

      <section class="panel" style="margin-bottom:22px;">
        <div class="panel__head"><h2 class="panel__title">Dane osobowe</h2></div>
        <?= $banner($profileMsg) ?>
        <form action="/profile" method="post" style="padding:4px 30px 30px;">
          <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">

          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="imie">Imię</label></div>
              <div class="input-wrap"><input class="input" type="text" id="imie" name="imie" maxlength="100" value="<?= e($account->firstName) ?>" required></div>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="nazwisko">Nazwisko</label></div>
              <div class="input-wrap"><input class="input" type="text" id="nazwisko" name="nazwisko" maxlength="100" value="<?= e($account->lastName) ?>" required></div>
            </div>
          </div>

          <div class="field">
            <div class="field__row"><label class="field__label" for="email">Adres e-mail</label></div>
            <div class="input-wrap"><input class="input" type="email" id="email" name="email" value="<?= e($account->email) ?>" required></div>
          </div>

<?php if (!$isVet): ?>
          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="klinika_nazwa">Nazwa kliniki</label></div>
              <div class="input-wrap"><input class="input" type="text" id="klinika_nazwa" name="klinika_nazwa" maxlength="150" value="<?= e($clinic['name'] ?? '') ?>" required></div>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="klinika_haslo">Hasło dołączeniowe</label></div>
              <div class="input-wrap"><input class="input" type="text" id="klinika_haslo" name="klinika_haslo" maxlength="60" value="<?= e($clinic['join_code'] ?? '') ?>" required></div>
            </div>
          </div>
          <div class="field">
            <div class="field__row"><label class="field__label" for="klinika_adres">Adres kliniki</label></div>
            <div class="input-wrap"><input class="input" type="text" id="klinika_adres" name="klinika_adres" maxlength="255" value="<?= e($clinic['address'] ?? '') ?>" required></div>
          </div>
<?php endif; ?>

<?php if ($isVet): ?>
          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="tytul">Tytuł</label></div>
              <div class="input-wrap"><input class="input" type="text" id="tytul" name="tytul" maxlength="20" value="<?= e($vet['title']) ?>"></div>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="gabinet">Gabinet</label></div>
              <div class="input-wrap"><input class="input" type="text" id="gabinet" name="gabinet" maxlength="50" value="<?= e($vet['room'] ?? '') ?>"></div>
            </div>
          </div>
          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="specjalizacja">Specjalizacja</label></div>
              <div class="input-wrap"><input class="input" type="text" id="specjalizacja" name="specjalizacja" maxlength="100" value="<?= e($vet['specialization'] ?? '') ?>"></div>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="numer_licencji">Numer licencji</label></div>
              <div class="input-wrap"><input class="input" type="text" id="numer_licencji" name="numer_licencji" maxlength="50" value="<?= e($vet['license_number']) ?>" required></div>
            </div>
          </div>
<?php endif; ?>

          <button class="btn btn--primary btn--lg" type="submit">Zapisz dane</button>
        </form>
      </section>

      <section class="panel">
        <div class="panel__head"><h2 class="panel__title">Zmiana hasła</h2></div>
        <?= $banner($passwordMsg) ?>
        <form action="/profile/password" method="post" style="padding:4px 30px 30px;">
          <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">

          <div class="field">
            <div class="field__row"><label class="field__label" for="haslo_obecne">Aktualne hasło</label></div>
            <div class="input-wrap"><input class="input" type="password" id="haslo_obecne" name="haslo_obecne" autocomplete="current-password" required></div>
          </div>

          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="haslo">Nowe hasło</label></div>
              <div class="input-wrap"><input class="input" type="password" id="haslo" name="haslo" placeholder="Min. 8 znaków" autocomplete="new-password" required></div>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="haslo2">Powtórz nowe hasło</label></div>
              <div class="input-wrap"><input class="input" type="password" id="haslo2" name="haslo2" autocomplete="new-password" required></div>
            </div>
          </div>

          <button class="btn btn--primary btn--lg" type="submit">Zmień hasło</button>
        </form>
      </section>
