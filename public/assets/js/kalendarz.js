document.addEventListener('DOMContentLoaded', () => {
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
      submit.disabled = false;
      showResult('Błąd połączenia. Spróbuj ponownie.', false);
    }
  });
});
