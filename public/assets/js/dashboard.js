document.addEventListener('DOMContentLoaded', () => {
  const panel = document.querySelector('.panel[data-csrf]');
  const modal = document.getElementById('cancel-modal');
  if (!panel || !modal) {
    return;
  }

  const csrf = panel.getAttribute('data-csrf');
  const backButton = document.getElementById('cancel-back');
  const confirmButton = document.getElementById('cancel-confirm');
  let currentId = null;

  const openModal = (id) => {
    currentId = id;
    confirmButton.disabled = false;
    modal.classList.add('modal-backdrop--open');
  };

  const closeModal = () => {
    modal.classList.remove('modal-backdrop--open');
    currentId = null;
  };

  panel.addEventListener('click', (event) => {
    const button = event.target.closest('.js-cancel');
    if (!button) {
      return;
    }
    openModal(button.getAttribute('data-id'));
  });

  backButton.addEventListener('click', closeModal);
  modal.addEventListener('click', (event) => {
    if (event.target === modal) {
      closeModal();
    }
  });

  confirmButton.addEventListener('click', async () => {
    if (currentId === null) {
      return;
    }

    const id = currentId;
    confirmButton.disabled = true;

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
        closeModal();
        return;
      }

      closeModal();
      window.alert(data.message || 'Nie udało się anulować wizyty.');
    } catch (error) {
      closeModal();
      window.alert('Błąd połączenia. Spróbuj ponownie.');
    }
  });
});
