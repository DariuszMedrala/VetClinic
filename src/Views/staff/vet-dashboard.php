<?php use App\Core\Csrf; $now = new DateTimeImmutable(); ?>
      <section style="margin-bottom:24px;">
        <h1 style="font-size:26px;font-weight:800;color:var(--ink-900);margin-bottom:4px;">Pulpit lekarza</h1>
        <p style="color:var(--ink-500);">Twoje wizyty i rozliczenia.</p>
      </section>

      <section class="stat-grid">
        <article class="stat-card">
          <span class="stat-card__icon">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="2"></rect><path d="M3 9h18M8 2v4M16 2v4"></path></svg>
          </span>
          <div class="stat-card__body">
            <span class="stat-card__label">Dzisiejsze wizyty</span>
            <span class="stat-card__value stat-card__value--teal"><?= e((string) $today) ?></span>
          </div>
        </article>
        <article class="stat-card">
          <span class="stat-card__icon">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>
          </span>
          <div class="stat-card__body">
            <span class="stat-card__label">Nadchodzące wizyty</span>
            <span class="stat-card__value stat-card__value--blue"><?= e((string) count($upcoming)) ?></span>
          </div>
        </article>
        <article class="stat-card">
          <span class="stat-card__icon">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="3" width="16" height="18" rx="2"></rect><path d="M9 3v3h6V3M8 12h8M8 16h5"></path></svg>
          </span>
          <div class="stat-card__body">
            <span class="stat-card__label">Do rozliczenia</span>
            <span class="stat-card__value stat-card__value--teal"><?= e((string) count($toInvoice)) ?></span>
          </div>
        </article>
      </section>

      <section class="panel" data-csrf="<?= e(Csrf::token()) ?>">
        <div class="panel__head">
          <h2 class="panel__title">Nadchodzące wizyty</h2>
          <span class="panel__meta"><?= e((string) count($upcoming)) ?></span>
        </div>
<?php if ($upcoming === []): ?>
        <p class="panel__empty">Brak nadchodzących wizyt.</p>
<?php else: ?>
        <table class="schedule schedule--even">
          <thead><tr><th>Dzień</th><th>Godzina</th><th>Pacjent</th><th>Właściciel</th><th>Status</th><th>Akcja</th></tr></thead>
          <tbody>
<?php foreach ($upcoming as $a): ?>
<?php $canComplete = $now >= $a->startsAt->modify('+15 minutes'); ?>
            <tr data-row="<?= e((string) $a->id) ?>">
              <td><span class="sched-time"><b><?= e($a->weekdayShort()) ?></b>&nbsp;<?= e($a->dateShort()) ?></span></td>
              <td><span class="sched-time"><?= e($a->time()) ?></span></td>
              <td><?= e($a->petName) ?> (<?= e($a->species) ?>)</td>
              <td><?= e($a->clientName) ?></td>
              <td><span class="badge <?= e($a->badgeClass()) ?>"><?= e($a->statusLabel()) ?></span></td>
              <td><button class="btn btn--primary btn--sm js-complete" data-id="<?= e((string) $a->id) ?>" data-can-complete="<?= $canComplete ? '1' : '0' ?>">Zakończ</button></td>
            </tr>
<?php endforeach; ?>
          </tbody>
        </table>
<?php endif; ?>
      </section>

      <section class="panel">
        <div class="panel__head">
          <h2 class="panel__title">Do rozliczenia</h2>
          <span class="panel__meta"><?= e((string) count($toInvoice)) ?></span>
        </div>
<?php if ($toInvoice === []): ?>
        <p class="panel__empty">Brak zakończonych wizyt oczekujących na fakturę.</p>
<?php else: ?>
        <table class="schedule schedule--even">
          <thead><tr><th>Data</th><th>Pacjent</th><th>Właściciel</th><th>Powód</th><th>Akcja</th></tr></thead>
          <tbody>
<?php foreach ($toInvoice as $a): ?>
            <tr>
              <td><span class="sched-time"><?= e($a->dateShort()) ?> <?= e($a->time()) ?></span></td>
              <td><?= e($a->petName) ?> (<?= e($a->species) ?>)</td>
              <td><?= e($a->clientName) ?></td>
              <td><?= e($a->reason) ?></td>
              <td><a class="btn btn--primary btn--sm" href="/invoices/new/<?= e((string) $a->id) ?>">Wystaw fakturę</a></td>
            </tr>
<?php endforeach; ?>
          </tbody>
        </table>
<?php endif; ?>
      </section>

      <div class="modal-backdrop" id="notice-modal">
        <div class="modal">
          <h3 class="modal__title" id="notice-title">Nie można zakończyć wizyty</h3>
          <p class="modal__text" id="notice-text"></p>
          <div class="modal__actions">
            <button class="btn btn--primary" type="button" id="notice-ok">Rozumiem</button>
          </div>
        </div>
      </div>

      <div class="modal-backdrop" id="complete-modal">
        <div class="modal">
          <h3 class="modal__title">Zakończ wizytę</h3>
          <p class="modal__text">Dodaj notatkę z wizyty (opcjonalnie) i potwierdź zakończenie.</p>
          <textarea class="modal__textarea" id="complete-notes" placeholder="Notatka z wizyty…" maxlength="2000"></textarea>
          <div class="modal__actions">
            <button class="btn btn--soft" type="button" id="complete-cancel">Anuluj</button>
            <button class="btn btn--primary" type="button" id="complete-confirm">Zakończ wizytę</button>
          </div>
        </div>
      </div>

      <script src="<?= e(asset('/assets/js/vet-dashboard.js')) ?>"></script>
