<?php $isVet = $isVet ?? false; ?>
      <section class="panel">
        <div class="panel__head">
          <h2 class="panel__title">Tydzień <?= e($weekLabel) ?></h2>
          <span class="cal-nav">
            <a class="btn btn--soft btn--sm" href="/calendar?week=<?= e($prevWeek) ?>">← Poprzedni</a>
            <a class="btn btn--soft btn--sm" href="/calendar?week=<?= e($todayWeek) ?>">Dziś</a>
            <a class="btn btn--soft btn--sm" href="/calendar?week=<?= e($nextWeek) ?>">Następny →</a>
          </span>
        </div>
      </section>

<?php require __DIR__ . '/_calendar.php'; ?>
