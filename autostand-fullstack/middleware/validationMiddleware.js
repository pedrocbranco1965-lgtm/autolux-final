const AppError = require('../utils/AppError');

function validateVehicle(req, res, next) {
    const { tipo, marca, modelo, ano, preco, combustivel, portas, cilindradas } = req.body;

    if (!['carro', 'moto'].includes(tipo)) {
        return next(new AppError('O tipo deve ser carro ou moto.', 400));
    }

    if (!marca || !modelo) {
        return next(new AppError('Marca e modelo sao obrigatorios.', 400));
    }

    const year = Number(ano);
    const price = Number(preco);

    if (!Number.isInteger(year) || year < 1900 || year > new Date().getFullYear() + 1) {
        return next(new AppError('Ano invalido.', 400));
    }

    if (!Number.isFinite(price) || price <= 0) {
        return next(new AppError('Preco invalido.', 400));
    }

    const allowedFuel = ['gasolina', 'diesel', 'hibrido', 'eletrico', 'outro'];
    if (!allowedFuel.includes(combustivel)) {
        return next(new AppError('Combustivel invalido.', 400));
    }

    if (tipo === 'carro' && (!Number.isInteger(Number(portas)) || Number(portas) < 2)) {
        return next(new AppError('Um carro deve indicar um numero de portas valido.', 400));
    }

    if (tipo === 'moto' && (!Number.isInteger(Number(cilindradas)) || Number(cilindradas) <= 0)) {
        return next(new AppError('Uma moto deve indicar cilindradas validas.', 400));
    }

    next();
}

function validateUser(req, res, next) {
    const { name, email, password, role = 'viewer' } = req.body;

    if (!name || String(name).trim().length < 2) {
        return next(new AppError('Nome invalido.', 400));
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(String(email || ''))) {
        return next(new AppError('Email invalido.', 400));
    }

    if (!password || String(password).length < 6) {
        return next(new AppError('A password deve ter pelo menos 6 caracteres.', 400));
    }

    if (!['admin', 'viewer'].includes(role)) {
        return next(new AppError('Role invalida.', 400));
    }

    next();
}

function validateSale(req, res, next) {
    const { vehicleId, customerName, customerEmail, salePrice } = req.body;

    if (!Number.isInteger(Number(vehicleId)) || Number(vehicleId) <= 0) {
        return next(new AppError('Veiculo invalido.', 400));
    }

    if (!customerName || String(customerName).trim().length < 2) {
        return next(new AppError('Nome do cliente invalido.', 400));
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(String(customerEmail || ''))) {
        return next(new AppError('Email do cliente invalido.', 400));
    }

    if (!Number.isFinite(Number(salePrice)) || Number(salePrice) <= 0) {
        return next(new AppError('Valor da venda invalido.', 400));
    }

    next();
}

module.exports = {
    validateVehicle,
    validateUser,
    validateSale
};
