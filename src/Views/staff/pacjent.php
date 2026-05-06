<?php
use App\Core\Csrf;
use DateTimeImmutable;

$fmtDate = static fn (?string $d): string => $d ? (new DateTimeImmutable($d))->format('d.m.Y') : '—';
$vaxBadge = static fn (string $s): array => $s === 'overdue' ? ['Zaległe', 'badge--overdue'] : ['Aktualne', 'badge--uptodate'];
$apptBadge = static fn (string $s): array => match ($s) {
    'scheduled' => ['Zaplanowana', 'badge--waiting'],
    'confirmed' => ['Potwierdzona', 'badge--confirmed'],
    'in_progress' => ['W trakcie', 'badge--progress'],
    'completed' => ['Zakończona', 'badge--uptodate'],
    'cancelled' => ['Anulowana', 'badge--overdue'],
    default => [$s, 'badge--waiting'],
};

$overdue = 0;
foreach ($vaccinations as $v) {
    if ($v['status'] === 'overdue') {
        $overdue++;
    }
}
?>
      <p style="margin-bottom:18px;"><a class="field__link" href="/pacjenci">← Wróć do listy</a></p>

      <div class="profile-grid" data-csrf="<?= e(Csrf::token()) ?>">
        <section class="panel profile-card">
          <span class="profile-card__photo img-ph" style="font-size:11px"><?= e($pet->name) ?></span>
          <div class="profile-card__main">
            <div class="profile-card__top">
              <div>
                <h1 class="profile-card__name"><?= e($pet->name) ?></h1>
                <p class="profile-card__breed"><?= e($pet->speciesName) ?><?= $pet->breed !== null ? ' · ' . e($pet->breed) : '' ?> • <?= e($pet->ageLabel()) ?> / <?= e($pet->sexLabel()) ?></p>
              </div>
              <div class="profile-card__actions">
                <button class="btn btn--outline-teal btn--sm" type="button" id="toggle-edit">Edytuj profil</button>
                <button class="btn btn--danger btn--sm js-delete-pet" type="button" data-id="<?= e((string) $pet->id) ?>">Usuń</button>
              </div>
            </div>
            <div class="facts">
              <div class="fact"><div class="fact__label">Właściciel</div><div class="fact__value"><?= e($pet->ownerName) ?></div></div>
              <div class="fact"><div class="fact__label">Telefon</div><div class="fact__value"><?= e($pet->ownerPhone ?? '—') ?></div></div>
              <div class="fact"><div class="fact__label">Waga</div><div class="fact__value"><?= e($pet->weightLabel()) ?></div></div>
            </div>
          </div>
        </section>

        <aside class="panel health">
          <h2 class="health__title">Przegląd zdrowia</h2>
          <div class="health__row"><span>Szczepienia</span><span class="badge <?= $overdue > 0 ? 'badge--overdue' : 'badge--uptodate' ?>"><?= $overdue > 0 ? e((string) $overdue) . ' zaległe' : 'Aktualne' ?></span></div>
          <div class="health__row"><span>Punkty lojalnościowe</span><span class="badge badge--loyalty"><?= e((string) $pet->loyaltyPoints) ?> pkt</span></div>
          <div class="health__row"><span>Wizyty w historii</span><span class="badge badge--uptodate"><?= e((string) count($history)) ?></span></div>
        </aside>
      </div>

      <section class="panel" id="edit-panel" style="display:none;">
        <div class="panel__head"><h2 class="panel__title">Edytuj profil</h2></div>
        <form id="edit-pet-form" data-id="<?= e((string) $pet->id) ?>" style="padding:0 30px 30px;">
          <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">

          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="e_name">Imię</label></div>
              <div class="input-wrap"><input class="input" type="text" id="e_name" name="name" maxlength="100" value="<?= e($pet->name) ?>" required></div>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="e_species">Gatunek</label></div>
              <div class="input-wrap">
                <select class="input" id="e_species" name="species_id" required>
