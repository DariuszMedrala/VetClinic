document.addEventListener('DOMContentLoaded', () => {
  const panel = document.querySelector('.panel[data-csrf]');
  if (!panel) {
    return;
  }

  const csrf = panel.getAttribute('data-csrf');

  panel.addEventListener('click', async (event) => {
    const button = event.target.closest('.js-complete');
    if (!button) {
      return;
    }

    if (!window.confirm('Oznaczyć wizytę jako zakończoną?')) {
      return;
    }

    button.disabled = true;

    try {
      const response = await fetch(`/appointments/${button.getAttribute('data-id')}/zakoncz`, {
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
        window.location.reload();
        return;
      }

      button.disabled = false;
      window.alert(data.message || 'Nie udało się zakończyć wizyty.');
    } catch (error) {
      button.disabled = false;
      window.alert('Błąd połączenia. Spróbuj ponownie.');
    }
  });
});
