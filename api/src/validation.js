export function validateOrder(input) {
  const supplierId = Number(input?.supplierId);
  const notes = String(input?.notes ?? '').trim();
  const rawItems = Array.isArray(input?.items) ? input.items : [];
  const errors = [];

  if (!Number.isInteger(supplierId) || supplierId < 1) {
    errors.push('Selecione um fornecedor válido.');
  }
  if (rawItems.length === 0) {
    errors.push('A encomenda deve conter pelo menos uma peça.');
  }
  if (notes.length > 500) {
    errors.push('As observações não podem exceder 500 caracteres.');
  }

  const items = rawItems.map((item) => ({
    supplierPartId: Number(item?.supplierPartId),
    quantity: Number(item?.quantity),
  }));

  for (const item of items) {
    if (!Number.isInteger(item.supplierPartId) || item.supplierPartId < 1) {
      errors.push('Existe uma peça inválida na encomenda.');
    }
    if (!Number.isInteger(item.quantity) || item.quantity < 1 || item.quantity > 999) {
      errors.push('Cada quantidade deve ser um número inteiro entre 1 e 999.');
    }
  }

  if (new Set(items.map((item) => item.supplierPartId)).size !== items.length) {
    errors.push('A mesma peça não pode aparecer mais do que uma vez.');
  }

  return {
    valid: errors.length === 0,
    errors: [...new Set(errors)],
    value: { supplierId, notes, items },
  };
}
