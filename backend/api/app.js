import express from 'express';
import cors from 'cors';
import { apiRoutes } from './routes/index.js';
import { requestLogger } from './middleware/requestLogger.js';
import { errorHandler, notFoundHandler } from './middleware/errorHandler.js';

export function createApp() {
  const app = express();

  app.use(cors());
  app.use(express.json({ limit: '256kb' }));
  app.use(requestLogger);

  app.get('/', (_req, res) => {
    res.json({
      servico: 'AutoLux - API de Compras (Node.js)',
      baseDados: 'autolux_compras (Base de Dados 2)',
      endpoints: [
        'GET    /api/health',
        'GET    /api/fornecedores?q=&pais=&ativos=true',
        'GET    /api/fornecedores/paises',
        'GET    /api/fornecedores/:id',
        'GET    /api/fornecedores/:id/artigos',
        'GET    /api/encomendas?fornecedorId=&estado=',
        'GET    /api/encomendas/resumo',
        'GET    /api/encomendas/:id',
        'POST   /api/encomendas',
        'PATCH  /api/encomendas/:id/estado',
      ],
    });
  });

  app.use('/api', apiRoutes);
  app.use(notFoundHandler);
  app.use(errorHandler);

  return app;
}
