export function formatCurrency(value) {
  return new Intl.NumberFormat('pt-PT', {
    style: 'currency',
    currency: 'EUR',
    maximumFractionDigits: 0
  }).format(value);
}

export function formatKm(value) {
  return `${new Intl.NumberFormat('pt-PT').format(value)} km`;
}

export function pluralize(count, singular, plural) {
  return count === 1 ? singular : plural;
}
