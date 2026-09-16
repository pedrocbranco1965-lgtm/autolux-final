<?php
declare(strict_types=1);

function salesDb(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('SALES_DB_HOST') ?: '127.0.0.1';
    $port = getenv('SALES_DB_PORT') ?: '3307';
    $name = getenv('SALES_DB_NAME') ?: 'autolux_vendas';
    $user = getenv('SALES_DB_USER') ?: 'autolux';
    $password = getenv('SALES_DB_PASSWORD') ?: 'autolux';
    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function suppliersApiUrl(): string
{
    return rtrim(getenv('SUPPLIERS_API_URL') ?: 'http://localhost:3000/api', '/');
}
