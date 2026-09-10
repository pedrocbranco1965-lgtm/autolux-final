const veiculoRepository = require('../repositories/veiculoRepository');
const AppError = require('../utils/AppError');
const { parsePagination, paginationMeta } = require('../utils/pagination');

async function getAll(req, res, next) {
    try {
        const { page, limit, offset } = parsePagination(req.query, {
            defaultLimit: 9,
            maxLimit: 50
        });

        const result = await veiculoRepository.getAll({
            q: req.query.q || '',
            tipo: req.query.tipo || '',
            combustivel: req.query.combustivel || '',
            estado: req.query.estado || '',
            excludeEstado: req.query.excludeEstado || '',
            ano: req.query.ano || '',
            precoMin: req.query.precoMin || '',
            precoMax: req.query.precoMax || '',
            page,
            limit,
            offset
        });

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

async function getById(req, res, next) {
    try {
        const veiculo = await veiculoRepository.findById(Number(req.params.id));

        if (!veiculo) {
            throw new AppError('Veiculo nao encontrado.', 404);
        }

        res.json({
            success: true,
            data: veiculo
        });
    } catch (error) {
        next(error);
    }
}

async function create(req, res, next) {
    try {
        const veiculo = await veiculoRepository.insert(req.body);

        res.status(201).json({
            success: true,
            message: 'Veiculo criado com sucesso.',
            data: veiculo
        });
    } catch (error) {
        next(error);
    }
}

async function update(req, res, next) {
    try {
        const veiculo = await veiculoRepository.update(Number(req.params.id), req.body);

        if (!veiculo) {
            throw new AppError('Veiculo nao encontrado.', 404);
        }

        res.json({
            success: true,
            message: 'Veiculo atualizado com sucesso.',
            data: veiculo
        });
    } catch (error) {
        next(error);
    }
}

async function remove(req, res, next) {
    try {
        const deleted = await veiculoRepository.delete(Number(req.params.id));

        if (!deleted) {
            throw new AppError('Veiculo nao encontrado.', 404);
        }

        res.json({
            success: true,
            message: 'Veiculo apagado com sucesso.'
        });
    } catch (error) {
        next(error);
    }
}

module.exports = {
    getAll,
    getById,
    create,
    update,
    remove
};
