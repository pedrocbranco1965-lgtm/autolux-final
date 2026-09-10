const express = require('express');
const veiculoController = require('../controllers/veiculoController');
const authMiddleware = require('../middleware/authMiddleware');
const roleMiddleware = require('../middleware/roleMiddleware');
const { validateVehicle } = require('../middleware/validationMiddleware');

const router = express.Router();

// GET e publico: qualquer visitante pode ver o catalogo.
router.get('/', veiculoController.getAll);
router.get('/:id', veiculoController.getById);

// Alteracoes ficam protegidas por JWT e role admin.
router.post(
    '/',
    authMiddleware,
    roleMiddleware('admin'),
    validateVehicle,
    veiculoController.create
);

router.put(
    '/:id',
    authMiddleware,
    roleMiddleware('admin'),
    validateVehicle,
    veiculoController.update
);

router.delete(
    '/:id',
    authMiddleware,
    roleMiddleware('admin'),
    veiculoController.remove
);

module.exports = router;
