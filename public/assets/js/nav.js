document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.getElementById('nav-toggle');
  const sidebar = document.getElementById('sidebar');
  const backdrop = document.getElementById('nav-backdrop');
  if (!toggle || !sidebar || !backdrop) {
    return;
  }

  const open = () => {
    sidebar.classList.add('sidebar--open');
    backdrop.classList.add('nav-backdrop--open');
  };

  const close = () => {
    sidebar.classList.remove('sidebar--open');
    backdrop.classList.remove('nav-backdrop--open');
  };

  toggle.addEventListener('click', () => {
    if (sidebar.classList.contains('sidebar--open')) {
      close();
    } else {
      open();
    }
  });

  backdrop.addEventListener('click', close);

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      close();
    }
  });

  sidebar.querySelectorAll('a, button[type="submit"]').forEach((element) => {
    element.addEventListener('click', close);
  });
});
