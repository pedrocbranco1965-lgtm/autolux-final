const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const userRepository = require('../repositories/userRepository');
const AppError = require('../utils/AppError');
const { getJwtSecret } = require('../utils/authCookie');

class AuthService {
    static gerarToken(user) {
        return jwt.sign(
            {
                id: user.id,
                name: user.name,
                email: user.email,
                role: user.role
            },
            getJwtSecret(),
            {
                expiresIn: process.env.JWT_EXPIRES_IN || '1h'
            }
        );
    }

    static async login(email, password) {
        const user = await userRepository.findByEmail(email.trim().toLowerCase());

        if (!user) {
            throw new AppError('Email ou password incorretos.', 401);
        }

        const passwordOk = await bcrypt.compare(
            password,
            user.getPasswordHash()
        );

        if (!passwordOk) {
            throw new AppError('Email ou password incorretos.', 401);
        }

        return {
            token: this.gerarToken(user),
            user: user.toJSON()
        };
    }
}

module.exports = AuthService;
