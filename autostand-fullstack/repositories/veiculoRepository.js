const BaseRepository = require('./BaseRepository');
const { pool } = require('../config/database');
const Carro = require('../models/Carro');
const Moto = require('../models/Moto');
const { assertSafeLimitOffset } = require('../utils/pagination');

class VeiculoRepository extends BaseRepository {
    constructor() {
        super('veiculos');
    }

    mapRow(row) {
        const dados = {
            id: row.id,
            marca: row.marca,
            modelo: row.modelo,
            ano: row.ano,
            preco: row.preco,
            combustivel: row.combustivel,
            quilometragem: row.quilometragem,
            estado: row.estado,
            portas: row.portas,
            cilindradas: row.cilindradas,
            createdAt: row.created_at
        };

        // Aqui nasce o polimorfismo: a mesma linha pode virar Carro ou Moto.
        if (row.tipo === 'moto') {
            return new Moto(dados);
        }

        return new Carro(dados);
    }

    buildFilters(filters = {}) {
        const conditions = [];
        const params = [];

        if (filters.q) {
            conditions.push('(marca LIKE ? OR modelo LIKE ?)');
            params.push(`%${filters.q}%`, `%${filters.q}%`);
        }

        if (filters.tipo) {
            conditions.push('tipo = ?');
            params.push(filters.tipo);
        }

        if (filters.combustivel) {
            conditions.push('combustivel = ?');
            params.push(filters.combustivel);
        }

        if (filters.estado) {
            conditions.push('estado = ?');
            params.push(filters.estado);
        }

        if (filters.excludeEstado) {
            conditions.push('estado <> ?');
            params.push(filters.excludeEstado);
        }

        if (filters.ano) {
            conditions.push('ano = ?');
            params.push(Number(filters.ano));
        }

        if (filters.precoMin) {
            conditions.push('preco >= ?');
            params.push(Number(filters.precoMin));
        }

        if (filters.precoMax) {
            conditions.push('preco <= ?');
            params.push(Number(filters.precoMax));
        }

        const whereSql = conditions.length > 0 ? ` WHERE ${conditions.join(' AND ')}` : '';
        return { whereSql, params };
    }

    async getAll(filters = {}) {
        const { whereSql, params } = this.buildFilters(filters);
        const page = Number(filters.page) || 1;
        const limit = Number(filters.limit) || 12;
        const offset = Number(filters.offset) || 0;
        assertSafeLimitOffset(limit, offset);

        const [countRows] = await pool.execute(
            `SELECT COUNT(*) AS total FROM veiculos${whereSql}`,
            params
        );
        const total = Number(countRows[0].total);

        const sql = `SELECT * FROM veiculos${whereSql} ORDER BY id DESC LIMIT ${limit} OFFSET ${offset}`;
        const [rows] = await pool.execute(sql, params);

        return {
            items: rows.map((row) => this.mapRow(row)),
            total,
            page,
            limit
        };
    }

    async findById(id) {
        const row = await this.findRowById(id);
        return row ? this.mapRow(row) : null;
    }

    async insert(dados) {
        const sql = `
            INSERT INTO veiculos
            (tipo, marca, modelo, ano, preco, combustivel, quilometragem, portas, cilindradas, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        `;

        const portas = dados.tipo === 'carro' ? Number(dados.portas) : null;
        const cilindradas = dados.tipo === 'moto' ? Number(dados.cilindradas) : null;

        const [result] = await pool.execute(sql, [
            dados.tipo,
            dados.marca.trim(),
            dados.modelo.trim(),
            Number(dados.ano),
            Number(dados.preco),
            dados.combustivel,
            Number(dados.quilometragem || 0),
            portas,
            cilindradas,
            dados.estado || 'disponivel'
        ]);

        return this.findById(result.insertId);
    }

    async update(id, dados) {
        const portas = dados.tipo === 'carro' ? Number(dados.portas) : null;
        const cilindradas = dados.tipo === 'moto' ? Number(dados.cilindradas) : null;

        const sql = `
            UPDATE veiculos
            SET tipo = ?, marca = ?, modelo = ?, ano = ?, preco = ?,
                combustivel = ?, quilometragem = ?, portas = ?, cilindradas = ?, estado = ?
            WHERE id = ?
        `;

        const [result] = await pool.execute(sql, [
            dados.tipo,
            dados.marca.trim(),
            dados.modelo.trim(),
            Number(dados.ano),
            Number(dados.preco),
            dados.combustivel,
            Number(dados.quilometragem || 0),
            portas,
            cilindradas,
            dados.estado || 'disponivel',
            id
        ]);

        if (result.affectedRows === 0) {
            return null;
        }

        return this.findById(id);
    }

    async delete(id) {
        return this.deleteById(id);
    }
}

module.exports = new VeiculoRepository();
