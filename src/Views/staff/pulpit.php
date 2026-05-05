<?php
use App\Core\Csrf;

$paw = '<svg class="icon icon--sm" viewBox="0 0 24 24" fill="currentColor"><circle cx="6" cy="10" r="1.7"></circle><circle cx="10.5" cy="6.5" r="1.7"></circle><circle cx="15.5" cy="6.5" r="1.7"></circle><circle cx="19" cy="10.5" r="1.7"></circle><path d="M12.5 12c-2 0-3.6 1.5-4.2 3-.5 1.3-1.8 2-1.8 3.4 0 1.2 1 2 2.3 1.8 1-.2 2.3-.6 3.7-.6s2.7.4 3.7.6c1.3.2 2.3-.6 2.3-1.8 0-1.4-1.3-2.1-1.8-3.4-.6-1.5-2.2-3-4.2-3z"></path></svg>';
?>
      <section class="stat-grid">
        <article class="stat-card">
          <span class="stat-card__icon">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="2"></rect><path d="M3 9h18M8 2v4M16 2v4"></path></svg>
          </span>
          <div class="stat-card__body">
            <span class="stat-card__label">Dzisiejsze wizyty</span>
            <span class="stat-card__value stat-card__value--teal"><?= e((string) $stats['appointmentsToday']) ?></span>
          </div>
        </article>

        <article class="stat-card">
          <span class="stat-card__icon">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="3" width="16" height="18" rx="2"></rect><path d="M9 3v3h6V3"></path><circle cx="16" cy="14" r="3"></circle><path d="M16 12.5v1.5l1 1"></path></svg>
          </span>
          <div class="stat-card__body">
            <span class="stat-card__label">Oczekujące faktury</span>
            <span class="stat-card__value stat-card__value--blue"><?= e((string) $stats['pendingInvoices']) ?></span>
          </div>
        </article>

        <article class="alert">
          <span class="alert__icon">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 3 2 20h20L12 3z"></path><path d="M12 9v5M12 17.5v.2"></path></svg>
          </span>
          <div class="alert__body">
            <p class="alert__title">Alerty systemowe</p>
            <p class="alert__text"><?= e((string) $stats['overdueVaccinations']) ?> zwierząt ma zaległe szczepienia!</p>
          </div>
        </article>
      </section>

      <section class="panel" data-csrf="<?= e(Csrf::token()) ?>">
        <div class="panel__head">
          <h2 class="panel__title">Nadchodzący harmonogram</h2>
          <span class="panel__meta"><?= e((string) count($appointments)) ?> wizyt</span>
        </div>

<?php if ($appointments === []): ?>
        <p class="panel__empty">Brak nadchodzących wizyt.</p>
<?php else: ?>
        <table class="schedule">
          <thead>
            <tr>
              <th>Dzień</th>
              <th>Godzina</th>
              <th>Pacjent</th>
              <th>Właściciel</th>
              <th>Lekarz</th>
              <th>Akcja</th>
            </tr>
          </thead>
          <tbody>
<?php foreach ($appointments as $a): ?>
            <tr data-row="<?= e((string) $a->id) ?>">
              <td><span class="sched-time"><b><?= e($a->weekdayShort()) ?></b>&nbsp;<?= e($a->dateShort()) ?></span></td>
              <td><span class="sched-time"><?= e($a->time()) ?> <span class="badge <?= e($a->badgeClass()) ?>"><?= e($a->statusLabel()) ?></span></span></td>
              <td><span class="patient"><span class="patient__avatar"><?= $paw ?></span><span class="patient__name"><?= e($a->petName) ?> (<?= e($a->species) ?>)</span></span></td>
              <td><?= e($a->clientName) ?></td>
              <td><span class="doctor"><?= e($a->vetName) ?></span></td>
              <td>
<?php if ($a->isCancellable()): ?>
                <button class="btn btn--outline btn--sm js-cancel" data-id="<?= e((string) $a->id) ?>">Anuluj</button>
<?php else: ?>
                <span class="badge <?= e($a->badgeClass()) ?>"><?= e($a->statusLabel()) ?></span>
<?php endif; ?>
              </td>
            </tr>
<?php endforeach; ?>
          </tbody>
        </table>

        <div class="sched-cards">
<?php foreach ($appointments as $a): ?>
          <article class="sched-card" data-row="<?= e((string) $a->id) ?>">
            <div class="sched-card__top">
              <div class="sched-card__time"><small><?= e($a->weekdayShort()) ?> <?= e($a->dateShort()) ?></small><b><?= e($a->time()) ?></b></div>
              <div class="sched-card__info">
                <div class="sched-card__head">
                  <span class="sched-card__name"><?= e($a->petName) ?> (<?= e($a->species) ?>)</span>
                  <span class="badge <?= e($a->badgeClass()) ?>"><?= e($a->statusLabel()) ?></span>
                </div>
                <div class="sched-card__owner">Właściciel: <?= e($a->clientName) ?></div>
                <span class="sched-card__doc"><?= e($a->vetName) ?></span>
              </div>
            </div>
<?php if ($a->isCancellable()): ?>
            <button class="btn btn--outline js-cancel" data-id="<?= e((string) $a->id) ?>">Anuluj wizytę</button>
<?php endif; ?>
          </article>
<?php endforeach; ?>
        </div>
<?php endif; ?>
      </section>

      <script src="/assets/js/pulpit.js"></script>
