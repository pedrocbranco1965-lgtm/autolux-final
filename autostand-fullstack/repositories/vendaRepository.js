const BaseRepository = require('./BaseRepository');
const { pool } = require('../config/database');
const AppError = require('../utils/AppError');
const { assertSafeLimitOffset } = require('../utils/pagination');

class VendaRepository extends BaseRepository {
    constructor() {
        super('vendas');
    }

    async getAll({ page = 1, limit = 10, offset = 0 } = {}) {
        assertSafeLimitOffset(Number(limit), Number(offset));
        const [countRows] = await pool.query('SELECT COUNT(*) AS total FROM vendas');
        const total = Number(countRows[0].total);

        const sql = `
            SELECT
                v.id,
                v.vehicle_id,
                CONCAT(ve.marca, ' ', ve.modelo) AS veiculo,
                v.customer_name,
                v.customer_email,
                v.sale_price,
                v.sold_at,
                v.user_id,
                u.name AS vendedor
            FROM vendas v
            INNER JOIN veiculos ve ON ve.id = v.vehicle_id
            INNER JOIN users u ON u.id = v.user_id
            ORDER BY v.id DESC
            LIMIT ${Number(limit)} OFFSET ${Number(offset)}
        `;

        const [rows] = await pool.query(sql);
        return { items: rows, total, page, limit };
    }

    async createSale({ vehicleId, customerName, customerEmail, salePrice, userId }) {
        const connection = await pool.getConnection();

        try {
            await connection.beginTransaction();

            const [vehicles] = await connection.execute(
                'SELECT * FROM veiculos WHERE id = ? FOR UPDATE',
                [vehicleId]
            );

            const vehicle = vehicles[0];
            if (!vehicle) {
                throw new AppError('Veiculo nao encontrado.', 404);
            }

            if (vehicle.estado === 'vendido') {
                throw new AppError('Este veiculo ja foi vendido.', 409);
            }

            const [result] = await connection.execute(
                `INSERT INTO vendas
                 (vehicle_id, customer_name, customer_email, sale_price, user_id)
                 VALUES (?, ?, ?, ?, ?)`,
                [vehicleId, customerName.trim(), customerEmail.trim().toLowerCase(), salePrice, userId]
            );

            await connection.execute(
                "UPDATE veiculos SET estado = 'vendido' WHERE id = ?",
                [vehicleId]
            );

            await connection.commit();
            return result.insertId;
        } catch (error) {
            await connection.rollback();
            throw error;
        } finally {
            connection.release();
        }
    }
}

module.exports = new VendaRepository();
