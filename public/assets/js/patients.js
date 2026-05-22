document.addEventListener('DOMContentLoaded', () => {
  const headers = {
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json',
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

  const deleteButton = document.querySelector('.js-delete-pet');
  if (deleteButton) {
    deleteButton.addEventListener('click', async () => {
      if (!window.confirm('Usunąć zwierzę wraz z jego wizytami i szczepieniami?')) {
        return;
      }

      const container = document.querySelector('[data-csrf]');
      const csrf = container ? container.getAttribute('data-csrf') : '';
      deleteButton.disabled = true;

      try {
        const response = await fetch(`/patients/${deleteButton.getAttribute('data-id')}/delete`, {
          method: 'POST',
          headers,
          body: JSON.stringify({ _csrf: csrf }),
        });
        const data = await response.json();

        if (response.ok && data.ok) {
          window.location = '/patients';
          return;
        }

        deleteButton.disabled = false;
        window.alert(data.message || 'Nie udało się usunąć.');
      } catch (error) {
        deleteButton.disabled = false;
        window.alert('Błąd połączenia. Spróbuj ponownie.');
      }
    });
  }
});
