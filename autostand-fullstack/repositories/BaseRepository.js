// repositories/BaseRepository.js
// Simula uma classe abstrata como a DatabaseSource que estudaste em PHP.

const { pool } = require('../config/database');

class BaseRepository {
    constructor(tableName) {
        if (new.target === BaseRepository) {
            throw new Error('BaseRepository e abstrato e nao deve ser instanciado diretamente.');
        }

        this.tableName = tableName;
    }

    async getAllRows() {
        const [rows] = await pool.query(
            `SELECT * FROM ${this.tableName} ORDER BY id DESC`
        );
        return rows;
    }

    async findRowById(id) {
        const [rows] = await pool.execute(
            `SELECT * FROM ${this.tableName} WHERE id = ? LIMIT 1`,
            [id]
        );
        return rows[0] || null;
    }

    async deleteById(id) {
        const [result] = await pool.execute(
            `DELETE FROM ${this.tableName} WHERE id = ?`,
            [id]
        );
        return result.affectedRows > 0;
    }
}

module.exports = BaseRepository;
