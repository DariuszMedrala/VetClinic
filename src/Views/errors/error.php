<main class="auth" style="grid-template-columns:1fr;place-items:center;min-height:100vh;">
  <section class="auth__panel" style="max-width:480px;text-align:center;">
    <div class="auth__mobile-head" style="justify-content:center;">
      <span class="brand-logo" aria-hidden="true">
        <svg class="icon" viewBox="0 0 24 24" fill="currentColor"><circle cx="6" cy="10" r="2"></circle><circle cx="10.5" cy="6" r="2"></circle><circle cx="15.5" cy="6" r="2"></circle><circle cx="19" cy="10.5" r="2"></circle><path d="M12.5 12c-2.2 0-4 1.7-4.7 3.4-.6 1.5-2 2.3-2 3.8 0 1.4 1.2 2.3 2.6 2.1 1.2-.2 2.6-.7 4.1-.7s2.9.5 4.1.7c1.4.2 2.6-.7 2.6-2.1 0-1.5-1.4-2.3-2-3.8C16.5 13.7 14.7 12 12.5 12z"></path></svg>
      </span>
      <span class="auth__brand-name">VetClinic</span>
    </div>
    <div style="font-size:64px;font-weight:800;line-height:1;color:var(--teal-700);margin-bottom:6px;"><?= e((string) $code) ?></div>
    <h2 class="auth__title"><?= e($heading) ?></h2>
    <p class="auth__subtitle"><?= e($message) ?></p>
    <a class="btn btn--primary btn--block btn--lg" href="/">Wróć na stronę główną</a>
  </section>
</main>
