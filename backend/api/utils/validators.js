import { HttpError } from './HttpError.js';

const isBlank = (value) => value === undefined || value === null || String(value).trim() === '';

export function requiredString(value, campo, { max = 255 } = {}) {
  if (isBlank(value)) {
    throw HttpError.badRequest(`O campo "${campo}" é obrigatório.`);
  }
  const texto = String(value).trim();
  if (texto.length > max) {
    throw HttpError.badRequest(`O campo "${campo}" excede ${max} caracteres.`);
  }
  return texto;
}

export function optionalString(value, campo, { max = 255 } = {}) {
  return isBlank(value) ? null : requiredString(value, campo, { max });
}

export function positiveInteger(value, campo) {
  const numero = Number(value);
  if (!Number.isInteger(numero) || numero <= 0) {
    throw HttpError.badRequest(`O campo "${campo}" tem de ser um número inteiro positivo.`);
  }
  return numero;
}

export function positiveDecimal(value, campo) {
  const numero = Number(value);
  if (!Number.isFinite(numero) || numero < 0) {
    throw HttpError.badRequest(`O campo "${campo}" tem de ser um número não negativo.`);
  }
  return Math.round(numero * 100) / 100;
}

export function oneOf(value, campo, valoresPermitidos) {
  const texto = requiredString(value, campo);
  if (!valoresPermitidos.includes(texto)) {
    throw HttpError.badRequest(
      `O campo "${campo}" só aceita os valores: ${valoresPermitidos.join(', ')}.`,
    );
  }
  return texto;
}

export function optionalDate(value, campo) {
  if (isBlank(value)) {
    return null;
  }
  const texto = String(value).trim();
  if (!/^\d{4}-\d{2}-\d{2}$/.test(texto) || Number.isNaN(Date.parse(texto))) {
    throw HttpError.badRequest(`O campo "${campo}" tem de estar no formato AAAA-MM-DD.`);
  }
  return texto;
}

export function nonEmptyArray(value, campo) {
  if (!Array.isArray(value) || value.length === 0) {
    throw HttpError.badRequest(`O campo "${campo}" tem de conter pelo menos um elemento.`);
  }
  return value;
}
