const jwt = require('jsonwebtoken');
const AppError = require('../utils/AppError');
const { readAuthToken, getJwtSecret } = require('../utils/authCookie');

function authMiddleware(req, res, next) {
    try {
        const token = readAuthToken(req);

        if (!token) {
            throw new AppError('Token de autenticacao em falta.', 401);
        }

        const decoded = jwt.verify(token, getJwtSecret());

        req.user = decoded;
        next();
    } catch (error) {
        if (error.name === 'TokenExpiredError') {
            return next(new AppError('Token expirado. Faz login novamente.', 401));
        }

        if (error.name === 'JsonWebTokenError') {
            return next(new AppError('Token invalido.', 401));
        }

        return next(error);
    }
}

module.exports = authMiddleware;
