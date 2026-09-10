const { log } = require('../utils/logger');

function requestLogger(req, res, next) {
    const startedAt = Date.now();

    res.on('finish', () => {
        const duration = Date.now() - startedAt;
        log(`${req.method} ${req.originalUrl} -> ${res.statusCode} (${duration}ms)`);
    });

    next();
}

module.exports = requestLogger;
