<?php $paw = '<svg width="52" height="52" viewBox="0 0 24 24" fill="#117a6d" aria-hidden="true"><circle cx="6" cy="10" r="2"></circle><circle cx="10.5" cy="6" r="2"></circle><circle cx="15.5" cy="6" r="2"></circle><circle cx="19" cy="10.5" r="2"></circle><path d="M12.5 12c-2.2 0-4 1.7-4.7 3.4-.6 1.5-2 2.3-2 3.8 0 1.4 1.2 2.3 2.6 2.1 1.2-.2 2.6-.7 4.1-.7s2.9.5 4.1.7c1.4.2 2.6-.7 2.6-2.1 0-1.5-1.4-2.3-2-3.8C16.5 13.7 14.7 12 12.5 12z"></path></svg>'; ?>
      <section style="margin-bottom:24px;">
        <h1 style="font-size:26px;font-weight:800;color:var(--ink-900);margin-bottom:4px;">Cześć, <?= e($client->firstName) ?>! 👋</h1>
        <p style="color:var(--ink-500);margin-bottom:12px;">Oto przegląd opieki nad Twoimi zwierzętami.</p>
<?php if (($clinicName ?? '') !== ''): ?>
        <span class="chip">
          <svg class="icon icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-5.5-7-11a7 7 0 0 1 14 0c0 5.5-7 11-7 11z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>
          Twoja klinika:&nbsp;<strong style="color:var(--ink-900);"><?= e($clinicName) ?></strong>
        </span>
<?php endif; ?>
      </section>

      <section class="stat-grid">
        <article class="stat-card">
          <span class="stat-card__icon"><?= $paw ?></span>
          <div class="stat-card__body">
            <span class="stat-card__label">Moje zwierzęta</span>
            <span class="stat-card__value stat-card__value--teal"><?= e((string) count($pets)) ?></span>
          </div>
        </article>
        <article class="stat-card">
          <span class="stat-card__icon">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="17" rx="2"></rect><path d="M3 9h18M8 2v4M16 2v4"></path></svg>
          </span>
          <div class="stat-card__body">
            <span class="stat-card__label">Nadchodzące wizyty</span>
            <span class="stat-card__value stat-card__value--blue"><?= e((string) count($appointments)) ?></span>
          </div>
        </article>
        <article class="stat-card">
          <span class="stat-card__icon">
            <svg class="icon" viewBox="0 0 24 24" fill="currentColor"><path d="m12 3 2.5 5.5L20 9l-4 4 1 6-5-3-5 3 1-6-4-4 5.5-.5z"></path></svg>
          </span>
          <div class="stat-card__body">
            <span class="stat-card__label">Punkty lojalnościowe</span>
            <span class="stat-card__value stat-card__value--teal"><?= e((string) $client->loyaltyPoints) ?></span>
          </div>
        </article>
      </section>

      <section class="panel" id="zwierzeta">
        <div class="panel__head">
          <h2 class="panel__title">Moje zwierzęta</h2>
          <span class="panel__meta"><?= e((string) count($pets)) ?></span>
        </div>
<?php if ($pets === []): ?>
        <p class="panel__empty">Nie masz jeszcze zarejestrowanych zwierząt.</p>
<?php else: ?>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:18px;padding:4px 30px 28px;">
<?php foreach ($pets as $pet): ?>
          <a class="pet-link" href="/portal/pets/<?= e((string) $pet->id) ?>" style="border:1px solid var(--line);border-radius:var(--r-lg);overflow:hidden;background:#fff;box-shadow:var(--shadow-sm);display:block;text-decoration:none;color:inherit;">
<?php if ($pet->photoPath !== null): ?>
            <img src="<?= e($pet->photoPath) ?>" alt="<?= e($pet->name) ?>" style="width:100%;height:150px;object-fit:cover;display:block;">
<?php else: ?>
            <div style="height:150px;background:#eaf7f4;display:grid;place-items:center;"><?= $paw ?></div>
