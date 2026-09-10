const AppError = require('../utils/AppError');

function roleMiddleware(...allowedRoles) {
    return function checkRole(req, res, next) {
        if (!req.user) {
            return next(new AppError('Utilizador nao autenticado.', 401));
        }

        if (!allowedRoles.includes(req.user.role)) {
            return next(new AppError('Nao tens permissao para executar esta operacao.', 403));
        }

        next();
    };
}

module.exports = roleMiddleware;
