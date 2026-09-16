/**
 * AutoLux - comportamento das páginas de gestão.
 *
 * Toda a interação assíncrona passa por endpoints JSON do próprio PHP:
 *  - /api/peca.php                -> detalhe de uma peça (Base de Dados 1)
 *  - /api/fornecedor-artigos.php  -> catálogo do fornecedor (proxy da API Node.js)
 */

const formatarMoeda = (valor) =>
  new Intl.NumberFormat('pt-PT', { style: 'currency', currency: 'EUR' }).format(Number(valor) || 0);

async function pedirJson(url) {
  const resposta = await fetch(url, { headers: { Accept: 'application/json' } });
  const dados = await resposta.json().catch(() => ({}));

  if (!resposta.ok) {
    throw new Error(dados.erro || `Pedido falhou (${resposta.status}).`);
  }
  return dados;
}

/* ----------------------------------------------------------
   Formulário de encomenda de cliente
   ---------------------------------------------------------- */
function iniciarFormularioVenda() {
  const seletorPeca = document.querySelector('[data-info-peca]');
  const painel = document.getElementById('info-peca');
  if (!seletorPeca || !painel) {
    return;
  }

  const quantidade = document.getElementById('seletor-quantidade');
  const botaoAdicionar = document.getElementById('adicionar-peca');
  const tabela = document.getElementById('tabela-itens');
  const corpo = document.getElementById('linhas-itens');
  const totalCelula = document.getElementById('total-itens');
  let proximoIndice = 1;

  const mostrarInformacaoLocal = (opcao) => {
    painel.innerHTML = [
      ['Referência', opcao.dataset.referencia],
      ['Designação', opcao.dataset.designacao],
      ['Marca / tipo', `${opcao.dataset.marca} · ${opcao.dataset.tipo}`],
      ['Preço unitário', formatarMoeda(opcao.dataset.preco)],
      ['Stock disponível', `${opcao.dataset.stock} unidades`],
    ]
      .map(([etiqueta, valor]) => `<div class="info-peca__linha"><span>${etiqueta}</span><strong>${valor}</strong></div>`)
      .join('');
  };

  const atualizarInformacao = async () => {
    const opcao = seletorPeca.selectedOptions[0];
    if (!opcao || !opcao.value) {
      painel.textContent = painel.dataset.vazio;
      return;
    }

    mostrarInformacaoLocal(opcao);
    quantidade.max = opcao.dataset.stock;

    try {
      // Confirma os dados no servidor: o stock pode ter mudado entretanto.
      const { dados } = await pedirJson(`/api/peca.php?id=${encodeURIComponent(opcao.value)}`);
      painel.innerHTML = [
        ['Referência', dados.referencia],
        ['Designação', dados.designacao],
        ['Marca / tipo', `${dados.marca} · ${dados.tipo}`],
        ['Preço unitário', formatarMoeda(dados.preco)],
        ['Stock disponível', `${dados.stock} unidades`],
        ['Descrição', dados.descricao || '—'],
      ]
        .map(([etiqueta, valor]) => `<div class="info-peca__linha"><span>${etiqueta}</span><strong>${valor}</strong></div>`)
        .join('');
      opcao.dataset.preco = dados.preco;
      opcao.dataset.stock = dados.stock;
      quantidade.max = dados.stock;
    } catch (erro) {
      painel.insertAdjacentHTML(
        'beforeend',
        `<div class="info-peca__linha texto-suave"><span>Aviso</span><span>${erro.message}</span></div>`,
      );
    }
  };

  const recalcularTotal = () => {
    const subtotais = [...corpo.querySelectorAll('[data-subtotal]')].map((celula) => Number(celula.dataset.subtotal));
    totalCelula.textContent = formatarMoeda(subtotais.reduce((soma, valor) => soma + valor, 0));
    tabela.classList.toggle('oculto', corpo.children.length === 0);
  };

  const adicionarLinha = () => {
    const opcao = seletorPeca.selectedOptions[0];
    const unidades = Number.parseInt(quantidade.value, 10);

    if (!opcao || !opcao.value) {
      window.alert('Selecione primeiro uma peça.');
      return;
    }
    if (!Number.isInteger(unidades) || unidades < 1) {
      window.alert('Indique uma quantidade válida.');
      return;
    }
    if (unidades > Number(opcao.dataset.stock)) {
      window.alert(`Stock insuficiente: existem apenas ${opcao.dataset.stock} unidades.`);
      return;
    }

    const indice = proximoIndice;
    proximoIndice += 1;
    const subtotal = unidades * Number(opcao.dataset.preco);

    const linha = document.createElement('tr');
    linha.innerHTML = `
      <td>${opcao.dataset.referencia}
        <input type="hidden" name="itens[${indice}][peca_id]" value="${opcao.value}">
        <input type="hidden" name="itens[${indice}][quantidade]" value="${unidades}">
      </td>
      <td>${opcao.dataset.designacao}</td>
      <td class="numerico">${unidades}</td>
      <td class="numerico">${formatarMoeda(opcao.dataset.preco)}</td>
      <td class="numerico" data-subtotal="${subtotal}">${formatarMoeda(subtotal)}</td>
      <td class="numerico">
        <button type="button" class="botao botao--pequeno botao--perigo" data-remover>Remover</button>
      </td>`;

    linha.querySelector('[data-remover]').addEventListener('click', () => {
      linha.remove();
      recalcularTotal();
    });

    corpo.appendChild(linha);
    seletorPeca.value = '';
    quantidade.value = 1;
    painel.textContent = painel.dataset.vazio;
    recalcularTotal();
  };

  seletorPeca.addEventListener('change', atualizarInformacao);
  botaoAdicionar?.addEventListener('click', adicionarLinha);

  if (seletorPeca.value) {
    atualizarInformacao();
  }
}

