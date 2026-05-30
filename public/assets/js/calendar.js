document.addEventListener('DOMContentLoaded', () => {
  const vetNav = document.querySelector('.js-vet-nav');
  if (vetNav) {
    vetNav.addEventListener('change', () => {
      const week = vetNav.getAttribute('data-week') || '';
      window.location = '/appointments/new?vet=' + encodeURIComponent(vetNav.value) + '&week=' + encodeURIComponent(week);
    });
  }

  const layout = document.getElementById('cal-layout');
  const panel = document.getElementById('visit-panel');

  if (layout && panel) {
    const set = (id, value, fallback) => {
      const el = document.getElementById(id);
      if (el) {
        el.textContent = value && value.length ? value : fallback;
      }
    };

    const openVisit = (el) => {
      const d = el.dataset;
      set('v-pet', d.pet, '—');
      set('v-meta', [d.species, d.breed].filter(Boolean).join(' · '), '—');
      set('v-owner', d.owner, '—');
      set('v-phone', d.phone, '—');
      set('v-when', d.date + ', ' + d.time, '—');
      set('v-vet', [d.vet, d.room].filter(Boolean).join(' · '), '—');
      set('v-status', d.status, '—');
      set('v-reason', d.reason, '—');
      set('v-notes', d.notes, 'Brak notatek.');
      layout.classList.add('cal-layout--open');
    };

    const closeVisit = () => layout.classList.remove('cal-layout--open');

    document.addEventListener('click', (event) => {
      const item = event.target.closest('.event[data-pet], .sched-card[data-pet]');
      if (item) {
        openVisit(item);
      }
    });

    document.getElementById('visit-close').addEventListener('click', closeVisit);
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        closeVisit();
      }
    });
  }

  const form = document.getElementById('new-appointment-form');
  if (!form) {
    return;
  }

  const result = form.querySelector('.js-result');
  const submit = form.querySelector('button[type="submit"]');

  const showResult = (message, ok) => {
    result.textContent = message;
    result.style.display = 'flex';
    result.style.color = ok ? 'var(--teal-700)' : 'var(--danger-600)';
  };

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const payload = Object.fromEntries(new FormData(form).entries());
    submit.disabled = true;

    try {
      const response = await fetch('/appointments', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        body: JSON.stringify(payload),
      });

      const data = await response.json();

      if (response.ok && data.ok) {
        showResult(data.message, true);
        window.setTimeout(() => window.location.reload(), 700);
        return;
      }

      submit.disabled = false;
      showResult(data.message || 'Nie udało się dodać wizyty.', false);
    } catch (error) {
      showResult('Błąd połączenia. Spróbuj ponownie.', false);
      submit.disabled = false;
    }
  });
});
