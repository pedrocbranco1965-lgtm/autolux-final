const BaseRepository = require('./BaseRepository');
const { pool } = require('../config/database');
const User = require('../models/User');
const { assertSafeLimitOffset } = require('../utils/pagination');

class UserRepository extends BaseRepository {
    constructor() {
        super('users');
    }

    mapRow(row) {
        return new User({
            id: row.id,
            name: row.name,
            email: row.email,
            passwordHash: row.password,
            role: row.role,
            createdAt: row.created_at
        });
    }

    async getAll({ page = 1, limit = 10, offset = 0 } = {}) {
        assertSafeLimitOffset(Number(limit), Number(offset));
        const [countRows] = await pool.execute('SELECT COUNT(*) AS total FROM users');
        const total = Number(countRows[0].total);

        const sql = `SELECT * FROM users ORDER BY id DESC LIMIT ${Number(limit)} OFFSET ${Number(offset)}`;
        const [rows] = await pool.query(sql);

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

    async findByEmail(email) {
        const [rows] = await pool.execute(
            'SELECT * FROM users WHERE email = ? LIMIT 1',
            [email]
        );

        return rows[0] ? this.mapRow(rows[0]) : null;
    }

    async insert({ name, email, passwordHash, role }) {
        const [result] = await pool.execute(
            `INSERT INTO users (name, email, password, role)
             VALUES (?, ?, ?, ?)`,
            [name.trim(), email.trim().toLowerCase(), passwordHash, role]
        );

        return this.findById(result.insertId);
    }

    async delete(id) {
        return this.deleteById(id);
    }
}

module.exports = new UserRepository();
