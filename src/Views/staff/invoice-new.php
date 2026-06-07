<?php
use App\Core\Csrf;
use App\Models\Invoice;

$when = (new DateTimeImmutable((string) $appointment['starts_at']))->format('d.m.Y H:i');
$apptId = (int) $appointment['appointment_id'];
?>
      <p style="margin-bottom:18px;"><a class="field__link" href="/dashboard">← Wróć do pulpitu</a></p>

      <section style="margin-bottom:24px;">
        <h1 style="font-size:26px;font-weight:800;color:var(--ink-900);margin-bottom:4px;">Wystaw fakturę</h1>
        <p style="color:var(--ink-500);">Dodaj zabiegi wykonane podczas wizyty — suma zostanie wyliczona automatycznie (z rabatem lojalnościowym).</p>
      </section>

      <section class="panel" style="margin-bottom:22px;">
        <div class="panel__head"><h2 class="panel__title">Wizyta</h2></div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:24px;padding:4px 30px 24px;">
          <div><div style="color:var(--ink-400);font-size:12px;font-weight:700;">PACJENT</div><div style="font-weight:700;color:var(--ink-900);"><?= e($appointment['pet_name']) ?> (<?= e($appointment['species']) ?>)</div></div>
          <div><div style="color:var(--ink-400);font-size:12px;font-weight:700;">WŁAŚCICIEL</div><div style="font-weight:700;color:var(--ink-900);"><?= e($appointment['client_name']) ?></div></div>
          <div><div style="color:var(--ink-400);font-size:12px;font-weight:700;">TERMIN</div><div style="font-weight:700;color:var(--ink-900);"><?= e($when) ?></div></div>
          <div><div style="color:var(--ink-400);font-size:12px;font-weight:700;">POWÓD</div><div style="font-weight:700;color:var(--ink-900);"><?= e($appointment['reason']) ?></div></div>
        </div>
      </section>

      <section class="panel">
        <div class="panel__head"><h2 class="panel__title">Zabiegi</h2></div>
<?php if ($error !== null): ?>
        <div class="details__alert" style="margin:0 30px 18px;"><?= e($error) ?></div>
<?php endif; ?>
        <form action="/invoices/new/<?= $apptId ?>" method="post">
          <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
          <table class="schedule">
            <thead><tr><th>Zabieg</th><th>Opis</th><th>Cena jedn.</th><th>Ilość</th></tr></thead>
            <tbody>
<?php foreach ($procedures as $proc): ?>
              <tr>
                <td style="font-weight:600;color:var(--ink-900);"><?= e($proc['name']) ?></td>
                <td style="color:var(--ink-500);font-size:14px;"><?= e($proc['description'] ?? '—') ?></td>
                <td><?= e(Invoice::money((string) $proc['base_price'])) ?></td>
                <td style="text-align:right;padding-right:30px;">
                  <div class="input-wrap" style="display:inline-flex;width:90px;padding:0 12px;">
                    <input class="input" type="number" name="qty[<?= e((string) $proc['id']) ?>]" min="0" max="99" value="0" style="padding:10px 0;text-align:center;">
                  </div>
                </td>
              </tr>
<?php endforeach; ?>
            </tbody>
          </table>
          <div style="padding:18px 30px 0;">
            <div class="field" style="max-width:360px;margin:0;">
              <div class="field__row"><label class="field__label" for="vaccine_type_id">Podana szczepionka (opcjonalnie)</label></div>
              <div class="input-wrap">
                <select class="input" id="vaccine_type_id" name="vaccine_type_id">
                  <option value="">— brak —</option>
<?php foreach ($vaccines as $v): ?>
                  <option value="<?= e((string) $v['id']) ?>"><?= e($v['name']) ?> — <?= e(Invoice::money((string) $v['price'])) ?></option>
<?php endforeach; ?>
                </select>
              </div>
              <p style="color:var(--ink-400);font-size:13px;margin-top:6px;">Jeśli wybierzesz, karta szczepień pacjenta zostanie zaktualizowana (nowa data podania i ważności, podał: Ty).</p>
            </div>
          </div>
          <div style="padding:24px 30px;">
            <button class="btn btn--primary btn--lg" type="submit">Wystaw fakturę</button>
          </div>
        </form>
      </section>
