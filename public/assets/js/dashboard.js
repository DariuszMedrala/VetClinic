document.addEventListener('DOMContentLoaded', () => {
  const panel = document.querySelector('.panel[data-csrf]');
  if (!panel) {
    return;
  }

  const csrf = panel.getAttribute('data-csrf');

  panel.addEventListener('click', async (event) => {
    const button = event.target.closest('.js-cancel');
    if (!button) {
      return;
    }

    const id = button.getAttribute('data-id');
    if (!window.confirm('Czy na pewno chcesz anulować tę wizytę?')) {
      return;
    }

    button.disabled = true;

    try {
      const response = await fetch(`/appointments/${id}/cancel`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ _csrf: csrf }),
      });

      const data = await response.json();

      if (response.ok && data.ok) {
        document.querySelectorAll(`[data-row="${id}"]`).forEach((node) => node.remove());
      } else {
        button.disabled = false;
        window.alert(data.message || 'Nie udało się anulować wizyty.');
      }
    } catch (error) {
      button.disabled = false;
      window.alert('Błąd połączenia. Spróbuj ponownie.');
    }
  });
});