<?php foreach ($species as $s): ?>
                  <option value="<?= e((string) $s['id']) ?>"<?= (int) $s['id'] === $pet->speciesId ? ' selected' : '' ?>><?= e($s['name']) ?></option>
<?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="e_breed">Rasa</label></div>
              <div class="input-wrap"><input class="input" type="text" id="e_breed" name="breed" maxlength="100" value="<?= e($pet->breed ?? '') ?>"></div>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="e_sex">Płeć</label></div>
              <div class="input-wrap">
                <select class="input" id="e_sex" name="sex">
                  <option value="unknown"<?= $pet->sex === 'unknown' ? ' selected' : '' ?>>Nieznana</option>
                  <option value="male"<?= $pet->sex === 'male' ? ' selected' : '' ?>>Samiec</option>
                  <option value="female"<?= $pet->sex === 'female' ? ' selected' : '' ?>>Samica</option>
                </select>
              </div>
            </div>
          </div>

          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="e_birth">Data urodzenia</label></div>
              <div class="input-wrap"><input class="input" type="date" id="e_birth" name="birth_date" value="<?= e($pet->birthDate?->format('Y-m-d') ?? '') ?>"></div>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="e_weight">Waga (kg)</label></div>
              <div class="input-wrap"><input class="input" type="number" id="e_weight" name="weight" step="0.1" min="0" value="<?= e($pet->weightKg ?? '') ?>"></div>
            </div>
          </div>

          <div class="details__alert js-result" style="display:none;margin-bottom:16px;"></div>
          <button class="btn btn--primary btn--lg" type="submit">Zapisz zmiany</button>
        </form>
      </section>

      <section class="panel">
        <div class="panel__head"><h2 class="panel__title">Szczepienia</h2></div>
<?php if ($vaccinations === []): ?>
        <p class="panel__empty">Brak zarejestrowanych szczepień.</p>
<?php else: ?>
        <table class="vax-table">
          <thead><tr><th>Szczepionka</th><th>Data podania</th><th>Ważne do</th><th>Status</th><th>Podał</th></tr></thead>
          <tbody>
<?php foreach ($vaccinations as $v): [$label, $cls] = $vaxBadge($v['status']); ?>
            <tr>
              <td><?= e($v['vaccine_name']) ?></td>
              <td><?= e($fmtDate($v['administered_at'])) ?></td>
              <td<?= $v['status'] === 'overdue' ? ' class="is-overdue"' : '' ?>><?= e($fmtDate($v['expires_at'])) ?></td>
              <td><span class="badge <?= e($cls) ?>"><?= e($label) ?></span></td>
              <td><?= e($v['administered_by'] ?? '—') ?></td>
            </tr>
<?php endforeach; ?>
          </tbody>
        </table>
<?php endif; ?>
      </section>

      <section class="panel">
        <div class="panel__head"><h2 class="panel__title">Historia wizyt i zabiegów</h2></div>
<?php if ($history === []): ?>
        <p class="panel__empty">Brak wizyt w historii.</p>
<?php else: ?>
        <table class="vax-table">
          <thead><tr><th>Data</th><th>Powód</th><th>Lekarz</th><th>Zabiegi</th><th>Status</th></tr></thead>
          <tbody>
<?php foreach ($history as $h): [$label, $cls] = $apptBadge($h['status']); ?>
            <tr>
              <td><?= e((new DateTimeImmutable($h['starts_at']))->format('d.m.Y H:i')) ?></td>
              <td><?= e($h['reason']) ?></td>
              <td><?= e($h['vet_name']) ?></td>
              <td><?= e($h['procedures']) ?></td>
              <td><span class="badge <?= e($cls) ?>"><?= e($label) ?></span></td>
            </tr>
<?php endforeach; ?>
          </tbody>
        </table>
<?php endif; ?>
      </section>

      <script src="/assets/js/pacjenci.js"></script>
