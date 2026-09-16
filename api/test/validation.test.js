import assert from 'node:assert/strict';
import test from 'node:test';
import { validateOrder } from '../src/validation.js';

test('aceita uma encomenda válida e normaliza os valores', () => {
  const result = validateOrder({
    supplierId: '2',
    notes: 'Entregar de manhã',
    items: [{ supplierPartId: '4', quantity: '3' }],
  });

  assert.equal(result.valid, true);
  assert.deepEqual(result.value, {
    supplierId: 2,
    notes: 'Entregar de manhã',
    items: [{ supplierPartId: 4, quantity: 3 }],
  });
});

test('rejeita encomendas sem fornecedor nem peças', () => {
  const result = validateOrder({});

  assert.equal(result.valid, false);
  assert.match(result.errors.join(' '), /fornecedor/i);
  assert.match(result.errors.join(' '), /pelo menos uma peça/i);
});

test('rejeita quantidades inválidas e peças repetidas', () => {
  const result = validateOrder({
    supplierId: 1,
    items: [
      { supplierPartId: 3, quantity: 0 },
      { supplierPartId: 3, quantity: 2 },
    ],
  });

  assert.equal(result.valid, false);
  assert.match(result.errors.join(' '), /quantidade/i);
  assert.match(result.errors.join(' '), /mais do que uma vez/i);
});
