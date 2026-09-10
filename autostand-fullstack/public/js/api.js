// Funcoes comuns para comunicar com a API REST.
// credentials: 'include' envia o cookie HttpOnly automaticamente.

async function apiFetch(url, options = {}) {
    const headers = {
        ...(options.headers || {})
    };

    if (options.body && !headers['Content-Type']) {
        headers['Content-Type'] = 'application/json';
    }

    const response = await fetch(url, {
        ...options,
        headers,
        credentials: 'include'
    });

    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        throw new Error(data.message || `Erro HTTP ${response.status}`);
    }

    return data;
}

async function logout() {
    try {
        await apiFetch('/api/auth/logout', { method: 'POST' });
    } catch (error) {
        // Mesmo que a API falhe, o utilizador sai da area reservada.
    }

    window.location.href = '/login.html';
}

function renderPagination(container, meta, onPageChange) {
    if (!container) {
        return;
    }

    if (!meta || !meta.totalPages || meta.totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    const previousDisabled = meta.hasPrevPage ? '' : ' disabled';
    const nextDisabled = meta.hasNextPage ? '' : ' disabled';
    const buttons = [];

    for (let page = 1; page <= meta.totalPages; page += 1) {
        const active = page === meta.page ? ' active' : '';
        buttons.push(
            `<li class="page-item${active}">
                <button class="page-link" type="button" data-page="${page}">${page}</button>
            </li>`
        );
    }

    container.innerHTML = `
        <ul class="pagination mb-0">
            <li class="page-item${previousDisabled}">
                <button class="page-link" type="button" data-page="${meta.page - 1}" aria-label="Anterior">Anterior</button>
            </li>
            ${buttons.join('')}
            <li class="page-item${nextDisabled}">
                <button class="page-link" type="button" data-page="${meta.page + 1}" aria-label="Seguinte">Seguinte</button>
            </li>
        </ul>
    `;

    container.querySelectorAll('button[data-page]').forEach((button) => {
        button.addEventListener('click', () => {
            const page = Number(button.dataset.page);
            if (page >= 1 && page <= meta.totalPages && page !== meta.page) {
                onPageChange(page);
            }
        });
    });
}
