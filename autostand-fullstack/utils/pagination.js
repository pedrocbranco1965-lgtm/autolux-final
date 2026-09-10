// Interpreta ?page= e ?limit= vindos da query string.
// LIMIT/OFFSET so entram no SQL depois de serem inteiros validados.

function parsePagination(query = {}, { defaultLimit = 12, maxLimit = 50 } = {}) {
    const page = Math.max(1, Number.parseInt(query.page, 10) || 1);
    let limit = Number.parseInt(query.limit, 10) || defaultLimit;

    if (limit < 1) {
        limit = defaultLimit;
    }

    if (limit > maxLimit) {
        limit = maxLimit;
    }

    return {
        page,
        limit,
        offset: (page - 1) * limit
    };
}

function paginationMeta({ total, page, limit }) {
    const totalPages = Math.ceil(total / limit) || 0;

    return {
        total,
        page,
        limit,
        totalPages,
        hasNextPage: page < totalPages,
        hasPrevPage: page > 1
    };
}

function assertSafeLimitOffset(limit, offset) {
    if (!Number.isInteger(limit) || !Number.isInteger(offset) || limit < 1 || offset < 0) {
        throw new Error('Paginacao invalida.');
    }
}

module.exports = {
    parsePagination,
    paginationMeta,
    assertSafeLimitOffset
};
