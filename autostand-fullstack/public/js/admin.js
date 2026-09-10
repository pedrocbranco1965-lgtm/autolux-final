let currentUser = null;
let currentVehicles = [];
let vehiclePage = 1;
let salesPage = 1;
let usersPage = 1;
const TABLE_PAGE_SIZE = 10;

const globalMessage = document.querySelector('#globalMessage');
const vehicleForm = document.querySelector('#vehicleForm');
const saleForm = document.querySelector('#saleForm');
const userForm = document.querySelector('#userForm');

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

function showMessage(text, type = 'success') {
    globalMessage.innerHTML = `<div class="alert alert-${type}">${escapeHtml(text)}</div>`;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function toggleVehicleSpecificFields() {
    const tipo = document.querySelector('#vehicleTipo').value;
    document.querySelector('#doorsGroup').classList.toggle('d-none', tipo !== 'carro');
    document.querySelector('#ccGroup').classList.toggle('d-none', tipo !== 'moto');
}

function resetVehicleForm() {
    vehicleForm.reset();
    document.querySelector('#vehicleId').value = '';
    document.querySelector('#vehicleKm').value = 0;
    document.querySelector('#vehiclePortas').value = 4;
    document.querySelector('#vehicleCc').value = 600;
    document.querySelector('#vehicleFormTitle').textContent = 'Novo veiculo';
    document.querySelector('#cancelEdit').classList.add('d-none');
    toggleVehicleSpecificFields();
}

async function checkLogin() {
    try {
        const data = await apiFetch('/api/auth/me');
        currentUser = data.user;
        document.querySelector('#loggedUser').textContent = `${currentUser.name} (${currentUser.role})`;

        if (currentUser.role !== 'admin') {
            document.querySelectorAll('.admin-only').forEach((element) => element.classList.add('d-none'));
            document.querySelector('#vehicleListColumn').className = 'col-12';
            document.querySelector('#salesListColumn').className = 'col-12';
        }

        return true;
    } catch (error) {
        window.location.href = '/login.html';
        return false;
    }
}

async function loadSaleOptions() {
    const saleSelect = document.querySelector('#saleVehicleId');
    if (!saleSelect || currentUser.role !== 'admin') {
        return;
    }

    const data = await apiFetch('/api/veiculos?excludeEstado=vendido&limit=50&page=1');
    saleSelect.innerHTML = '<option value="">Escolher...</option>' + data.data.map((vehicle) => `
        <option value="${vehicle.id}" data-price="${vehicle.preco}">${escapeHtml(vehicle.nomeCompleto)} - ${formatPrice(vehicle.preco)}</option>
    `).join('');
}

async function loadVehicles(page = vehiclePage) {
    vehiclePage = page;
    const data = await apiFetch(`/api/veiculos?page=${page}&limit=${TABLE_PAGE_SIZE}`);
    currentVehicles = data.data;

    const tbody = document.querySelector('#vehicleTableBody');
    tbody.innerHTML = currentVehicles.map((vehicle) => `
        <tr>
            <td>${vehicle.id}</td>
            <td>${escapeHtml(vehicle.nomeCompleto)}<div class="small text-muted">${escapeHtml(vehicle.infoEspecifica)}</div></td>
            <td class="text-capitalize">${escapeHtml(vehicle.tipo)}</td>
            <td>${formatPrice(vehicle.preco)}</td>
            <td class="text-capitalize">${escapeHtml(vehicle.estado)}</td>
            <td>
                ${currentUser.role === 'admin' ? `
                    <button class="btn btn-sm btn-warning" data-action="edit" data-id="${vehicle.id}">Editar</button>
                    <button class="btn btn-sm btn-danger" data-action="delete" data-id="${vehicle.id}">Apagar</button>
                ` : '<span class="text-muted">Consulta</span>'}
            </td>
        </tr>
    `).join('');

    renderPagination(document.querySelector('#vehiclePagination'), data, loadVehicles);
    await loadSaleOptions();
}

async function loadSales(page = salesPage) {
    salesPage = page;
    const data = await apiFetch(`/api/vendas?page=${page}&limit=${TABLE_PAGE_SIZE}`);
    const tbody = document.querySelector('#salesTableBody');

    tbody.innerHTML = data.data.map((sale) => `
        <tr>
            <td>${sale.id}</td>
            <td>${escapeHtml(sale.veiculo)}</td>
            <td>${escapeHtml(sale.customer_name)}<div class="small text-muted">${escapeHtml(sale.customer_email)}</div></td>
            <td>${formatPrice(sale.sale_price)}</td>
            <td>${escapeHtml(sale.vendedor)}</td>
            <td>${new Date(sale.sold_at).toLocaleString('pt-PT')}</td>
        </tr>
    `).join('');

    renderPagination(document.querySelector('#salesPagination'), data, loadSales);
}

async function loadUsers(page = usersPage) {
    if (currentUser.role !== 'admin') return;

    usersPage = page;
    const data = await apiFetch(`/api/users?page=${page}&limit=${TABLE_PAGE_SIZE}`);
    const tbody = document.querySelector('#usersTableBody');

    tbody.innerHTML = data.data.map((user) => `
        <tr>
            <td>${user.id}</td>
            <td>${escapeHtml(user.name)}</td>
            <td>${escapeHtml(user.email)}</td>
            <td>${escapeHtml(user.role)}</td>
            <td>
                ${user.id === currentUser.id
                    ? '<span class="text-muted">Sessao atual</span>'
                    : `<button class="btn btn-sm btn-danger" data-user-delete="${user.id}">Apagar</button>`}
            </td>
        </tr>
    `).join('');

    renderPagination(document.querySelector('#usersPagination'), data, loadUsers);
}

vehicleForm.addEventListener('submit', async (event) => {
    event.preventDefault();

    try {
        const id = document.querySelector('#vehicleId').value;
        const tipo = document.querySelector('#vehicleTipo').value;

        const body = {
            tipo,
            marca: document.querySelector('#vehicleMarca').value,
            modelo: document.querySelector('#vehicleModelo').value,
            ano: Number(document.querySelector('#vehicleAno').value),
            preco: Number(document.querySelector('#vehiclePreco').value),
            combustivel: document.querySelector('#vehicleCombustivel').value,
            quilometragem: Number(document.querySelector('#vehicleKm').value),
            estado: document.querySelector('#vehicleEstado').value,
            portas: tipo === 'carro' ? Number(document.querySelector('#vehiclePortas').value) : null,
            cilindradas: tipo === 'moto' ? Number(document.querySelector('#vehicleCc').value) : null
        };

        await apiFetch(id ? `/api/veiculos/${id}` : '/api/veiculos', {
            method: id ? 'PUT' : 'POST',
            body: JSON.stringify(body)
        });

        showMessage(id ? 'Veiculo atualizado.' : 'Veiculo criado.');
        resetVehicleForm();
        await loadVehicles(1);
    } catch (error) {
        showMessage(error.message, 'danger');
    }
});

document.querySelector('#vehicleTipo').addEventListener('change', toggleVehicleSpecificFields);
document.querySelector('#cancelEdit').addEventListener('click', resetVehicleForm);
document.querySelector('#logoutButton').addEventListener('click', () => logout());

document.querySelector('#vehicleTableBody').addEventListener('click', async (event) => {
    const button = event.target.closest('button[data-action]');
    if (!button) return;

    const id = Number(button.dataset.id);
    const vehicle = currentVehicles.find((item) => item.id === id);
    if (!vehicle) return;

    if (button.dataset.action === 'edit') {
        document.querySelector('#vehicleId').value = vehicle.id;
        document.querySelector('#vehicleTipo').value = vehicle.tipo;
        document.querySelector('#vehicleMarca').value = vehicle.marca;
        document.querySelector('#vehicleModelo').value = vehicle.modelo;
        document.querySelector('#vehicleAno').value = vehicle.ano;
        document.querySelector('#vehiclePreco').value = vehicle.preco;
        document.querySelector('#vehicleCombustivel').value = vehicle.combustivel;
        document.querySelector('#vehicleKm').value = vehicle.quilometragem;
        document.querySelector('#vehicleEstado').value = vehicle.estado;
        document.querySelector('#vehiclePortas').value = vehicle.portas ?? 4;
        document.querySelector('#vehicleCc').value = vehicle.cilindradas ?? 600;
        document.querySelector('#vehicleFormTitle').textContent = `Editar #${vehicle.id}`;
        document.querySelector('#cancelEdit').classList.remove('d-none');
        toggleVehicleSpecificFields();
        window.scrollTo({ top: 0, behavior: 'smooth' });
        return;
    }

    if (button.dataset.action === 'delete') {
        if (!confirm(`Apagar ${vehicle.nomeCompleto}?`)) return;

        try {
            await apiFetch(`/api/veiculos/${id}`, { method: 'DELETE' });
            showMessage('Veiculo apagado.');
            await loadVehicles(1);
        } catch (error) {
            showMessage(error.message, 'danger');
        }
    }
});

const saleVehicle = document.querySelector('#saleVehicleId');
saleVehicle.addEventListener('change', () => {
    const option = saleVehicle.selectedOptions[0];
    if (option && option.dataset.price) {
        document.querySelector('#salePrice').value = option.dataset.price;
    }
});

saleForm.addEventListener('submit', async (event) => {
    event.preventDefault();

    try {
        const body = {
            vehicleId: Number(document.querySelector('#saleVehicleId').value),
            customerName: document.querySelector('#saleCustomerName').value,
            customerEmail: document.querySelector('#saleCustomerEmail').value,
            salePrice: Number(document.querySelector('#salePrice').value)
        };

        await apiFetch('/api/vendas', {
            method: 'POST',
            body: JSON.stringify(body)
        });

        saleForm.reset();
        showMessage('Venda registada com sucesso.');
        await Promise.all([loadVehicles(1), loadSales(1)]);
    } catch (error) {
        showMessage(error.message, 'danger');
    }
});

userForm.addEventListener('submit', async (event) => {
    event.preventDefault();

    try {
        const body = {
            name: document.querySelector('#userName').value,
            email: document.querySelector('#userEmail').value,
            password: document.querySelector('#userPassword').value,
            role: document.querySelector('#userRole').value
        };

        await apiFetch('/api/users', {
            method: 'POST',
            body: JSON.stringify(body)
        });

        userForm.reset();
        showMessage('Utilizador criado.');
        await loadUsers(1);
    } catch (error) {
        showMessage(error.message, 'danger');
    }
});

document.querySelector('#usersTableBody').addEventListener('click', async (event) => {
    const button = event.target.closest('button[data-user-delete]');
    if (!button) return;

    if (!confirm('Apagar este utilizador?')) return;

    try {
        await apiFetch(`/api/users/${button.dataset.userDelete}`, {
            method: 'DELETE'
        });
        showMessage('Utilizador apagado.');
        await loadUsers(1);
    } catch (error) {
        showMessage(error.message, 'danger');
    }
});

async function init() {
    const loggedIn = await checkLogin();
    if (!loggedIn) return;

    try {
        await Promise.all([
            loadVehicles(1),
            loadSales(1),
            loadUsers(1)
        ]);
    } catch (error) {
        showMessage(error.message, 'danger');
    }

    toggleVehicleSpecificFields();
}

init();
