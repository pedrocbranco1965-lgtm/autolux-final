/**
 * Teste rápido da Web API Node.js (a API tem de estar a correr: npm run start:api).
 * Percorre o ciclo completo: health -> fornecedores -> criar encomenda ->
 * consultar -> alterar estado -> validação de erro.
 *
 * Uso: npm run test:api
 */
require('dotenv').config({ path: require('path').join(__dirname, '..', '.env') });

const base = process.env.API_BASE_URL || 'http://localhost:3000/api';
let falhas = 0;

async function pedido(metodo, caminho, corpo) {
  const resposta = await fetch(base + caminho, {
    method: metodo,
    headers: { 'Content-Type': 'application/json' },
    body: corpo ? JSON.stringify(corpo) : undefined,
  });
  return { status: resposta.status, dados: await resposta.json() };
}

function verificar(descricao, condicao, extra = '') {
  console.log(`${condicao ? 'OK  ' : 'FALHA'} ${descricao}${extra ? ' — ' + extra : ''}`);
  if (!condicao) falhas++;
}

(async () => {
  console.log(`A testar ${base}\n`);

  const health = await pedido('GET', '/health');
  verificar('GET /health responde ok', health.status === 200 && health.dados.estado === 'ok');

  const fornecedores = await pedido('GET', '/fornecedores');
  verificar('GET /fornecedores devolve lista', fornecedores.status === 200 && Array.isArray(fornecedores.dados) && fornecedores.dados.length > 0,
    `${fornecedores.dados.length} fornecedores`);

  const fornecedorId = fornecedores.dados[0].id;
  const criada = await pedido('POST', '/encomendas', {
    fornecedor_id: fornecedorId,
    observacoes: 'Encomenda de teste (scripts/test-api.js)',
    itens: [
      { referencia_peca: 'IG-BKR6E', descricao: 'Vela de ignição', quantidade: 100, preco_unitario: 2.9 },
      { referencia_peca: 'FL-C30135', descricao: 'Filtro de ar', quantidade: 20, preco_unitario: 9.1 },
    ],
  });
  verificar('POST /encomendas cria encomenda (201)', criada.status === 201 && criada.dados.id > 0, `id ${criada.dados.id}`);
  verificar('total calculado no servidor', Math.abs(criada.dados.total - (100 * 2.9 + 20 * 9.1)) < 0.001, `total ${criada.dados.total}`);

  const detalhe = await pedido('GET', `/encomendas/${criada.dados.id}`);
  verificar('GET /encomendas/:id devolve itens', detalhe.status === 200 && detalhe.dados.itens.length === 2);

  const estado = await pedido('PATCH', `/encomendas/${criada.dados.id}/estado`, { estado: 'enviada' });
  verificar('PATCH /encomendas/:id/estado altera estado', estado.status === 200 && estado.dados.estado === 'enviada');

  const invalida = await pedido('POST', '/encomendas', { fornecedor_id: fornecedorId, itens: [] });
  verificar('POST /encomendas sem itens devolve 400', invalida.status === 400, invalida.dados.erro);

  const inexistente = await pedido('GET', '/encomendas/999999');
  verificar('GET /encomendas/999999 devolve 404', inexistente.status === 404);

  console.log(`\n${falhas === 0 ? 'Todos os testes passaram.' : falhas + ' teste(s) falharam.'}`);
  process.exit(falhas === 0 ? 0 : 1);
})().catch((erro) => {
  console.error('Não foi possível contactar a API:', erro.message);
  console.error('Arranque-a primeiro com: npm run start:api');
  process.exit(1);
});
