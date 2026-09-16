/**
 * Erro com código HTTP associado. Permite que serviços e repositórios sinalizem
 * falhas de negócio sem conhecerem o Express: o tratamento central converte-o
 * na resposta JSON adequada.
 */
export class HttpError extends Error {
  constructor(status, message, details = null) {
    super(message);
    this.name = 'HttpError';
    this.status = status;
    this.details = details;
  }

  static badRequest(message, details = null) {
    return new HttpError(400, message, details);
  }

  static notFound(message) {
    return new HttpError(404, message);
  }

  static conflict(message) {
    return new HttpError(409, message);
  }
}
