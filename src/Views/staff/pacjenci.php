<?php
use App\Core\Csrf;
?>
      <section class="panel" data-csrf="<?= e(Csrf::token()) ?>">
        <div class="panel__head">
          <h2 class="panel__title">Dodaj zwierzę</h2>
        </div>

        <form id="add-pet-form" enctype="multipart/form-data" style="padding:0 30px 30px;">
          <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">

          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="client_id">Właściciel</label></div>
              <div class="input-wrap">
                <select class="input" id="client_id" name="client_id" required>
                  <option value="">— wybierz —</option>
<?php foreach ($groups as $group): $c = $group['client']; ?>
                  <option value="<?= e((string) $c->userId) ?>"><?= e($c->fullName()) ?> (<?= e($c->email) ?>)</option>
<?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="species_id">Gatunek</label></div>
              <div class="input-wrap">
                <select class="input" id="species_id" name="species_id" required>
                  <option value="">— wybierz —</option>
<?php foreach ($species as $s): ?>
                  <option value="<?= e((string) $s['id']) ?>"><?= e($s['name']) ?></option>
<?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="name">Imię</label></div>
              <div class="input-wrap">
                <input class="input" type="text" id="name" name="name" maxlength="100" placeholder="np. Luna" required>
              </div>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="breed">Rasa</label></div>
              <div class="input-wrap">
                <input class="input" type="text" id="breed" name="breed" maxlength="100" placeholder="np. Golden Retriever">
              </div>
            </div>
          </div>

          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="sex">Płeć</label></div>
              <div class="input-wrap">
                <select class="input" id="sex" name="sex">
                  <option value="unknown">Nieznana</option>
                  <option value="male">Samiec</option>
                  <option value="female">Samica</option>
                </select>
              </div>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="birth_date">Data urodzenia</label></div>
              <div class="input-wrap">
                <input class="input" type="date" id="birth_date" name="birth_date">
              </div>
            </div>
          </div>

          <div class="field-2">
            <div class="field">
              <div class="field__row"><label class="field__label" for="weight">Waga (kg)</label></div>
              <div class="input-wrap">
                <input class="input" type="number" id="weight" name="weight" step="0.1" min="0" placeholder="np. 12.5">
              </div>
            </div>
            <div class="field">
              <div class="field__row"><label class="field__label" for="photo">Zdjęcie</label></div>
              <div class="input-wrap">
                <input class="input" type="file" id="photo" name="photo" accept="image/*">
              </div>
            </div>
          </div>

          <div class="details__alert js-result" style="display:none;margin-bottom:16px;"></div>

          <button class="btn btn--primary btn--lg" type="submit">Dodaj zwierzę</button>
        </form>
      </section>

      <section class="panel">
        <div class="panel__head">
          <h2 class="panel__title">Klienci i zwierzęta</h2>
          <span class="panel__meta"><?= e((string) count($groups)) ?> klientów</span>
        </div>

<?php foreach ($groups as $group): $c = $group['client']; ?>
        <div style="padding:18px 30px;border-top:1px solid var(--line);">
          <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:12px;">
            <div>
              <div style="font-weight:800;color:var(--ink-900);"><?= e($c->fullName()) ?></div>
              <div style="color:var(--ink-500);font-size:14px;"><?= e($c->email) ?><?= $c->phone !== null ? ' · ' . e($c->phone) : '' ?></div>
            </div>
            <span class="badge badge--loyalty"><?= e((string) $c->loyaltyPoints) ?> pkt</span>
          </div>
<?php if ($group['pets'] === []): ?>
          <p style="color:var(--ink-400);font-size:14px;">Brak zwierząt.</p>
<?php else: ?>
          <div style="display:flex;flex-wrap:wrap;gap:10px;">
<?php foreach ($group['pets'] as $pet): ?>
            <a class="patient" href="/pacjenci/<?= e((string) $pet->id) ?>" style="text-decoration:none;border:1px solid var(--line-strong);border-radius:var(--r-md);padding:8px 14px;color:var(--ink-700);">
              <span class="patient__name"><?= e($pet->name) ?> · <?= e($pet->speciesName) ?>, <?= e($pet->ageLabel()) ?></span>
            </a>
<?php endforeach; ?>
          </div>
<?php endif; ?>
        </div>
<?php endforeach; ?>
      </section>

      <script src="/assets/js/pacjenci.js"></script>
