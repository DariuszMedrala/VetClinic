document.addEventListener('DOMContentLoaded', () => {
  const modal = document.querySelector('.js-confirm-modal');
  if (!modal) {
    return;
  }

  const back = modal.querySelector('.js-confirm-back');
  const confirm = modal.querySelector('.js-confirm-ok');
  let pendingForm = null;

  const close = () => {
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

  back.addEventListener('click', close);
  modal.addEventListener('click', (event) => {
    if (event.target === modal) {
      close();
    }
  });

  confirm.addEventListener('click', () => {
    if (pendingForm) {
      pendingForm.submit();
    }
  });
});
