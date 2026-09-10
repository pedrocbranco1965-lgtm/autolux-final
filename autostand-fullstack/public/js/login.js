const form = document.querySelector('#loginForm');
const message = document.querySelector('#message');

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

form.addEventListener('submit', async (event) => {
    event.preventDefault();

    try {
        message.innerHTML = '';

        const email = document.querySelector('#email').value.trim();
        const password = document.querySelector('#password').value;

        await apiFetch('/api/auth/login', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });

        window.location.href = '/admin.html';
    } catch (error) {
        message.innerHTML = `<div class="alert alert-danger">${escapeHtml(error.message)}</div>`;
    }
});
