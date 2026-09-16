import assert from 'node:assert/strict';
import test from 'node:test';
import { createApp } from '../src/app.js';

async function withServer(pool, callback) {
  const server = createApp(pool).listen(0);
  await new Promise((resolve) => server.once('listening', resolve));
  const { port } = server.address();
  try {
    await callback(`http://127.0.0.1:${port}`);
  } finally {
    await new Promise((resolve) => server.close(resolve));
  }
}

test('GET /api/health confirma que o serviço está operacional', async () => {
  const pool = { query: async () => [[{ ok: 1 }]] };

  await withServer(pool, async (baseUrl) => {
    const response = await fetch(`${baseUrl}/api/health`);
    assert.equal(response.status, 200);
    assert.deepEqual(await response.json(), {
      status: 'ok',
      service: 'fornecedores-api',
    });
  });
});

test('GET /api/suppliers associa as peças ao fornecedor correto', async () => {
  let queryNumber = 0;
  const pool = {
    query: async () => {
      queryNumber += 1;
      if (queryNumber === 1) {
        return [[
          { id: 1, name: 'Fornecedor A' },
          { id: 2, name: 'Fornecedor B' },
        ]];
      }
      return [[
        { id: 10, supplierId: 2, name: 'Filtro', unitCost: 8, availableStock: 5 },
      ]];
    },
  };

  await withServer(pool, async (baseUrl) => {
    const response = await fetch(`${baseUrl}/api/suppliers`);
    const suppliers = await response.json();

    assert.equal(response.status, 200);
    assert.equal(suppliers[0].parts.length, 0);
    assert.equal(suppliers[1].parts[0].name, 'Filtro');
  });
});

test('rotas inexistentes devolvem JSON e código 404', async () => {
  await withServer({}, async (baseUrl) => {
    const response = await fetch(`${baseUrl}/api/nao-existe`);
    assert.equal(response.status, 404);
    assert.deepEqual(await response.json(), { error: 'Endpoint não encontrado.' });
  });
});
