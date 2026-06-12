<?php
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
      <p style="margin-bottom:18px;"><a class="field__link" href="/portal">← Wróć do portalu</a></p>

      <div class="profile-grid">
        <section class="panel profile-card">
<?php if ($pet->photoPath !== null): ?>
          <img class="profile-card__photo" src="<?= e($pet->photoPath) ?>" alt="<?= e($pet->name) ?>" style="object-fit:cover;">
<?php else: ?>
          <span class="profile-card__photo" style="display:grid;place-items:center;background:#eaf7f4;">
            <svg width="58" height="58" viewBox="0 0 24 24" fill="#117a6d" aria-hidden="true"><circle cx="6" cy="10" r="2"></circle><circle cx="10.5" cy="6" r="2"></circle><circle cx="15.5" cy="6" r="2"></circle><circle cx="19" cy="10.5" r="2"></circle><path d="M12.5 12c-2.2 0-4 1.7-4.7 3.4-.6 1.5-2 2.3-2 3.8 0 1.4 1.2 2.3 2.6 2.1 1.2-.2 2.6-.7 4.1-.7s2.9.5 4.1.7c1.4.2 2.6-.7 2.6-2.1 0-1.5-1.4-2.3-2-3.8C16.5 13.7 14.7 12 12.5 12z"></path></svg>
          </span>
<?php endif; ?>
          <div class="profile-card__main">
            <div class="profile-card__top">
              <div>
                <h1 class="profile-card__name"><?= e($pet->name) ?></h1>
                <p class="profile-card__breed"><?= e($pet->speciesName) ?><?= $pet->breed !== null ? ' · ' . e($pet->breed) : '' ?> • <?= e($pet->ageLabel()) ?> / <?= e($pet->sexLabel()) ?></p>
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

      <section class="panel">
        <div class="panel__head"><h2 class="panel__title">Szczepienia</h2></div>
<?php if ($vaccinations === []): ?>
        <p class="panel__empty">Brak zarejestrowanych szczepień.</p>
<?php else: ?>
        <div class="table-scroll">
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
        </div>
<?php endif; ?>
      </section>

      <section class="panel">
        <div class="panel__head"><h2 class="panel__title">Historia wizyt i zabiegów</h2></div>
<?php if ($history === []): ?>
        <p class="panel__empty">Brak wizyt w historii.</p>
<?php else: ?>
        <div class="table-scroll">
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
        </div>
<?php endif; ?>
      </section>
