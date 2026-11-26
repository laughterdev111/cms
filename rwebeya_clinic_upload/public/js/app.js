const form = document.getElementById('loginForm');
if (form) {
  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const message = document.getElementById('message');
    message.textContent = 'Checking your account…';
    try {
      const response = await fetch('../api/login.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
          email: document.getElementById('email').value.trim(),
          password: document.getElementById('password').value
        })
      });
      const result = await response.json();
      if (!response.ok || !result.success) throw new Error(result.message || 'Login failed.');
      const routes = {
        Admin: 'admin-dashboard.html',
        Receptionist: 'reception-dashboard.html',
        Doctor: 'doctor-dashboard.html'
      };
      window.location.href = routes[result.role] || 'login.html';
    } catch (error) {
      message.textContent = error.message + ' (The backend must be configured before login can work.)';
    }
  });
}