<?php endif; ?>
            <div style="padding:14px 16px 16px;">
              <div style="font-weight:800;color:var(--ink-900);font-size:17px;margin-bottom:2px;"><?= e($pet->name) ?></div>
              <div style="color:var(--ink-500);font-size:13px;margin-bottom:12px;"><?= e($pet->speciesName) ?><?= $pet->breed !== null ? ' · ' . e($pet->breed) : '' ?></div>
              <div style="display:flex;flex-wrap:wrap;gap:6px;">
                <span class="badge badge--uptodate"><?= e($pet->ageLabel()) ?></span>
                <span class="badge badge--confirmed"><?= e($pet->sexLabel()) ?></span>
                <span class="badge badge--waiting"><?= e($pet->weightLabel()) ?></span>
              </div>
            </div>
          </a>
<?php endforeach; ?>
        </div>
<?php endif; ?>
      </section>

      <section class="panel" id="wizyty">
        <div class="panel__head">
          <h2 class="panel__title">Nadchodzące wizyty</h2>
          <span class="panel__meta"><?= e((string) count($appointments)) ?></span>
        </div>
<?php if ($appointments === []): ?>
        <p class="panel__empty">Brak zaplanowanych wizyt.</p>
<?php else: ?>
        <table class="schedule schedule--lead">
          <thead><tr><th>Dzień</th><th>Godzina</th><th>Zwierzę</th><th>Lekarz</th><th>Status</th></tr></thead>
          <tbody>
<?php foreach ($appointments as $a): ?>
            <tr>
              <td><span class="sched-time"><b><?= e($a->weekdayShort()) ?></b>&nbsp;<?= e($a->dateShort()) ?></span></td>
              <td><span class="sched-time"><?= e($a->time()) ?></span></td>
              <td><?= e($a->petName) ?> (<?= e($a->species) ?>)</td>
              <td><?= e($a->vetName) ?></td>
              <td><span class="badge <?= e($a->badgeClass()) ?>"><?= e($a->statusLabel()) ?></span></td>
            </tr>
<?php endforeach; ?>
          </tbody>
        </table>
        <div class="sched-cards">
<?php foreach ($appointments as $a): ?>
          <article class="sched-card">
            <div class="sched-card__top">
              <div class="sched-card__time"><small><?= e($a->weekdayShort()) ?> <?= e($a->dateShort()) ?></small><b><?= e($a->time()) ?></b></div>
              <div class="sched-card__info">
                <div class="sched-card__head">
                  <span class="sched-card__name"><?= e($a->petName) ?> (<?= e($a->species) ?>)</span>
                  <span class="badge <?= e($a->badgeClass()) ?>"><?= e($a->statusLabel()) ?></span>
                </div>
                <div class="sched-card__owner"><?= e($a->reason) ?></div>
                <span class="sched-card__doc"><?= e($a->vetName) ?></span>
              </div>
            </div>
          </article>
<?php endforeach; ?>
        </div>
<?php endif; ?>
      </section>

      <section class="panel" id="faktury">
        <div class="panel__head">
          <h2 class="panel__title">Moje faktury</h2>
          <span class="panel__meta"><?= e((string) count($invoices)) ?></span>
        </div>
<?php if ($invoices === []): ?>
        <p class="panel__empty">Brak faktur.</p>
<?php else: ?>
        <table class="schedule schedule--lead">
          <thead><tr><th>Numer</th><th>Wystawiono</th><th>Status</th><th>Suma</th></tr></thead>
          <tbody>
<?php foreach ($invoices as $inv): ?>
            <tr>
              <td><?= e($inv->number) ?></td>
              <td><?= e($inv->issuedAt->format('d.m.Y')) ?></td>
              <td><span class="badge <?= e($inv->statusBadge()) ?>"><?= e($inv->statusLabel()) ?></span></td>
              <td><strong><?= e($inv->totalLabel()) ?></strong></td>
            </tr>
<?php endforeach; ?>
          </tbody>
        </table>
        <div class="sched-cards">
<?php foreach ($invoices as $inv): ?>
          <article class="sched-card">
            <div class="sched-card__top">
              <div class="sched-card__time"><small><?= e($inv->number) ?></small><b><?= e($inv->totalLabel()) ?></b></div>
              <div class="sched-card__info">
                <div class="sched-card__head">
                  <span class="sched-card__name"><?= e($inv->issuedAt->format('d.m.Y')) ?></span>
                  <span class="badge <?= e($inv->statusBadge()) ?>"><?= e($inv->statusLabel()) ?></span>
                </div>
              </div>
            </div>
          </article>
<?php endforeach; ?>
        </div>
<?php endif; ?>
      </section>
