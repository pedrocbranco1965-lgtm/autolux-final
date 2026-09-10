const { readAuthToken, COOKIE_NAME } = require('../utils/authCookie');

describe('Cookie de autenticacao', () => {
    test('prefere o cookie HttpOnly ao header Bearer', () => {
        const req = {
            cookies: { [COOKIE_NAME]: 'cookie-token' },
            headers: { authorization: 'Bearer header-token' }
        };

        expect(readAuthToken(req)).toBe('cookie-token');
    });

    test('aceita Bearer quando nao ha cookie (Postman)', () => {
        const req = {
            cookies: {},
            headers: { authorization: 'Bearer so-header' }
        };

        expect(readAuthToken(req)).toBe('so-header');
    });

    test('devolve null sem cookie nem Bearer', () => {
        expect(readAuthToken({ cookies: {}, headers: {} })).toBeNull();
    });
});
