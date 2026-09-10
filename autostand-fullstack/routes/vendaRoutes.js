const express = require('express');
const vendaController = require('../controllers/vendaController');
const authMiddleware = require('../middleware/authMiddleware');
const roleMiddleware = require('../middleware/roleMiddleware');
const { validateSale } = require('../middleware/validationMiddleware');

const router = express.Router();

router.get(
    '/',
    authMiddleware,
    roleMiddleware('admin', 'viewer'),
    vendaController.getAll
);

router.post(
    '/',
    authMiddleware,
    roleMiddleware('admin'),
    validateSale,
    vendaController.create
);

module.exports = router;
