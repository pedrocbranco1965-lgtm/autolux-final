function errorMiddleware(error, req, res, next) {
    console.error(error);

    if (error && error.code === 'ER_DUP_ENTRY') {
        return res.status(409).json({
            success: false,
            message: 'Ja existe um registo com esse valor unico.'
        });
    }

    if (error && error.code === 'ER_ROW_IS_REFERENCED_2') {
        return res.status(409).json({
            success: false,
            message: 'Este registo esta a ser utilizado por outro registo e nao pode ser apagado.'
        });
    }

    const statusCode = error.statusCode || 500;
    const hideDetails = process.env.NODE_ENV === 'production' && statusCode === 500;

    return res.status(statusCode).json({
        success: false,
        message: hideDetails
            ? 'Erro interno do servidor.'
            : (error.message || 'Erro interno do servidor.')
    });
}

module.exports = errorMiddleware;
