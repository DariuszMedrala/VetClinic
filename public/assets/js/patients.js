document.addEventListener('DOMContentLoaded', () => {
  const headers = {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json',
  };

  const csrf = () => {
    const container = document.querySelector('[data-csrf]');
    return container ? container.getAttribute('data-csrf') : '';
  };

  const showResult = (form, message, ok) => {
    const result = form.querySelector('.js-result');
    if (!result) {
      return;
    }
    result.textContent = message;
    result.style.display = 'flex';
    result.style.color = ok ? 'var(--teal-700)' : 'var(--danger-600)';
  };

  const submitForm = (form, url) => {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      const submit = form.querySelector('button[type="submit"]');
      submit.disabled = true;

      try {
        const response = await fetch(url, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
          body: new FormData(form),
        });
        const data = await response.json();

        if (response.ok && data.ok) {
          showResult(form, data.message, true);
          window.setTimeout(() => window.location.reload(), 700);
          return;
        }

        submit.disabled = false;
        showResult(form, data.message || 'Wystąpił błąd.', false);
      } catch (error) {
        submit.disabled = false;
        showResult(form, 'Błąd połączenia. Spróbuj ponownie.', false);
      }
    });
  };

  const setupDeleteModal = ({ modalId, backId, confirmId, trigger, url, onSuccess }) => {
    const modal = document.getElementById(modalId);
    if (!modal) {
      return;
    }
    const confirmButton = document.getElementById(confirmId);
    let pendingId = null;

    const close = () => {
      modal.classList.remove('modal-backdrop--open');
      pendingId = null;
    };

    document.querySelectorAll(trigger).forEach((button) => {
      button.addEventListener('click', () => {
        pendingId = button.getAttribute('data-id');
        confirmButton.disabled = false;
        modal.classList.add('modal-backdrop--open');
      });
    });

    document.getElementById(backId).addEventListener('click', close);
    modal.addEventListener('click', (event) => {
      if (event.target === modal) {
        close();
      }
    });

    confirmButton.addEventListener('click', async () => {
      if (pendingId === null) {
        return;
      }
      confirmButton.disabled = true;

      try {
        const response = await fetch(url(pendingId), {
          method: 'POST',
          headers,
          body: JSON.stringify({ _csrf: csrf() }),
        });
        const data = await response.json();

        if (response.ok && data.ok) {
          onSuccess();
          return;
        }

        close();
        window.alert(data.message || 'Nie udało się usunąć.');
      } catch (error) {
        close();
        window.alert('Błąd połączenia. Spróbuj ponownie.');
      }
    });
  };

  const addForm = document.getElementById('add-pet-form');
  if (addForm) {
    submitForm(addForm, '/patients');
  }

  const editForm = document.getElementById('edit-pet-form');
  if (editForm) {
    submitForm(editForm, `/patients/${editForm.getAttribute('data-id')}/update`);
  }

  const toggle = document.getElementById('toggle-edit');
  const editPanel = document.getElementById('edit-panel');
  if (toggle && editPanel) {
    toggle.addEventListener('click', () => {
      editPanel.style.display = editPanel.style.display === 'none' ? 'block' : 'none';
    });
  }

  setupDeleteModal({
    modalId: 'vet-del-modal',
    backId: 'vet-del-back',
    confirmId: 'vet-del-confirm',
    trigger: '.js-del-vet',
    url: (id) => `/patients/vets/${id}/delete`,
    onSuccess: () => window.location.reload(),
  });

  setupDeleteModal({
    modalId: 'client-del-modal',
    backId: 'client-del-back',
    confirmId: 'client-del-confirm',
    trigger: '.js-del-client',
    url: (id) => `/patients/clients/${id}/delete`,
    onSuccess: () => window.location.reload(),
  });

  setupDeleteModal({
    modalId: 'pet-del-modal',
    backId: 'pet-del-back',
    confirmId: 'pet-del-confirm',
    trigger: '.js-delete-pet',
    url: (id) => `/patients/${id}/delete`,
    onSuccess: () => { window.location = '/patients'; },
  });
});
