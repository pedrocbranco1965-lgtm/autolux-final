import * as encomendaService from '../services/encomendaService.js';
import { positiveInteger } from '../utils/validators.js';

export async function index(req, res) {
  const encomendas = await encomendaService.listarEncomendas({
    fornecedorId: req.query.fornecedorId ? positiveInteger(req.query.fornecedorId, 'fornecedorId') : null,
    estado: req.query.estado?.trim() || null,
  });

  res.json({ total: encomendas.length, dados: encomendas });
}

export async function show(req, res) {
  const id = positiveInteger(req.params.id, 'id');
  res.json({ dados: await encomendaService.obterEncomenda(id) });
}

export async function store(req, res) {
  const encomenda = await encomendaService.criarEncomenda(req.body ?? {});
  res
    .status(201)
    .location(`/api/encomendas/${encomenda.id}`)
    .json({ mensagem: `Encomenda ${encomenda.numero} submetida com sucesso.`, dados: encomenda });
}

export async function updateEstado(req, res) {
  const id = positiveInteger(req.params.id, 'id');
  const encomenda = await encomendaService.atualizarEstado(id, req.body?.estado);
  res.json({ mensagem: `Encomenda ${encomenda.numero} atualizada para "${encomenda.estado}".`, dados: encomenda });
}

export async function resumo(_req, res) {
  res.json({ dados: await encomendaService.obterResumo() });
}
