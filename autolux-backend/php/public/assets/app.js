/**
 * JavaScript de apoio às páginas PHP (melhora a experiência, mas os
 * formulários funcionam sem ele porque a validação real é feita no servidor).
 *
 *  - venda.php: mostra informação da peça escolhida, linhas dinâmicas,
 *    total em tempo real e campo extra consoante o método de pagamento.
 *  - encomenda_fornecedor.php: adicionar peças do catálogo, linhas dinâmicas e total.
 */
(function () {
  'use strict';

  const dadosPecas = document.getElementById('dados-pecas');
  const pecas = dadosPecas ? JSON.parse(dadosPecas.textContent) : [];
  const pecaPorId = new Map(pecas.map((p) => [String(p.id), p]));

  const euros = (v) => new Intl.NumberFormat('pt-PT', { style: 'currency', currency: 'EUR' }).format(v || 0);

  // ---------------------------------------------------------------- venda.php
  const formVenda = document.getElementById('form-venda');
  if (formVenda) {
    const contentor = document.getElementById('linhas-pecas');
    const template = document.getElementById('template-linha');
    const resumoItens = document.getElementById('resumo-itens');
    const resumoTotal = document.getElementById('resumo-total');

    function atualizarLinha(linha) {
      const select = linha.querySelector('.select-peca');
      const qtd = linha.querySelector('.input-quantidade');
      const info = linha.querySelector('.info-peca');
      const peca = pecaPorId.get(select.value);
      if (!peca) {
        info.textContent = '';
        return;
      }
      qtd.max = peca.stock;
      if (Number(qtd.value) > peca.stock) qtd.value = peca.stock;
      info.innerHTML =
        `<b>${peca.marca} ${peca.nome}</b> · ${peca.tipo} · ref. <code>${peca.referencia}</code> · ` +
        `${euros(peca.preco)}/un · stock disponível <b>${peca.stock}</b>` +
        (peca.descricao ? `<br>${peca.descricao}` : '');
    }

    function atualizarResumo() {
      const linhas = [...contentor.querySelectorAll('.linha-peca')];
      let total = 0;
      const itens = [];
      linhas.forEach((linha) => {
        const peca = pecaPorId.get(linha.querySelector('.select-peca').value);
        const qtd = Number(linha.querySelector('.input-quantidade').value) || 0;
        if (peca && qtd > 0) {
          total += peca.preco * qtd;
          itens.push(`<li><span>${qtd} × ${peca.nome}</span><span>${euros(peca.preco * qtd)}</span></li>`);
        }
      });
      resumoItens.innerHTML = itens.length ? itens.join('') : '<li class="texto-suave">Nenhuma peça selecionada.</li>';
      resumoTotal.textContent = euros(total);
      contentor.querySelectorAll('.remover-linha').forEach((b) => (b.disabled = linhas.length === 1));
    }

    contentor.addEventListener('input', (ev) => {
      const linha = ev.target.closest('.linha-peca');
      if (linha) atualizarLinha(linha);
      atualizarResumo();
    });
    contentor.addEventListener('click', (ev) => {
      if (ev.target.classList.contains('remover-linha')) {
        ev.target.closest('.linha-peca').remove();
        atualizarResumo();
      }
    });
    document.getElementById('adicionar-linha').addEventListener('click', () => {
      contentor.appendChild(template.content.cloneNode(true));
      atualizarResumo();
    });

    contentor.querySelectorAll('.linha-peca').forEach(atualizarLinha);
    atualizarResumo();

    // Campo extra do método de pagamento (telemóvel MB WAY, dígitos do cartão...)
    const grupoExtra = document.getElementById('grupo-campo-extra');
    const rotuloExtra = document.getElementById('rotulo-campo-extra');
    function atualizarCampoExtra() {
      const escolhido = formVenda.querySelector('input[name="tipo_pagamento"]:checked');
      const etiqueta = escolhido ? escolhido.dataset.campoExtra : '';
      grupoExtra.hidden = !etiqueta;
      rotuloExtra.textContent = etiqueta;
    }
    formVenda.querySelectorAll('input[name="tipo_pagamento"]').forEach((r) => r.addEventListener('change', atualizarCampoExtra));
    atualizarCampoExtra();
  }

  // -------------------------------------------------- encomenda_fornecedor.php
  const formEncomenda = document.getElementById('form-encomenda');
  if (formEncomenda) {
    const contentor = document.getElementById('linhas-encomenda');
    const template = document.getElementById('template-linha-encomenda');
    const resumoTotal = document.getElementById('resumo-total');
    const seletor = document.getElementById('seletor-catalogo');

    function atualizarTotal() {
      let total = 0;
      contentor.querySelectorAll('.linha-encomenda').forEach((l) => {
        const qtd = Number(l.querySelector('.input-quantidade').value) || 0;
        const preco = Number(l.querySelector('.input-preco').value) || 0;
        total += qtd * preco;
      });
      resumoTotal.textContent = euros(total);
      const linhas = contentor.querySelectorAll('.linha-encomenda');
      contentor.querySelectorAll('.remover-linha').forEach((b) => (b.disabled = linhas.length === 1));
    }

    function novaLinha(valores) {
      const fragmento = template.content.cloneNode(true);
      const linha = fragmento.querySelector('.linha-encomenda');
      if (valores) {
        linha.querySelector('[name="referencia[]"]').value = valores.referencia;
        linha.querySelector('[name="descricao[]"]').value = valores.descricao;
        linha.querySelector('[name="quantidade[]"]').value = valores.quantidade;
        linha.querySelector('[name="preco_unitario[]"]').value = valores.preco_unitario;
      }
      // Se a única linha existente estiver vazia, substitui-a em vez de acrescentar
      const existentes = [...contentor.querySelectorAll('.linha-encomenda')];
      const vazia = existentes.length === 1 && !existentes[0].querySelector('[name="referencia[]"]').value;
      if (vazia && valores) existentes[0].remove();
      contentor.appendChild(fragmento);
      atualizarTotal();
    }

    seletor.addEventListener('change', () => {
      const peca = pecaPorId.get(seletor.value);
      if (peca) {
        novaLinha({
          referencia: peca.referencia,
          descricao: peca.descricao,
          quantidade: Math.max(1, peca.stock_minimo * 2 - peca.stock),
          preco_unitario: peca.preco_custo.toFixed(2),
        });
      }
      seletor.value = '';
    });
    document.getElementById('adicionar-linha-vazia').addEventListener('click', () => novaLinha(null));
    contentor.addEventListener('input', atualizarTotal);
    contentor.addEventListener('click', (ev) => {
      if (ev.target.classList.contains('remover-linha')) {
        ev.target.closest('.linha-encomenda').remove();
        atualizarTotal();
      }
    });
    atualizarTotal();
  }
})();
