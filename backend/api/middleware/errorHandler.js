import { HttpError } from '../utils/HttpError.js';

/** Traduz erros conhecidos do MySQL em mensagens úteis para o cliente da API. */
const ERROS_MYSQL = {
  ER_DUP_ENTRY: { status: 409, mensagem: 'Já existe um registo com esses dados.' },
  ER_NO_REFERENCED_ROW_2: { status: 400, mensagem: 'Referência inválida para um registo relacionado.' },
  ER_BAD_DB_ERROR: { status: 500, mensagem: 'Base de dados de compras inexistente. Correr "npm run db:setup".' },
  ECONNREFUSED: { status: 503, mensagem: 'Sem ligação ao servidor MySQL. Verifique se o serviço está a correr.' },
};

export function notFoundHandler(req, res) {
  res.status(404).json({ erro: `Rota não encontrada: ${req.method} ${req.originalUrl}` });
}

// eslint-disable-next-line no-unused-vars -- o Express exige a assinatura de 4 argumentos
export function errorHandler(error, _req, res, _next) {
  if (error instanceof HttpError) {
    return res.status(error.status).json({ erro: error.message, detalhes: error.details ?? undefined });
  }

  const conhecido = ERROS_MYSQL[error.code];
  if (conhecido) {
    console.error(`[api] erro MySQL ${error.code}: ${error.message}`);
    return res.status(conhecido.status).json({ erro: conhecido.mensagem });
  }

  if (error instanceof SyntaxError && 'body' in error) {
    return res.status(400).json({ erro: 'Corpo do pedido não é JSON válido.' });
  }

  console.error('[api] erro inesperado:', error);
  return res.status(500).json({ erro: 'Erro interno no serviço de compras.' });
}
