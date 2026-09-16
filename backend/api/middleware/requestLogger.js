/** Registo simples de pedidos, útil para o professor acompanhar a integração PHP → Node.js. */
export function requestLogger(req, res, next) {
  const inicio = process.hrtime.bigint();

  res.on('finish', () => {
    const duracaoMs = Number(process.hrtime.bigint() - inicio) / 1_000_000;
    console.log(`[api] ${req.method} ${req.originalUrl} -> ${res.statusCode} (${duracaoMs.toFixed(1)} ms)`);
  });

  next();
}
