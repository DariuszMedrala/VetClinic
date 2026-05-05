<?php
use App\Core\Csrf;

$eventColor = static fn (string $status): string => match ($status) {
    'confirmed' => 'event--teal',
    'in_progress' => 'event--gold',
    default => 'event--blue',
};
?>
      <section class="panel">
        <div class="panel__head">
          <h2 class="panel__title">Tydzień <?= e($weekLabel) ?></h2>
          <span class="cal-nav">
            <a class="btn btn--soft btn--sm" href="/kalendarz?week=<?= e($prevWeek) ?>">← Poprzedni</a>
            <a class="btn btn--soft btn--sm" href="/kalendarz?week=<?= e($todayWeek) ?>">Dziś</a>
            <a class="btn btn--soft btn--sm" href="/kalendarz?week=<?= e($nextWeek) ?>">Następny →</a>
          </span>
        </div>

        <form class="auth__form" id="new-appointment-form" style="max-width:none;box-shadow:none;padding:0;">
          <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
          <h3 class="panel__title" style="margin-bottom:16px;">Nowa wizyta</h3>

          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="pet_id">Pacjent</label></div>
              <select class="input" id="pet_id" name="pet_id" required>
                <option value="">— wybierz —</option>
<?php foreach ($pets as $pet): ?>
                <option value="<?= e((string) $pet['id']) ?>"><?= e($pet['label']) ?></option>
<?php endforeach; ?>
              </select>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="vet_id">Lekarz</label></div>
              <select class="input" id="vet_id" name="vet_id" required>
                <option value="">— wybierz —</option>
<?php foreach ($vets as $vet): ?>
                <option value="<?= e((string) $vet['id']) ?>"><?= e($vet['name']) ?></option>
<?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="date">Data</label></div>
              <input class="input" type="date" id="date" name="date" value="<?= e($defaultDate) ?>" required>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="time">Godzina</label></div>
              <input class="input" type="time" id="time" name="time" value="09:00" step="900" required>
            </div>
          </div>

          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="duration">Czas trwania</label></div>
              <select class="input" id="duration" name="duration">
                <option value="30">30 minut</option>
                <option value="45">45 minut</option>
                <option value="60" selected>60 minut</option>
                <option value="90">90 minut</option>
              </select>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="reason">Powód</label></div>
              <input class="input" type="text" id="reason" name="reason" placeholder="np. Szczepienie" maxlength="255" required>
            </div>
          </div>

          <div class="details__alert js-result" style="display:none;margin-bottom:16px;"></div>

          <button class="btn btn--primary btn--lg" type="submit">Dodaj wizytę</button>
        </form>
      </section>

      <div class="calendar">
        <div class="calendar__head">
          <div class="calendar__day-h"></div>
<?php foreach ($days as $d): ?>
          <div class="calendar__day-h<?= $d['isToday'] ? ' calendar__day-h--today' : '' ?>"><div class="calendar__dow"><?= e($d['dow']) ?></div><div class="calendar__dom"><?= e($d['dom']) ?></div></div>
<?php endforeach; ?>
        </div>

        <div class="calendar__body">
<?php for ($h = 8; $h <= 17; $h++): $row = $h - 7; ?>
          <div class="calendar__hour" style="grid-row:<?= $row ?>"><?= sprintf('%02d:00', $h) ?></div>
<?php endfor; ?>

<?php for ($row = 1; $row <= 10; $row++): ?>
<?php foreach ($days as $i => $d): $col = $i + 2; ?>
          <div class="calendar__cell<?= $d['isToday'] ? ' calendar__col-today' : '' ?>" style="grid-area:<?= $row ?>/<?= $col ?>"></div>
<?php endforeach; ?>
<?php endfor; ?>

<?php foreach ($appointments as $a):
    $col = (int) $a->startsAt->format('N') + 1;
    $startHour = (int) $a->startsAt->format('G');
    $rowStart = $startHour - 7;
    if ($rowStart > 10) { continue; }
    if ($rowStart < 1) { $rowStart = 1; }
    $rowEnd = (int) $a->endsAt->format('G') - 7 + ((int) $a->endsAt->format('i') > 0 ? 1 : 0);
    if ($rowEnd <= $rowStart) { $rowEnd = $rowStart + 1; }
    if ($rowEnd > 11) { $rowEnd = 11; }
?>
          <div class="event <?= e($eventColor($a->status)) ?>" style="grid-column:<?= $col ?>;grid-row:<?= $rowStart ?>/<?= $rowEnd ?>">
            <div class="event__title"><?= e($a->petName) ?> (<?= e($a->species) ?>)</div>
            <div class="event__sub"><?= e($a->time()) ?> · <?= e($a->reason) ?></div>
          </div>
<?php endforeach; ?>
        </div>
      </div>

      <div class="sched-cards">
<?php if ($appointments === []): ?>
        <p class="panel__empty">Brak wizyt w tym tygodniu.</p>
<?php else: ?>
<?php foreach ($appointments as $a): ?>
        <article class="sched-card">
          <div class="sched-card__top">
            <div class="sched-card__time"><small><?= e($a->startsAt->format('d.m')) ?></small><b><?= e($a->time()) ?></b></div>
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
<?php endif; ?>
      </div>

      <script src="/assets/js/kalendarz.js"></script>
