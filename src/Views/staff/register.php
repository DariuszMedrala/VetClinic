<?php
use App\Core\Csrf;

$isVet = false;
$selectedVet = (int) ($selectedVet ?? 0);
?>
      <section style="margin-bottom:24px;">
        <h1 style="font-size:26px;font-weight:800;color:var(--ink-900);margin-bottom:4px;">Zarejestruj wizytę</h1>
        <p style="color:var(--ink-500);">Wybierz lekarza, aby zobaczyć jego dostępność i zajęte terminy, a następnie umów wizytę dla klienta.</p>
      </section>

      <section class="panel">
        <div class="panel__head"><h2 class="panel__title">Nowa wizyta</h2></div>

        <form id="new-appointment-form" style="padding:0 30px 30px;">
          <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">

          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="vet_id">Lekarz</label></div>
              <div class="input-wrap">
                <select class="input js-vet-nav" id="vet_id" name="vet_id" data-week="<?= e($currentWeek) ?>" required>
                  <option value="">— wybierz —</option>
<?php foreach ($vets as $vet): ?>
                  <option value="<?= e((string) $vet['id']) ?>"<?= (int) $vet['id'] === $selectedVet ? ' selected' : '' ?>><?= e($vet['name']) ?></option>
<?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="pet_id">Pacjent</label></div>
              <div class="input-wrap">
                <select class="input" id="pet_id" name="pet_id" required>
                  <option value="">— wybierz —</option>
<?php foreach ($pets as $pet): ?>
                  <option value="<?= e((string) $pet['id']) ?>"><?= e($pet['label']) ?></option>
<?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="date">Data</label></div>
              <div class="input-wrap">
                <input class="input" type="date" id="date" name="date" value="<?= e($defaultDate) ?>" required>
              </div>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="time">Godzina</label></div>
              <div class="input-wrap">
                <input class="input" type="time" id="time" name="time" value="09:00" step="900" required>
              </div>
            </div>
          </div>

          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="duration">Czas trwania</label></div>
              <div class="input-wrap">
                <select class="input" id="duration" name="duration">
                  <option value="30">30 minut</option>
                  <option value="45">45 minut</option>
                  <option value="60" selected>60 minut</option>
                  <option value="90">90 minut</option>
                </select>
              </div>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="reason">Powód</label></div>
              <div class="input-wrap">
                <select class="input" id="reason" name="reason" required>
                  <option value="">— wybierz —</option>
<?php foreach ($reasons as $r): ?>
                  <option value="<?= e($r['name']) ?>"><?= e($r['name']) ?></option>
<?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <div class="details__alert js-result" style="display:none;margin-bottom:16px;"></div>

          <button class="btn btn--primary btn--lg" type="submit">Zarejestruj wizytę</button>
        </form>
      </section>

      <section class="panel">
        <div class="panel__head">
          <h2 class="panel__title">Tydzień <?= e($weekLabel) ?></h2>
          <span class="cal-nav">
            <a class="btn btn--soft btn--sm" href="/appointments/new?vet=<?= e((string) $selectedVet) ?>&week=<?= e($prevWeek) ?>">← Poprzedni</a>
            <a class="btn btn--soft btn--sm" href="/appointments/new?vet=<?= e((string) $selectedVet) ?>&week=<?= e($todayWeek) ?>">Dziś</a>
            <a class="btn btn--soft btn--sm" href="/appointments/new?vet=<?= e((string) $selectedVet) ?>&week=<?= e($nextWeek) ?>">Następny →</a>
          </span>
        </div>
<?php if ($selectedVet === 0): ?>
        <p class="panel__empty">Wybierz lekarza powyżej, aby zobaczyć jego dostępność (zacieniowana) i zajęte terminy.</p>
<?php else: ?>
        <p style="color:var(--ink-500);font-size:13px;padding:0 30px 8px;display:flex;align-items:center;gap:8px;"><span style="display:inline-block;width:16px;height:12px;border-radius:3px;background:var(--teal-50);border:1px solid var(--teal-100);"></span> dostępność lekarza · <span style="display:inline-block;width:16px;height:12px;border-radius:3px;background:var(--teal-600);"></span> zajęte terminy</p>
<?php endif; ?>
      </section>

<?php require __DIR__ . '/_calendar.php'; ?>
