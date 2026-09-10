const AuthService = require('../services/authService');
const userRepository = require('../repositories/userRepository');
const AppError = require('../utils/AppError');
const { setAuthCookie, clearAuthCookie } = require('../utils/authCookie');

async function login(req, res, next) {
    try {
        const { email, password } = req.body;

        if (!email || !password) {
            throw new AppError('Email e password sao obrigatorios.', 400);
        }

        const result = await AuthService.login(email, password);

        setAuthCookie(res, result.token);

        res.json({
            success: true,
            user: result.user
        });
    } catch (error) {
        next(error);
    }
}

async function me(req, res, next) {
    try {
        const user = await userRepository.findById(req.user.id);

        if (!user) {
            throw new AppError('Utilizador nao encontrado.', 404);
        }

        res.json({
            success: true,
            user: user.toJSON()
        });
    } catch (error) {
        next(error);
    }
}

function logout(req, res) {
    clearAuthCookie(res);

    res.json({
        success: true,
        message: 'Sessao terminada.'
    });
}

module.exports = {
    login,
    me,
    logout
};
