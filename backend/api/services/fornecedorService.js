import * as fornecedorRepository from '../repositories/fornecedorRepository.js';
import { HttpError } from '../utils/HttpError.js';

export async function listarFornecedores(filtros) {
  return fornecedorRepository.listar(filtros);
}

export async function obterFornecedor(id) {
  const fornecedor = await fornecedorRepository.procurarPorId(id);
  if (!fornecedor) {
    throw HttpError.notFound(`Fornecedor ${id} não encontrado.`);
  }
  return fornecedor;
}

/**
 * Detalhe do fornecedor com o respetivo catálogo. As duas consultas são
 * independentes, por isso correm em paralelo com Promise.all.
 */
export async function obterFornecedorComArtigos(id) {
  const [fornecedor, artigos] = await Promise.all([
    obterFornecedor(id),
    fornecedorRepository.listarArtigos(id),
  ]);
  return { ...fornecedor, artigos };
}

export async function listarArtigos(id) {
  await obterFornecedor(id);
  return fornecedorRepository.listarArtigos(id);
}

export async function listarPaises() {
  return fornecedorRepository.listarPaises();
}

export async function garantirFornecedorAtivo(id) {
  const fornecedor = await obterFornecedor(id);
  if (!fornecedor.ativo) {
    throw HttpError.conflict(`O fornecedor "${fornecedor.nome}" está inativo e não aceita encomendas.`);
  }
  return fornecedor;
}
