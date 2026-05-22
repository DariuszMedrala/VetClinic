<?php
use App\Core\Csrf;

$names = [1 => 'Poniedziałek', 2 => 'Wtorek', 3 => 'Środa', 4 => 'Czwartek', 5 => 'Piątek', 6 => 'Sobota', 7 => 'Niedziela'];
?>
      <section style="margin-bottom:24px;">
        <h1 style="font-size:26px;font-weight:800;color:var(--ink-900);margin-bottom:4px;">Moja dostępność</h1>
        <p style="color:var(--ink-500);">Zaznacz dni i godziny, w których recepcja może umawiać do Ciebie wizyty.</p>
      </section>

      <section class="panel">
        <div class="panel__head"><h2 class="panel__title">Grafik tygodniowy</h2></div>
<?php if ($message !== null): ?>
        <div class="details__alert" style="margin:0 30px 18px;color:<?= $message['ok'] ? 'var(--teal-700)' : 'var(--danger-600)' ?>;"><?= e($message['message']) ?></div>
<?php endif; ?>
        <form action="/dostepnosc" method="post" style="padding:4px 30px 30px;">
          <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">

<?php foreach ($names as $wd => $label):
    $on = isset($availability[$wd]);
    $start = $availability[$wd]['start'] ?? '09:00';
    $end = $availability[$wd]['end'] ?? '17:00';
?>
          <div style="display:flex;align-items:center;gap:18px;flex-wrap:wrap;padding:14px 0;border-top:1px solid var(--line);">
            <label class="checkbox" style="min-width:190px;margin:0;">
              <input type="checkbox" name="dni[<?= $wd ?>][enabled]" value="1"<?= $on ? ' checked' : '' ?>>
              <span style="font-weight:700;color:var(--ink-900);"><?= e($label) ?></span>
            </label>
            <div style="display:flex;align-items:center;gap:10px;">
              <div class="input-wrap" style="padding:0 12px;"><input class="input" type="time" name="dni[<?= $wd ?>][start]" value="<?= e($start) ?>" step="900" style="padding:10px 0;"></div>
              <span style="color:var(--ink-400);">–</span>
              <div class="input-wrap" style="padding:0 12px;"><input class="input" type="time" name="dni[<?= $wd ?>][end]" value="<?= e($end) ?>" step="900" style="padding:10px 0;"></div>
            </div>
          </div>
<?php endforeach; ?>

          <button class="btn btn--primary btn--lg" type="submit" style="margin-top:24px;">Zapisz grafik</button>
        </form>
      </section>
