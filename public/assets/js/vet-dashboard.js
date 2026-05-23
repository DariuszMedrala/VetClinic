document.addEventListener('DOMContentLoaded', () => {
  const panel = document.querySelector('.panel[data-csrf]');
  const modal = document.getElementById('complete-modal');
  const notice = document.getElementById('notice-modal');
  if (!panel || !modal || !notice) {
    return;
  }

  const csrf = panel.getAttribute('data-csrf');
  const notesInput = document.getElementById('complete-notes');
  const cancelButton = document.getElementById('complete-cancel');
  const confirmButton = document.getElementById('complete-confirm');
  const noticeText = document.getElementById('notice-text');
  const noticeOk = document.getElementById('notice-ok');
  let currentId = null;

  const openModal = (id) => {
    currentId = id;
    notesInput.value = '';
    confirmButton.disabled = false;
    modal.classList.add('modal-backdrop--open');
    notesInput.focus();
  };

  const closeModal = () => {
    modal.classList.remove('modal-backdrop--open');
    currentId = null;
  };

  const showNotice = (message) => {
    noticeText.textContent = message;
    notice.classList.add('modal-backdrop--open');
  };

  const closeNotice = () => {
    notice.classList.remove('modal-backdrop--open');
  };

  panel.addEventListener('click', (event) => {
    const button = event.target.closest('.js-complete');
    if (!button) {
      return;
    }
    if (button.getAttribute('data-can-complete') === '0') {
      showNotice('Wizytę można zakończyć dopiero 15 minut po jej rozpoczęciu.');
      return;
    }
    openModal(button.getAttribute('data-id'));
  });

  cancelButton.addEventListener('click', closeModal);
  modal.addEventListener('click', (event) => {
    if (event.target === modal) {
      closeModal();
    }
  });

  noticeOk.addEventListener('click', closeNotice);
  notice.addEventListener('click', (event) => {
    if (event.target === notice) {
      closeNotice();
    }
  });

  confirmButton.addEventListener('click', async () => {
    if (currentId === null) {
      return;
    }

    confirmButton.disabled = true;

    try {
      const response = await fetch(`/appointments/${currentId}/complete`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ _csrf: csrf, notes: notesInput.value }),
      });

      const data = await response.json();

      if (response.ok && data.ok) {
        window.location.reload();
        return;
      }

      closeModal();
      showNotice(data.message || 'Nie udało się zakończyć wizyty.');
    } catch (error) {
      closeModal();
      showNotice('Błąd połączenia. Spróbuj ponownie.');
    }
  });
});
