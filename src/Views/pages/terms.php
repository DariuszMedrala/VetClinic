<div style="background:#eef1fa;min-height:100vh;">
  <header style="background:#fff;border-bottom:1px solid var(--line);padding:16px 24px;display:flex;align-items:center;justify-content:space-between;">
    <a href="/" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
      <span class="brand-logo" aria-hidden="true">
        <svg class="icon" viewBox="0 0 24 24" fill="currentColor"><circle cx="6" cy="10" r="2"></circle><circle cx="10.5" cy="6" r="2"></circle><circle cx="15.5" cy="6" r="2"></circle><circle cx="19" cy="10.5" r="2"></circle><path d="M12.5 12c-2.2 0-4 1.7-4.7 3.4-.6 1.5-2 2.3-2 3.8 0 1.4 1.2 2.3 2.6 2.1 1.2-.2 2.6-.7 4.1-.7s2.9.5 4.1.7c1.4.2 2.6-.7 2.6-2.1 0-1.5-1.4-2.3-2-3.8C16.5 13.7 14.7 12 12.5 12z"></path></svg>
      </span>
      <span style="font-weight:800;font-size:22px;color:var(--teal-700);">VetClinic</span>
    </a>
    <a class="btn btn--soft btn--sm" href="/register">← Wróć do rejestracji</a>
  </header>

  <main style="max-width:820px;margin:0 auto;padding:40px 20px 64px;">
    <h1 style="font-size:34px;font-weight:800;color:var(--ink-900);margin-bottom:6px;">Regulamin i polityka prywatności</h1>
    <p style="color:var(--ink-500);margin-bottom:28px;">Dokument na potrzeby projektu zaliczeniowego — system VetClinic.</p>

    <section class="panel" style="padding:30px;margin-bottom:22px;">
      <h2 class="panel__title" style="margin-bottom:16px;">Regulamin</h2>
      <ol style="color:var(--ink-700);line-height:1.7;padding-left:20px;display:flex;flex-direction:column;gap:10px;">
        <li>Serwis VetClinic służy do zarządzania pracą kliniki weterynaryjnej: umawiania wizyt, prowadzenia kart pacjentów oraz rozliczeń.</li>
        <li>Konto może założyć klient, lekarz weterynarii lub recepcja. Każde konto jest przypisane do jednej kliniki.</li>
        <li>Użytkownik zobowiązuje się podawać prawdziwe dane oraz chronić swoje hasło dostępu.</li>
        <li>Recepcja i lekarze mają dostęp wyłącznie do danych własnej kliniki.</li>
        <li>Administrator może zawiesić konto naruszające zasady korzystania z serwisu.</li>
      </ol>
    </section>

    <section class="panel" style="padding:30px;">
      <h2 class="panel__title" style="margin-bottom:16px;">Polityka prywatności (RODO)</h2>
      <ol style="color:var(--ink-700);line-height:1.7;padding-left:20px;display:flex;flex-direction:column;gap:10px;">
        <li>Administratorem danych osobowych jest klinika, do której przypisane jest konto.</li>
        <li>Dane przetwarzane są wyłącznie w celu świadczenia usług weterynaryjnych i rozliczeń.</li>
        <li>Hasła przechowujemy w postaci zaszyfrowanej (bcrypt) — nie mamy do nich dostępu.</li>
        <li>Użytkownik ma prawo wglądu, poprawiania oraz usunięcia swoich danych.</li>
        <li>Dane nie są udostępniane podmiotom trzecim poza zakresem wymaganym przepisami prawa.</li>
      </ol>
    </section>
  </main>
</div>
