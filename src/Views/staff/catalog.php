<?php
use App\Core\Csrf;
use App\Models\Invoice;

$banner = static function (?array $msg): string {
    if ($msg === null) {
        return '';
    }
    $color = $msg['ok'] ? 'var(--teal-700)' : 'var(--danger-600)';

    return '<div class="details__alert" style="margin:0 0 18px;color:' . $color . ';">' . e($msg['message']) . '</div>';
};

$delForm = static function (string $type, int $id): string {
    return '<form method="post" action="/catalog/' . e($type) . '/' . $id . '/delete" class="js-del-form" style="display:inline;">'
        . '<input type="hidden" name="_csrf" value="' . e(Csrf::token()) . '">'
        . '<button class="btn btn--outline btn--sm" type="submit">Usuń</button></form>';
};
?>
      <section style="margin-bottom:24px;">
        <h1 style="font-size:26px;font-weight:800;color:var(--ink-900);margin-bottom:4px;">Katalog kliniki</h1>
        <p style="color:var(--ink-500);">Powody wizyt, szczepionki i zabiegi dostępne w Twojej klinice.</p>
      </section>

      <?= $banner($msg) ?>

      <section class="panel" style="margin-bottom:22px;">
        <div class="panel__head"><h2 class="panel__title">Powody wizyt</h2><span class="panel__meta"><?= e((string) count($catalog['reasons'])) ?></span></div>
        <form method="post" action="/catalog/reasons" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;padding:4px 30px 20px;">
          <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
          <div class="field" style="flex:1;min-width:220px;margin:0;">
            <div class="field__row"><label class="field__label" for="r_name">Nazwa powodu</label></div>
            <div class="input-wrap"><input class="input" type="text" id="r_name" name="name" maxlength="150" placeholder="np. Szczepienie" required></div>
          </div>
          <button class="btn btn--primary" type="submit">Dodaj</button>
        </form>
<?php if ($catalog['reasons'] === []): ?>
        <p class="panel__empty">Brak powodów wizyt. Dodaj pierwszy powyżej.</p>
<?php else: ?>
        <table class="schedule schedule--even">
          <thead><tr><th>Nazwa</th><th>Akcja</th></tr></thead>
          <tbody>
<?php foreach ($catalog['reasons'] as $r): ?>
            <tr><td><?= e($r['name']) ?></td><td><?= $delForm('reasons', (int) $r['id']) ?></td></tr>
<?php endforeach; ?>
          </tbody>
        </table>
<?php endif; ?>
      </section>

      <section class="panel" style="margin-bottom:22px;">
        <div class="panel__head"><h2 class="panel__title">Szczepionki</h2><span class="panel__meta"><?= e((string) count($catalog['vaccines'])) ?></span></div>
        <form method="post" action="/catalog/vaccines" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;padding:4px 30px 20px;">
          <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
          <div class="field" style="flex:2;min-width:200px;margin:0;">
            <div class="field__row"><label class="field__label" for="v_name">Nazwa</label></div>
            <div class="input-wrap"><input class="input" type="text" id="v_name" name="name" maxlength="150" placeholder="np. Szczepionka DHPP" required></div>
          </div>
          <div class="field" style="flex:1;min-width:120px;margin:0;">
            <div class="field__row"><label class="field__label" for="v_price">Cena (zł)</label></div>
            <div class="input-wrap"><input class="input" type="number" id="v_price" name="price" step="0.01" min="0" placeholder="90.00" required></div>
          </div>
          <div class="field" style="flex:1;min-width:120px;margin:0;">
            <div class="field__row"><label class="field__label" for="v_months">Ważność (mies.)</label></div>
            <div class="input-wrap"><input class="input" type="number" id="v_months" name="validity_months" min="1" step="1" placeholder="12" required></div>
          </div>
          <button class="btn btn--primary" type="submit">Dodaj</button>
        </form>
<?php if ($catalog['vaccines'] === []): ?>
        <p class="panel__empty">Brak szczepionek. Dodaj pierwszą powyżej.</p>
<?php else: ?>
        <table class="schedule schedule--even">
          <thead><tr><th>Nazwa</th><th>Cena</th><th>Ważność</th><th>Akcja</th></tr></thead>
          <tbody>
<?php foreach ($catalog['vaccines'] as $v): ?>
            <tr>
              <td><?= e($v['name']) ?></td>
              <td><?= e(Invoice::money((string) $v['price'])) ?></td>
              <td><?= e((string) $v['validity_months']) ?> mies.</td>
              <td><?= $delForm('vaccines', (int) $v['id']) ?></td>
            </tr>
<?php endforeach; ?>
          </tbody>
        </table>
<?php endif; ?>
      </section>

      <section class="panel">
        <div class="panel__head"><h2 class="panel__title">Zabiegi</h2><span class="panel__meta"><?= e((string) count($catalog['procedures'])) ?></span></div>
        <form method="post" action="/catalog/treatments" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;padding:4px 30px 20px;">
          <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
          <div class="field" style="flex:2;min-width:200px;margin:0;">
            <div class="field__row"><label class="field__label" for="t_name">Nazwa</label></div>
            <div class="input-wrap"><input class="input" type="text" id="t_name" name="name" maxlength="150" placeholder="np. Czyszczenie zębów" required></div>
          </div>
          <div class="field" style="flex:1;min-width:120px;margin:0;">
            <div class="field__row"><label class="field__label" for="t_price">Cena (zł)</label></div>
            <div class="input-wrap"><input class="input" type="number" id="t_price" name="price" step="0.01" min="0" placeholder="150.00" required></div>
          </div>
          <button class="btn btn--primary" type="submit">Dodaj</button>
        </form>
<?php if ($catalog['procedures'] === []): ?>
        <p class="panel__empty">Brak zabiegów. Dodaj pierwszy powyżej.</p>
<?php else: ?>
        <table class="schedule schedule--even">
          <thead><tr><th>Nazwa</th><th>Cena</th><th>Akcja</th></tr></thead>
          <tbody>
<?php foreach ($catalog['procedures'] as $p): ?>
            <tr>
              <td><?= e($p['name']) ?></td>
              <td><?= e(Invoice::money((string) $p['base_price'])) ?></td>
              <td><?= $delForm('treatments', (int) $p['id']) ?></td>
            </tr>
<?php endforeach; ?>
          </tbody>
        </table>
<?php endif; ?>
      </section>

      <div class="modal-backdrop" id="catalog-del-modal">
        <div class="modal">
          <h3 class="modal__title">Usuń pozycję</h3>
          <p class="modal__text">Czy na pewno chcesz usunąć tę pozycję z katalogu?</p>
          <div class="modal__actions">
            <button class="btn btn--soft" type="button" id="del-back">Wróć</button>
            <button class="btn btn--danger" type="button" id="del-confirm">Usuń</button>
          </div>
        </div>
      </div>

      <script src="<?= e(asset('/assets/js/catalog.js')) ?>"></script>
