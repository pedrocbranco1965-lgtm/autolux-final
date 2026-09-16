import { Router } from 'express';
import { fornecedorRoutes } from './fornecedorRoutes.js';
import { encomendaRoutes } from './encomendaRoutes.js';
import { pool } from '../db/pool.js';
import { asyncHandler } from '../utils/asyncHandler.js';

export const apiRoutes = Router();

apiRoutes.get(
  '/health',
  asyncHandler(async (_req, res) => {
    const ligacao = await pool.getConnection();
    try {
      await ligacao.ping();
      res.json({ estado: 'ok', baseDados: 'ligada', instante: new Date().toISOString() });
    } finally {
      ligacao.release();
    }
  }),
);

apiRoutes.use('/fornecedores', fornecedorRoutes);
apiRoutes.use('/encomendas', encomendaRoutes);