/* ----------------------------------------------------------
   Campos específicos de cada método de pagamento
   ---------------------------------------------------------- */
function iniciarMetodosPagamento() {
  const seletor = document.getElementById('metodo_pagamento_id');
  const blocos = [...document.querySelectorAll('.bloco-pagamento')];
  if (!seletor || blocos.length === 0) {
    return;
  }

  const mostrarBlocoAtivo = () => {
    blocos.forEach((bloco) => {
      bloco.classList.toggle('oculto', bloco.dataset.metodo !== seletor.value);
    });
  };

  seletor.addEventListener('change', mostrarBlocoAtivo);
  mostrarBlocoAtivo();
}

/* ----------------------------------------------------------
   Encomenda a fornecedor (dados vindos da API Node.js)
   ---------------------------------------------------------- */
function iniciarEncomendaFornecedor() {
  const seletorFornecedor = document.getElementById('fornecedor_id');
  const seletorArtigo = document.getElementById('seletor-artigo');
  if (!seletorFornecedor || !seletorArtigo) {
    return;
  }

  const estado = document.getElementById('estado-artigos');
  const quantidade = document.getElementById('quantidade-artigo');
  const corpo = document.getElementById('linhas-encomenda');
  const tabela = document.getElementById('tabela-encomenda');
  const totalCelula = document.getElementById('total-encomenda');
  const botaoAdicionar = document.getElementById('adicionar-artigo');
  let proximoIndice = 0;

  const recalcularTotal = () => {
    const subtotais = [...corpo.querySelectorAll('[data-subtotal]')].map((celula) => Number(celula.dataset.subtotal));
    totalCelula.textContent = formatarMoeda(subtotais.reduce((soma, valor) => soma + valor, 0));
    tabela.classList.toggle('oculto', corpo.children.length === 0);
  };

  const carregarArtigos = async () => {
    seletorArtigo.innerHTML = '<option value="">— Selecione o artigo —</option>';

    if (!seletorFornecedor.value) {
      estado.textContent = 'Escolha primeiro o fornecedor para carregar o catálogo.';
      return;
    }

    estado.textContent = 'A carregar catálogo do fornecedor a partir da API Node.js...';

    try {
      const { dados } = await pedirJson(`/api/fornecedor-artigos.php?id=${encodeURIComponent(seletorFornecedor.value)}`);

      dados.forEach((artigo) => {
        const opcao = document.createElement('option');
        opcao.value = artigo.referencia;
        opcao.textContent = `${artigo.referencia} — ${artigo.designacao} (${formatarMoeda(artigo.precoCusto)})`;
        opcao.dataset.designacao = artigo.designacao;
        opcao.dataset.preco = artigo.precoCusto;
        seletorArtigo.appendChild(opcao);
      });

      estado.textContent = `${dados.length} artigo(s) disponíveis neste fornecedor.`;
    } catch (erro) {
      estado.textContent = `Não foi possível obter o catálogo: ${erro.message}`;
    }
  };

  const adicionarArtigo = () => {
    const opcao = seletorArtigo.selectedOptions[0];
    const unidades = Number.parseInt(quantidade.value, 10);

    if (!opcao || !opcao.value) {
      window.alert('Selecione um artigo do fornecedor.');
      return;
    }
    if (!Number.isInteger(unidades) || unidades < 1) {
      window.alert('Indique uma quantidade válida.');
      return;
    }

    const indice = proximoIndice;
    proximoIndice += 1;
    const subtotal = unidades * Number(opcao.dataset.preco);

    const linha = document.createElement('tr');
    linha.innerHTML = `
      <td>${opcao.value}
        <input type="hidden" name="itens[${indice}][referencia]" value="${opcao.value}">
        <input type="hidden" name="itens[${indice}][designacao]" value="${opcao.dataset.designacao}">
        <input type="hidden" name="itens[${indice}][quantidade]" value="${unidades}">
        <input type="hidden" name="itens[${indice}][preco_unitario]" value="${opcao.dataset.preco}">
      </td>
      <td>${opcao.dataset.designacao}</td>
      <td class="numerico">${unidades}</td>
      <td class="numerico">${formatarMoeda(opcao.dataset.preco)}</td>
      <td class="numerico" data-subtotal="${subtotal}">${formatarMoeda(subtotal)}</td>
      <td class="numerico">
        <button type="button" class="botao botao--pequeno botao--perigo" data-remover>Remover</button>
      </td>`;

    linha.querySelector('[data-remover]').addEventListener('click', () => {
      linha.remove();
      recalcularTotal();
    });

    corpo.appendChild(linha);
    quantidade.value = 1;
    recalcularTotal();
  };

  seletorFornecedor.addEventListener('change', carregarArtigos);
  botaoAdicionar?.addEventListener('click', adicionarArtigo);

  if (seletorFornecedor.value) {
    carregarArtigos();
  }
}

document.addEventListener('DOMContentLoaded', () => {
  iniciarFormularioVenda();
  iniciarMetodosPagamento();
  iniciarEncomendaFornecedor();
});
