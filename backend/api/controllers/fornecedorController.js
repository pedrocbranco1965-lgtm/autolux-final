import * as fornecedorService from '../services/fornecedorService.js';
import { positiveInteger } from '../utils/validators.js';

export async function index(req, res) {
  const fornecedores = await fornecedorService.listarFornecedores({
    pesquisa: req.query.q?.trim() || null,
    pais: req.query.pais?.trim() || null,
    apenasAtivos: req.query.ativos === 'true',
  });

  res.json({ total: fornecedores.length, dados: fornecedores });
}

export async function show(req, res) {
  const id = positiveInteger(req.params.id, 'id');
  res.json({ dados: await fornecedorService.obterFornecedorComArtigos(id) });
}

export async function artigos(req, res) {
  const id = positiveInteger(req.params.id, 'id');
  const lista = await fornecedorService.listarArtigos(id);
  res.json({ total: lista.length, dados: lista });
}

export async function paises(_req, res) {
  res.json({ dados: await fornecedorService.listarPaises() });
}
