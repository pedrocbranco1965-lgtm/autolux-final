// Erro personalizado para conseguirmos devolver codigos HTTP adequados.

class AppError extends Error {
    constructor(message, statusCode = 500) {
        super(message);
        this.statusCode = statusCode;
    }
}

module.exports = AppError;
