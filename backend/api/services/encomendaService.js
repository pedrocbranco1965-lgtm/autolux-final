import * as encomendaRepository from '../repositories/encomendaRepository.js';
import { garantirFornecedorAtivo } from './fornecedorService.js';
import { HttpError } from '../utils/HttpError.js';
import {
  nonEmptyArray,
  oneOf,
  optionalDate,
  optionalString,
  positiveDecimal,
  positiveInteger,
  requiredString,
} from '../utils/validators.js';

export const ESTADOS = ['submetida', 'confirmada', 'recebida', 'cancelada'];

/** Transições de estado permitidas: uma encomenda recebida ou cancelada é final. */
const TRANSICOES_VALIDAS = {
  submetida: ['confirmada', 'cancelada'],
  confirmada: ['recebida', 'cancelada'],
  recebida: [],
  cancelada: [],
};

export async function listarEncomendas(filtros) {
  return encomendaRepository.listar(filtros);
}

export async function obterEncomenda(id) {
  const encomenda = await encomendaRepository.procurarPorId(id);
  if (!encomenda) {
    throw HttpError.notFound(`Encomenda ${id} não encontrada.`);
  }
  return encomenda;
}

export async function obterResumo() {
  const linhas = await encomendaRepository.resumoPorEstado();
  const resumo = Object.fromEntries(ESTADOS.map((estado) => [estado, { total: 0, valor: 0 }]));

  for (const linha of linhas) {
    resumo[linha.estado] = { total: Number(linha.total), valor: Number(linha.valor) };
  }

  return {
    porEstado: resumo,
    totalEncomendas: Object.values(resumo).reduce((soma, item) => soma + item.total, 0),
    valorEmCurso: resumo.submetida.valor + resumo.confirmada.valor,
  };
}

/**
 * Valida e regista uma nova encomenda ao fornecedor.
 * Os subtotais são sempre recalculados no servidor: o cliente HTTP nunca
 * determina o valor final da encomenda.
 */
export async function criarEncomenda(dados) {
  const fornecedorId = positiveInteger(dados.fornecedorId, 'fornecedorId');
  const dataPrevista = optionalDate(dados.dataPrevista, 'dataPrevista');
  const observacoes = optionalString(dados.observacoes, 'observacoes');
  const linhas = nonEmptyArray(dados.itens, 'itens');

  await garantirFornecedorAtivo(fornecedorId);

  const itens = linhas.map((item, indice) => {
    const posicao = indice + 1;
    const quantidade = positiveInteger(item.quantidade, `itens[${posicao}].quantidade`);
    const precoUnitario = positiveDecimal(item.precoUnitario, `itens[${posicao}].precoUnitario`);

    return {
      referencia: requiredString(item.referencia, `itens[${posicao}].referencia`, { max: 30 }),
      designacao: requiredString(item.designacao, `itens[${posicao}].designacao`, { max: 120 }),
      quantidade,
      precoUnitario,
      subtotal: Math.round(quantidade * precoUnitario * 100) / 100,
    };
  });

  const id = await encomendaRepository.criar({ fornecedorId, dataPrevista, observacoes, itens });
  return obterEncomenda(id);
}

export async function atualizarEstado(id, novoEstado) {
  const estado = oneOf(novoEstado, 'estado', ESTADOS);
  const encomenda = await obterEncomenda(id);

  if (encomenda.estado === estado) {
    return encomenda;
  }
  if (!TRANSICOES_VALIDAS[encomenda.estado].includes(estado)) {
    throw HttpError.conflict(
      `Não é possível passar a encomenda ${encomenda.numero} de "${encomenda.estado}" para "${estado}".`,
    );
  }

  await encomendaRepository.atualizarEstado(id, estado);
  return obterEncomenda(id);
}
