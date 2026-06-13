<?php
$fmtDate = static fn (?string $d): string => $d ? (new DateTimeImmutable($d))->format('d.m.Y') : '—';
?>
      <section style="margin-bottom:24px;">
        <h1 style="font-size:26px;font-weight:800;color:var(--ink-900);margin-bottom:4px;">Alerty</h1>
        <p style="color:var(--ink-500);">Zaległe szczepienia w klinice<?= ($clinic['name'] ?? '') !== '' ? ' ' . e($clinic['name']) : '' ?>.</p>
      </section>

      <section class="panel">
        <div class="panel__head">
          <h2 class="panel__title">Zaległe szczepienia</h2>
          <span class="panel__meta"><?= e((string) count($overdue)) ?></span>
        </div>
<?php if ($overdue === []): ?>
        <p class="panel__empty">Brak alertów — wszystkie szczepienia są aktualne.</p>
<?php else: ?>
        <div class="table-scroll">
        <table class="schedule schedule--even">
          <thead><tr><th>Pacjent</th><th>Właściciel</th><th>Szczepionka</th><th>Ważne do</th><th>Akcja</th></tr></thead>
          <tbody>
<?php foreach ($overdue as $row): ?>
            <tr>
              <td data-label="Pacjent"><?= e($row['pet_name']) ?> (<?= e($row['species']) ?>)</td>
              <td data-label="Właściciel"><?= e($row['owner_name']) ?></td>
              <td data-label="Szczepionka"><?= e($row['vaccine_name']) ?></td>
              <td data-label="Ważne do"><span class="badge badge--overdue"><?= e($fmtDate($row['expires_at'])) ?></span></td>
              <td data-label="Akcja"><a class="btn btn--outline-teal btn--sm" href="/patients/<?= e((string) $row['pet_id']) ?>">Karta pacjenta</a></td>
            </tr>
<?php endforeach; ?>
          </tbody>
        </table>
        </div>
<?php endif; ?>
      </section>
