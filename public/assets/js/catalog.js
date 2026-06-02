document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('catalog-del-modal');
  if (!modal) {
    return;
  }

  const backButton = document.getElementById('del-back');
  const confirmButton = document.getElementById('del-confirm');
  let pendingForm = null;

  const closeModal = () => {
    modal.classList.remove('modal-backdrop--open');
    pendingForm = null;
  };

  document.querySelectorAll('form.js-del-form').forEach((form) => {
    form.addEventListener('submit', (event) => {
      event.preventDefault();
      pendingForm = form;
      modal.classList.add('modal-backdrop--open');
    });
  });

  backButton.addEventListener('click', closeModal);
  modal.addEventListener('click', (event) => {
    if (event.target === modal) {
      closeModal();
    }
  });

  confirmButton.addEventListener('click', () => {
    if (pendingForm) {
      pendingForm.submit();
    }
  });
});
