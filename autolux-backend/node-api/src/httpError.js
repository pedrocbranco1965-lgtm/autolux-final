/**
 * Erro com código HTTP associado. Permite às rotas lançarem
 * `throw new HttpError(404, 'Fornecedor não encontrado')` e deixar o
 * middleware de erros em server.js formatar a resposta.
 */
class HttpError extends Error {
  constructor(status, message, detalhes) {
    super(message);
    this.status = status;
    this.detalhes = detalhes;
  }
}

module.exports = HttpError;
