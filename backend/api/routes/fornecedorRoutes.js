import { Router } from 'express';
import * as fornecedorController from '../controllers/fornecedorController.js';
import { asyncHandler } from '../utils/asyncHandler.js';

export const fornecedorRoutes = Router();

fornecedorRoutes.get('/', asyncHandler(fornecedorController.index));
fornecedorRoutes.get('/paises', asyncHandler(fornecedorController.paises));
fornecedorRoutes.get('/:id', asyncHandler(fornecedorController.show));
fornecedorRoutes.get('/:id/artigos', asyncHandler(fornecedorController.artigos));
