require('dotenv').config();
process.env.JWT_SECRET = process.env.JWT_SECRET || 'segredo_de_teste_jest';

const jwt = require('jsonwebtoken');
const AuthService = require('../services/authService');

describe('JWT', () => {
    test('gera token com id e role', () => {
        const user = {
            id: 7,
            name: 'Pedro',
            email: 'pedro@example.com',
            role: 'admin'
        };

        const token = AuthService.gerarToken(user);
        const decoded = jwt.verify(
            token,
            process.env.JWT_SECRET
        );

        expect(decoded.id).toBe(7);
        expect(decoded.role).toBe('admin');
    });
});
