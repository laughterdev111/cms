(() => {
  const key = 'rwebeya-theme';
  const root = document.documentElement;
  const saved = localStorage.getItem(key) || 'light';
  if (saved === 'dark') root.dataset.theme = 'dark';
  const toggle = document.getElementById('themeToggle');
  const updateThemeButton = () => {
    if (!toggle) return;
    const dark = root.dataset.theme === 'dark';
    toggle.setAttribute('aria-label', dark ? 'Switch to light theme' : 'Switch to dark theme');
    const label = toggle.querySelector('.theme-label');
    const icon = toggle.querySelector('.theme-icon');
    if (label) label.textContent = dark ? 'Light mode' : 'Dark mode';
    if (icon) icon.textContent = dark ? '☀' : '◐';
  };
  updateThemeButton();
  const applyTheme = mode => {
    if (mode === 'dark') root.dataset.theme = 'dark'; else delete root.dataset.theme;
    localStorage.setItem(key, mode); updateThemeButton();
  };
  document.querySelectorAll('[data-set-theme]').forEach(button => button.addEventListener('click', () => applyTheme(button.dataset.setTheme)));
  if (toggle) toggle.addEventListener('click', () => {
    const dark = root.dataset.theme !== 'dark';
    if (dark) root.dataset.theme = 'dark'; else delete root.dataset.theme;
    localStorage.setItem(key, dark ? 'dark' : 'light');
    updateThemeButton();
  });

  let hasUnsavedChanges = false;
  const dirtyForms = new Set();
  document.querySelectorAll('form[data-track-changes]').forEach(form => {
    form.addEventListener('input', () => { hasUnsavedChanges = true; dirtyForms.add(form); });
    form.addEventListener('change', () => { hasUnsavedChanges = true; dirtyForms.add(form); });
    form.addEventListener('submit', () => { hasUnsavedChanges = false; dirtyForms.clear(); });
  });
  window.addEventListener('beforeunload', event => {
    if (hasUnsavedChanges) { event.preventDefault(); event.returnValue = ''; }
  });
  document.querySelectorAll('[data-logout-form]').forEach(form => {
    form.addEventListener('submit', event => {
      event.preventDefault();
      const message = hasUnsavedChanges
        ? 'You have unsaved changes. If you log out now, those changes may be lost. Do you still want to log out?'
        : 'Are you sure you want to log out of your Rwebeya Clinic account?';
      if (window.confirm(message)) {
        hasUnsavedChanges = false;
        form.submit();
      }
    });
  });

  const search = document.getElementById('tableSearch');
  if (search) search.addEventListener('input', () => {
    const term = search.value.toLowerCase();
    document.querySelectorAll('[data-search-table] tbody tr').forEach(row => {
      row.hidden = !row.innerText.toLowerCase().includes(term);
    });
  });
})();
