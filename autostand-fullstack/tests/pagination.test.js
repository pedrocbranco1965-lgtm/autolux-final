const { parsePagination, paginationMeta } = require('../utils/pagination');

describe('Paginacao', () => {
    test('usa page 1 e limit por omissao', () => {
        expect(parsePagination({}, { defaultLimit: 9 })).toEqual({
            page: 1,
            limit: 9,
            offset: 0
        });
    });

    test('rejeita limit acima do maximo', () => {
        const result = parsePagination({ page: '2', limit: '999' }, { defaultLimit: 10, maxLimit: 50 });
        expect(result).toEqual({
            page: 2,
            limit: 50,
            offset: 50
        });
    });

    test('meta indica pagina seguinte', () => {
        expect(paginationMeta({ total: 25, page: 1, limit: 10 })).toMatchObject({
            total: 25,
            page: 1,
            limit: 10,
            totalPages: 3,
            hasNextPage: true,
            hasPrevPage: false
        });
    });
});
