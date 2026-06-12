<?php
use App\Core\Csrf;

$banner = static function (?array $msg): string {
    if ($msg === null) {
        return '';
    }
    $color = $msg['ok'] ? 'var(--teal-700)' : 'var(--danger-600)';

    return '<div class="details__alert" style="margin:0 0 18px;color:' . $color . ';">' . e($msg['message']) . '</div>';
};

$s = $loyalty['settings'];
$pointsPer = (int) $s['points_per'];
$perAmount = rtrim(rtrim(number_format((float) $s['per_amount'], 2, '.', ''), '0'), '.');
?>
      <section style="margin-bottom:24px;">
        <h1 style="font-size:26px;font-weight:800;color:var(--ink-900);margin-bottom:4px;">Program lojalnościowy</h1>
        <p style="color:var(--ink-500);">Ustaw, jak Twoja klinika nalicza punkty i przyznaje zniżki klientom.</p>
      </section>

      <?= $banner($msg) ?>

      <section class="panel" style="margin-bottom:22px;">
        <div class="panel__head"><h2 class="panel__title">Naliczanie punktów</h2></div>
        <form method="post" action="/loyalty/settings" style="padding:4px 30px 22px;">
          <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
          <p style="color:var(--ink-500);font-size:14px;margin-bottom:14px;">Klient otrzymuje wskazaną liczbę punktów za każde wydane złotówki (liczone od kwoty opłaconej faktury).</p>
          <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div class="field" style="flex:1;min-width:160px;margin:0;">
              <div class="field__row"><label class="field__label" for="points_per">Punkty</label></div>
              <div class="input-wrap"><input class="input" type="number" id="points_per" name="points_per" min="0" step="1" value="<?= e((string) $pointsPer) ?>" required></div>
            </div>
            <div style="padding-bottom:12px;color:var(--ink-500);font-weight:700;">za każde</div>
            <div class="field" style="flex:1;min-width:160px;margin:0;">
              <div class="field__row"><label class="field__label" for="per_amount">Kwota (zł)</label></div>
              <div class="input-wrap"><input class="input" type="number" id="per_amount" name="per_amount" min="0.01" step="0.01" value="<?= e($perAmount) ?>" required></div>
            </div>
            <button class="btn btn--primary" type="submit" style="margin-bottom:0;">Zapisz</button>
          </div>
        </form>
      </section>

      <section class="panel">
        <div class="panel__head"><h2 class="panel__title">Progi zniżek</h2><span class="panel__meta"><?= e((string) count($loyalty['tiers'])) ?></span></div>
        <form method="post" action="/loyalty/tiers" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;padding:4px 30px 20px;">
          <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
          <div class="field" style="flex:1;min-width:160px;margin:0;">
            <div class="field__row"><label class="field__label" for="min_points">Od ilu punktów</label></div>
            <div class="input-wrap"><input class="input" type="number" id="min_points" name="min_points" min="0" step="1" placeholder="100" required></div>
          </div>
          <div class="field" style="flex:1;min-width:160px;margin:0;">
            <div class="field__row"><label class="field__label" for="discount_percent">Zniżka (%)</label></div>
            <div class="input-wrap"><input class="input" type="number" id="discount_percent" name="discount_percent" min="0" max="100" step="0.01" placeholder="10" required></div>
          </div>
          <button class="btn btn--primary" type="submit">Dodaj próg</button>
        </form>
<?php if ($loyalty['tiers'] === []): ?>
        <p class="panel__empty">Brak progów zniżek. Dodaj pierwszy powyżej.</p>
<?php else: ?>
        <div class="table-scroll">
        <table class="schedule schedule--even">
          <thead><tr><th>Od punktów</th><th>Zniżka</th><th>Akcja</th></tr></thead>
          <tbody>
<?php foreach ($loyalty['tiers'] as $t): ?>
            <tr>
              <td><?= e((string) (int) $t['min_points']) ?> pkt</td>
              <td><?= e(rtrim(rtrim(number_format((float) $t['discount_percent'], 2, '.', ''), '0'), '.')) ?>%</td>
              <td>
                <form method="post" action="/loyalty/tiers/<?= e((string) $t['id']) ?>/delete" class="js-del-form" style="display:inline;">
                  <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
                  <button class="btn btn--outline btn--sm" type="submit">Usuń</button>
                </form>
              </td>
            </tr>
<?php endforeach; ?>
          </tbody>
        </table>
        </div>
<?php endif; ?>
      </section>

      <div class="modal-backdrop js-confirm-modal" id="loyalty-del-modal">
        <div class="modal">
          <h3 class="modal__title">Usuń próg</h3>
          <p class="modal__text">Czy na pewno chcesz usunąć ten próg zniżki?</p>
          <div class="modal__actions">
            <button class="btn btn--soft js-confirm-back" type="button">Wróć</button>
            <button class="btn btn--danger js-confirm-ok" type="button">Usuń</button>
          </div>
        </div>
      </div>

      <script src="<?= e(asset('/assets/js/delete-confirm.js')) ?>"></script>
