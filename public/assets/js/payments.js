document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('pay-form');
  if (!form) {
    return;
  }

  const options = form.querySelectorAll('.pay-option');
  options.forEach((option) => {
    const radio = option.querySelector('input[type="radio"]');
    radio.addEventListener('change', () => {
      options.forEach((other) => other.classList.remove('pay-option--active'));
      option.classList.add('pay-option--active');
    });
  });

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
      const response = await fetch(`/invoices/${form.getAttribute('data-id')}/pay`, {
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
      showResult(data.message || 'Nie udało się przetworzyć płatności.', false);
    } catch (error) {
      submit.disabled = false;
      showResult('Błąd połączenia. Spróbuj ponownie.', false);
    }
  });
});
