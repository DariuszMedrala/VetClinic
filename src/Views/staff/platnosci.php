      <section class="panel">
        <div class="panel__head">
          <h2 class="panel__title">Faktury</h2>
          <span class="panel__meta"><?= e((string) count($invoices)) ?> dokumentów</span>
        </div>

<?php if ($invoices === []): ?>
        <p class="panel__empty">Brak faktur.</p>
<?php else: ?>
        <table class="schedule">
          <thead>
            <tr>
              <th>Numer</th>
              <th>Pacjent</th>
              <th>Klient</th>
              <th>Wystawiono</th>
              <th>Status</th>
              <th>Suma</th>
            </tr>
          </thead>
          <tbody>
<?php foreach ($invoices as $inv): ?>
            <tr>
              <td><a class="field__link" href="/platnosci/<?= e((string) $inv->id) ?>"><?= e($inv->number) ?></a></td>
              <td><?= e($inv->petName) ?> (<?= e($inv->species) ?>)</td>
              <td><?= e($inv->clientName) ?></td>
              <td><?= e($inv->issuedAt->format('d.m.Y')) ?></td>
              <td><span class="badge <?= e($inv->statusBadge()) ?>"><?= e($inv->statusLabel()) ?></span></td>
              <td><strong><?= e($inv->totalLabel()) ?></strong></td>
            </tr>
<?php endforeach; ?>
          </tbody>
        </table>

        <div class="sched-cards">
<?php foreach ($invoices as $inv): ?>
          <a class="sched-card" href="/platnosci/<?= e((string) $inv->id) ?>" style="text-decoration:none;">
            <div class="sched-card__top">
              <div class="sched-card__time"><small><?= e($inv->number) ?></small><b><?= e($inv->totalLabel()) ?></b></div>
              <div class="sched-card__info">
                <div class="sched-card__head">
                  <span class="sched-card__name"><?= e($inv->petName) ?> (<?= e($inv->species) ?>)</span>
                  <span class="badge <?= e($inv->statusBadge()) ?>"><?= e($inv->statusLabel()) ?></span>
                </div>
                <div class="sched-card__owner"><?= e($inv->clientName) ?></div>
                <span class="sched-card__doc"><?= e($inv->issuedAt->format('d.m.Y')) ?></span>
              </div>
            </div>
          </a>
<?php endforeach; ?>
        </div>
<?php endif; ?>
      </section>
