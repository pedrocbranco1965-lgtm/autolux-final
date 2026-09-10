// Cookie HttpOnly: o JavaScript do browser NAO consegue ler o JWT
// (ao contrario do localStorage). O browser envia-o sozinho em cada pedido.

const COOKIE_NAME = 'autostand_token';

function isProduction() {
    return process.env.NODE_ENV === 'production';
}

function cookieMaxAgeMs() {
    const raw = process.env.JWT_EXPIRES_IN || '1h';
    const match = /^(\d+)([smhd])$/.exec(String(raw));

    if (!match) {
        return 60 * 60 * 1000;
    }

    const amount = Number(match[1]);
    const unit = {
        s: 1000,
        m: 60 * 1000,
        h: 60 * 60 * 1000,
        d: 24 * 60 * 60 * 1000
    };

    return amount * unit[match[2]];
}

function cookieOptions() {
    return {
        httpOnly: true,
        // Em localhost usamos HTTP, por isso Secure so em producao (HTTPS).
        secure: isProduction(),
        sameSite: 'lax',
        path: '/',
        maxAge: cookieMaxAgeMs()
    };
}

function setAuthCookie(res, token) {
    res.cookie(COOKIE_NAME, token, cookieOptions());
}

function clearAuthCookie(res) {
    res.clearCookie(COOKIE_NAME, {
        httpOnly: true,
        secure: isProduction(),
        sameSite: 'lax',
        path: '/'
    });
}

function readAuthToken(req) {
    if (req.cookies && req.cookies[COOKIE_NAME]) {
        return req.cookies[COOKIE_NAME];
    }

    const authorization = req.headers.authorization;
    if (authorization && authorization.startsWith('Bearer ')) {
        return authorization.substring(7);
    }

    return null;
}

function getJwtSecret() {
    const secret = process.env.JWT_SECRET;

    if (!secret) {
        throw new Error('JWT_SECRET em falta. Copia .env.example para .env.');
    }

    return secret;
}

module.exports = {
    COOKIE_NAME,
    setAuthCookie,
    clearAuthCookie,
    readAuthToken,
    getJwtSecret
};
