/**
 * Adapta um handler assíncrono ao Express 4, que não encaminha automaticamente
 * as rejeições de promessas para o middleware de erros.
 */
export const asyncHandler = (handler) => (req, res, next) => {
  Promise.resolve(handler(req, res, next)).catch(next);
};
