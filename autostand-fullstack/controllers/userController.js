const bcrypt = require('bcrypt');
const userRepository = require('../repositories/userRepository');
const AppError = require('../utils/AppError');
const { parsePagination, paginationMeta } = require('../utils/pagination');

async function getAll(req, res, next) {
    try {
        const { page, limit, offset } = parsePagination(req.query, {
            defaultLimit: 10,
            maxLimit: 50
        });

        const result = await userRepository.getAll({ page, limit, offset });

        res.json({
            success: true,
            ...paginationMeta({
                total: result.total,
                page: result.page,
                limit: result.limit
            }),
            data: result.items
        });
    } catch (error) {
        next(error);
    }
}

async function create(req, res, next) {
    try {
        const { name, email, password, role = 'viewer' } = req.body;

        const existing = await userRepository.findByEmail(email);
        if (existing) {
            throw new AppError('Ja existe um utilizador com esse email.', 409);
        }

        const passwordHash = await bcrypt.hash(password, 10);

        const user = await userRepository.insert({
            name,
            email,
            passwordHash,
            role
        });

        res.status(201).json({
            success: true,
            message: 'Utilizador criado com sucesso.',
            data: user
        });
    } catch (error) {
        next(error);
    }
}

async function remove(req, res, next) {
    try {
        const id = Number(req.params.id);

        if (id === req.user.id) {
            throw new AppError('Nao podes apagar o utilizador com que tens sessao iniciada.', 400);
        }

        const deleted = await userRepository.delete(id);
        if (!deleted) {
            throw new AppError('Utilizador nao encontrado.', 404);
        }

        res.json({
            success: true,
            message: 'Utilizador apagado com sucesso.'
        });
    } catch (error) {
        next(error);
    }
}

module.exports = {
    getAll,
    create,
    remove
};
