const { rateLimit } = require('express-rate-limit');

function skipInTests() {
    return process.env.NODE_ENV === 'test';
}

const jsonLimitMessage = (message) => ({
    success: false,
    message
});

// Limite geral da API: evita abusos acidentais (scripts, F5 em loop).
const apiLimiter = rateLimit({
    windowMs: 15 * 60 * 1000,
    limit: 200,
    standardHeaders: 'draft-8',
    legacyHeaders: false,
    skip: skipInTests,
    message: jsonLimitMessage('Demasiados pedidos. Tenta novamente daqui a alguns minutos.')
});

// Login e mais apertado: dificulta adivinhar passwords por forca bruta.
// skipSuccessfulRequests: um login certo nao conta para o limite.
const loginLimiter = rateLimit({
    windowMs: 15 * 60 * 1000,
    limit: 10,
    standardHeaders: 'draft-8',
    legacyHeaders: false,
    skipSuccessfulRequests: true,
    skip: skipInTests,
    message: jsonLimitMessage('Demasiadas tentativas de login. Espera um pouco e tenta de novo.')
});

module.exports = {
    apiLimiter,
    loginLimiter
};
