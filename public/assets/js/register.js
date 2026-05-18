document.addEventListener('DOMContentLoaded', () => {
  const options = document.getElementById('rola-options');
  const pick = document.getElementById('clinic-pick');
  const create = document.getElementById('clinic-new');
  if (!options || !pick || !create) {
    return;
  }

  const apply = (role) => {
    const isReception = role === 'recepcja';
    create.style.display = isReception ? '' : 'none';
    pick.style.display = isReception ? 'none' : '';
  };

  options.addEventListener('change', (event) => {
    if (event.target.name === 'rola') {
      apply(event.target.value);
    }
  });

  const checked = options.querySelector('input[name="rola"]:checked');
  if (checked) {
    apply(checked.value);
  }
});
