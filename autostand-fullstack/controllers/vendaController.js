const vendaRepository = require('../repositories/vendaRepository');
const { parsePagination, paginationMeta } = require('../utils/pagination');

async function getAll(req, res, next) {
    try {
        const { page, limit, offset } = parsePagination(req.query, {
            defaultLimit: 10,
            maxLimit: 50
        });

        const result = await vendaRepository.getAll({ page, limit, offset });

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
        const vendaId = await vendaRepository.createSale({
            vehicleId: Number(req.body.vehicleId),
            customerName: req.body.customerName,
            customerEmail: req.body.customerEmail,
            salePrice: Number(req.body.salePrice),
            userId: req.user.id
        });

        res.status(201).json({
            success: true,
            message: 'Venda registada. O veiculo ficou com estado vendido.',
            id: vendaId
        });
    } catch (error) {
        next(error);
    }
}

module.exports = {
    getAll,
    create
};
