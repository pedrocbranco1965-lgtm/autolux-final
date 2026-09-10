const vehicleList = document.querySelector('#vehicleList');
const filterForm = document.querySelector('#filterForm');
const total = document.querySelector('#total');
const message = document.querySelector('#message');
const paginationNav = document.querySelector('#paginationNav');

let currentPage = 1;
const PAGE_SIZE = 9;

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function formatPrice(value) {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(Number(value));
}

function statusClass(status) {
    if (status === 'disponivel') return 'text-bg-success';
    if (status === 'reservado') return 'text-bg-warning';
    return 'text-bg-secondary';
}

function renderVehicles(vehicles) {
    vehicleList.innerHTML = '';

    if (vehicles.length === 0) {
        vehicleList.innerHTML = '<div class="col-12"><div class="alert alert-info">Nenhum veiculo encontrado.</div></div>';
        return;
    }

    for (const vehicle of vehicles) {
        vehicleList.insertAdjacentHTML('beforeend', `
            <div class="col-md-6 col-lg-4">
                <article class="card vehicle-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge text-bg-dark text-capitalize">${escapeHtml(vehicle.tipo)}</span>
                            <span class="badge badge-status ${statusClass(vehicle.estado)}">${escapeHtml(vehicle.estado)}</span>
                        </div>
                        <h3 class="h5">${escapeHtml(vehicle.nomeCompleto)}</h3>
                        <p class="text-muted mb-2">${vehicle.ano} - ${escapeHtml(vehicle.combustivel)} - ${vehicle.quilometragem} km</p>
                        <p class="mb-3">${escapeHtml(vehicle.infoEspecifica)}</p>
                        <div class="price">${formatPrice(vehicle.preco)}</div>
                    </div>
                </article>
            </div>
        `);
    }
}

function buildFilterParams(page) {
    const params = new URLSearchParams();
    const q = document.querySelector('#q').value.trim();
    const tipo = document.querySelector('#tipo').value;
    const combustivel = document.querySelector('#combustivel').value;
    const estado = document.querySelector('#estado').value;

    if (q) params.set('q', q);
    if (tipo) params.set('tipo', tipo);
    if (combustivel) params.set('combustivel', combustivel);
    if (estado) params.set('estado', estado);

    params.set('page', String(page));
    params.set('limit', String(PAGE_SIZE));

    return params;
}

async function loadVehicles(page = currentPage) {
    try {
        message.innerHTML = '';
        currentPage = page;

        const data = await apiFetch(`/api/veiculos?${buildFilterParams(page).toString()}`);
        const from = data.total === 0 ? 0 : ((data.page - 1) * data.limit) + 1;
        const to = Math.min(data.page * data.limit, data.total);

        total.textContent = data.total === 0
            ? '0 resultados'
            : `${from}–${to} de ${data.total} resultado(s)`;

        renderVehicles(data.data);
        renderPagination(paginationNav, data, loadVehicles);
    } catch (error) {
        message.innerHTML = `<div class="alert alert-danger">${escapeHtml(error.message)}</div>`;
    }
}

filterForm.addEventListener('submit', (event) => {
    event.preventDefault();
    loadVehicles(1);
});

loadVehicles(1);
