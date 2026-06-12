<?php
use App\Core\Csrf;
use App\Models\Invoice;
?>
      <p style="margin-bottom:18px;"><a class="field__link" href="/invoices">← Wróć do faktur</a></p>

      <div class="billing-head">
        <h1 class="billing-head__title">Faktura <?= e($invoice->number) ?></h1>
        <div class="billing-head__row">
          <span class="chip"><?= e($invoice->petName) ?> (<?= e($invoice->species) ?>)</span>
          <span class="dot-sep">•</span>
          <span class="billing-head__owner">Właściciel: <?= e($invoice->clientName) ?></span>
<?php if ($invoice->hasDiscount()): ?>
          <span class="badge badge--loyalty">Klient lojalnościowy</span>
<?php endif; ?>
          <span style="flex:1"></span>
          <span class="badge <?= e($invoice->statusBadge()) ?>"><?= e($invoice->statusLabel()) ?></span>
        </div>
      </div>

      <div class="billing-layout">
        <section class="panel invoice">
          <div class="panel__head">
            <h2 class="panel__title">Szczegóły faktury</h2>
            <span class="chip" style="background:var(--blue-50);font-size:14px;padding:6px 12px;"><?= e($invoice->number) ?></span>
          </div>
<?php if ($items === []): ?>
          <p class="panel__empty">Brak pozycji na fakturze.</p>
<?php else: ?>
          <div class="table-scroll">
          <table class="invoice__table">
            <thead>
              <tr><th>Usługa / Produkt</th><th>Opis</th><th>Cena</th></tr>
            </thead>
            <tbody>
<?php foreach ($items as $item): ?>
              <tr>
                <td class="invoice__service"><?= e($item['name']) ?><?= (int) $item['quantity'] > 1 ? ' × ' . e((string) $item['quantity']) : '' ?></td>
                <td class="invoice__desc"><?= e($item['description'] ?? '—') ?></td>
                <td><?= e(Invoice::money((string) $item['line_total'])) ?></td>
              </tr>
<?php endforeach; ?>
            </tbody>
          </table>
          </div>
<?php endif; ?>
        </section>

        <aside>
          <section class="panel summary">
            <h2 class="summary__title">Podsumowanie</h2>
            <div class="summary__row summary__row--muted"><span>Suma częściowa</span><span style="font-weight:800;color:var(--ink-900)"><?= e($invoice->subtotalLabel()) ?></span></div>
<?php if ($invoice->hasDiscount()): ?>
            <div class="summary__row summary__row--discount"><span>Rabat lojalnościowy (<?= e((string) $invoice->discountPercent()) ?>%)</span><span><?= e($invoice->discountLabel()) ?></span></div>
<?php endif; ?>
            <div class="summary__total">
              <span class="summary__total-label">Razem</span>
              <span class="summary__total-value"><?= e($invoice->totalLabel()) ?></span>
            </div>
          </section>

<?php if ($invoice->isPending()): ?>
          <section class="panel payment" data-csrf="<?= e(Csrf::token()) ?>">
            <h2 class="payment__title">Metoda płatności</h2>
            <form id="pay-form" data-id="<?= e((string) $invoice->id) ?>">
              <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
              <label class="pay-option pay-option--active">
                <input type="radio" name="payment_method" value="card" checked style="display:none">
                <span class="pay-option__radio"></span>
                Karta
              </label>
              <label class="pay-option">
                <input type="radio" name="payment_method" value="cash" style="display:none">
                <span class="pay-option__radio"></span>
                Gotówka
              </label>
              <label class="pay-option">
                <input type="radio" name="payment_method" value="insurance" style="display:none">
                <span class="pay-option__radio"></span>
                Ubezpieczenie
              </label>

              <div class="details__alert js-result" style="display:none;margin:16px 0;"></div>

              <button class="btn btn--primary btn--block btn--lg" type="submit">Przetwórz płatność</button>
            </form>
          </section>
<?php else: ?>
          <section class="panel payment">
            <h2 class="payment__title">Płatność</h2>
            <div class="summary__row"><span>Metoda</span><span style="font-weight:700;color:var(--ink-900)"><?= e($invoice->paymentLabel()) ?></span></div>
            <div class="summary__row"><span>Opłacono</span><span style="font-weight:700;color:var(--ink-900)"><?= e($invoice->paidAt?->format('d.m.Y H:i') ?? '—') ?></span></div>
          </section>
<?php endif; ?>
        </aside>
      </div>

      <script src="/assets/js/payments.js"></script>
