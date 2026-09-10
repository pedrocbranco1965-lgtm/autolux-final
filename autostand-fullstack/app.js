// app.js
// Responsabilidade: configurar o Express, middlewares, rotas e front-end estatico.

const express = require('express');
const path = require('path');
const helmet = require('helmet');
const cookieParser = require('cookie-parser');

const veiculoRoutes = require('./routes/veiculoRoutes');
const authRoutes = require('./routes/authRoutes');
const userRoutes = require('./routes/userRoutes');
const vendaRoutes = require('./routes/vendaRoutes');

const requestLogger = require('./middleware/requestLogger');
const errorMiddleware = require('./middleware/errorMiddleware');
const { apiLimiter } = require('./middleware/rateLimiters');

const app = express();

// Cabecalhos HTTP de seguranca (CSP, X-Frame-Options, etc.).
app.use(helmet({
    contentSecurityPolicy: {
        directives: {
            defaultSrc: ["'self'"],
            scriptSrc: ["'self'"],
            styleSrc: ["'self'", 'https://cdn.jsdelivr.net'],
            imgSrc: ["'self'", 'data:'],
            connectSrc: ["'self'"],
            fontSrc: ["'self'", 'https://cdn.jsdelivr.net'],
            objectSrc: ["'none'"],
            frameAncestors: ["'none'"]
        }
    },
    // COEP estrito bloqueava o CSS do Bootstrap no CDN.
    crossOriginEmbedderPolicy: false
}));

app.use(cookieParser());

// Limite de tamanho do JSON evita bodies gigantes.
app.use(express.json({ limit: '32kb' }));
app.use(express.urlencoded({ extended: true, limit: '32kb' }));

app.use(requestLogger);

app.use('/api', apiLimiter);

app.use('/api/auth', authRoutes);
app.use('/api/veiculos', veiculoRoutes);
app.use('/api/users', userRoutes);
app.use('/api/vendas', vendaRoutes);

app.use(express.static(path.join(__dirname, 'public')));

app.get('/api', (req, res) => {
    res.json({
        success: true,
        message: 'API AutoStand online'
    });
});

app.use('/api', (req, res) => {
    res.status(404).json({
        success: false,
        message: 'Rota da API nao encontrada.'
    });
});

app.use(errorMiddleware);

module.exports = app;
