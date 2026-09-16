import cors from 'cors';
import express from 'express';
import { validateOrder } from './validation.js';

export function createApp(pool) {
  const app = express();
  app.use(cors());
  app.use(express.json({ limit: '100kb' }));

  app.get('/api/health', async (_request, response, next) => {
    try {
      await pool.query('SELECT 1');
      response.json({ status: 'ok', service: 'fornecedores-api' });
    } catch (error) {
      next(error);
    }
  });

  app.get('/api/suppliers', async (_request, response, next) => {
    try {
      const [suppliers] = await pool.query(
        `SELECT id, name, email, phone, address
         FROM suppliers WHERE active = TRUE ORDER BY name`,
      );
      const [parts] = await pool.query(
        `SELECT id, supplier_id AS supplierId, sku, name, unit_cost AS unitCost,
                available_stock AS availableStock
         FROM supplier_parts ORDER BY name`,
      );

      response.json(suppliers.map((supplier) => ({
        ...supplier,
        parts: parts.filter((part) => part.supplierId === supplier.id),
      })));
    } catch (error) {
      next(error);
    }
  });

  app.get('/api/orders', async (_request, response, next) => {
    try {
      const [orders] = await pool.query(
        `SELECT po.id, po.status, po.total, po.notes, po.created_at AS createdAt,
                s.name AS supplierName
         FROM purchase_orders po
         JOIN suppliers s ON s.id = po.supplier_id
         ORDER BY po.created_at DESC`,
      );
      response.json(orders);
    } catch (error) {
      next(error);
    }
  });

  app.post('/api/orders', async (request, response, next) => {
    const validation = validateOrder(request.body);
    if (!validation.valid) {
      return response.status(422).json({ error: 'Dados inválidos.', details: validation.errors });
    }

    const { supplierId, notes, items } = validation.value;
    const connection = await pool.getConnection();

    try {
      await connection.beginTransaction();
      const [suppliers] = await connection.execute(
        'SELECT id FROM suppliers WHERE id = ? AND active = TRUE FOR UPDATE',
        [supplierId],
      );
      if (suppliers.length === 0) {
        await connection.rollback();
        return response.status(404).json({ error: 'Fornecedor não encontrado.' });
      }

      const ids = items.map((item) => item.supplierPartId);
      const placeholders = ids.map(() => '?').join(', ');
      const [parts] = await connection.execute(
        `SELECT id, name, unit_cost AS unitCost, available_stock AS availableStock
         FROM supplier_parts
         WHERE supplier_id = ? AND id IN (${placeholders}) FOR UPDATE`,
        [supplierId, ...ids],
      );
      if (parts.length !== items.length) {
        await connection.rollback();
        return response.status(422).json({ error: 'Uma ou mais peças não pertencem ao fornecedor.' });
      }

      let total = 0;
      for (const item of items) {
        const part = parts.find((candidate) => candidate.id === item.supplierPartId);
        if (item.quantity > part.availableStock) {
          await connection.rollback();
          return response.status(409).json({ error: `Stock insuficiente no fornecedor para ${part.name}.` });
        }
        total += Number(part.unitCost) * item.quantity;
      }
      total = Number(total.toFixed(2));

      const [orderResult] = await connection.execute(
        `INSERT INTO purchase_orders (supplier_id, total, notes)
         VALUES (?, ?, ?)`,
        [supplierId, total, notes || null],
      );

      for (const item of items) {
        const part = parts.find((candidate) => candidate.id === item.supplierPartId);
        await connection.execute(
          `INSERT INTO purchase_order_items
             (purchase_order_id, supplier_part_id, quantity, unit_cost)
           VALUES (?, ?, ?, ?)`,
          [orderResult.insertId, item.supplierPartId, item.quantity, part.unitCost],
        );
        await connection.execute(
          'UPDATE supplier_parts SET available_stock = available_stock - ? WHERE id = ?',
          [item.quantity, item.supplierPartId],
        );
      }

      await connection.commit();
      return response.status(201).json({
        id: orderResult.insertId,
        status: 'submitted',
        total,
        message: 'Encomenda submetida com sucesso.',
      });
    } catch (error) {
      await connection.rollback();
      return next(error);
    } finally {
      connection.release();
    }
  });

  app.use((_request, response) => {
    response.status(404).json({ error: 'Endpoint não encontrado.' });
  });

  app.use((error, _request, response, _next) => {
    console.error(error);
    response.status(500).json({ error: 'Erro interno do serviço de fornecedores.' });
  });

  return app;
}
