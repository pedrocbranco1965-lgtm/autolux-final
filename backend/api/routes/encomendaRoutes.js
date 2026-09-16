import { Router } from 'express';
import * as encomendaController from '../controllers/encomendaController.js';
import { asyncHandler } from '../utils/asyncHandler.js';

export const encomendaRoutes = Router();

encomendaRoutes.get('/', asyncHandler(encomendaController.index));
encomendaRoutes.get('/resumo', asyncHandler(encomendaController.resumo));
encomendaRoutes.get('/:id', asyncHandler(encomendaController.show));
encomendaRoutes.post('/', asyncHandler(encomendaController.store));
encomendaRoutes.patch('/:id/estado', asyncHandler(encomendaController.updateEstado));
