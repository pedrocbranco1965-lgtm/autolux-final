import path from 'node:path';
import { fileURLToPath } from 'node:url';
import dotenv from 'dotenv';

const projectRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');

dotenv.config({ path: path.join(projectRoot, '.env') });
dotenv.config({ path: path.join(projectRoot, '.env.example') });

const toInt = (value, fallback) => {
  const parsed = Number.parseInt(value ?? '', 10);
  return Number.isNaN(parsed) ? fallback : parsed;
};

export const config = {
  projectRoot,
  api: {
    port: toInt(process.env.API_PORT, 3001),
  },
  web: {
    port: toInt(process.env.WEB_PORT, 8000),
  },
  database: {
    host: process.env.DB_HOST ?? '127.0.0.1',
    port: toInt(process.env.DB_PORT, 3306),
    user: process.env.DB_USER ?? 'root',
    password: process.env.DB_PASSWORD ?? '',
    vendas: process.env.DB_VENDAS ?? 'autolux_vendas',
    compras: process.env.DB_COMPRAS ?? 'autolux_compras',
  },
};
