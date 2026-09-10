const express = require('express');
const userController = require('../controllers/userController');
const authMiddleware = require('../middleware/authMiddleware');
const roleMiddleware = require('../middleware/roleMiddleware');
const { validateUser } = require('../middleware/validationMiddleware');

const router = express.Router();

router.use(authMiddleware, roleMiddleware('admin'));

router.get('/', userController.getAll);
router.post('/', validateUser, userController.create);
router.delete('/:id', userController.remove);

module.exports = router;
