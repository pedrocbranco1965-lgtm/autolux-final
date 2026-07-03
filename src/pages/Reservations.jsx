import { useState } from 'react';
import { siteInfo } from '../data/site';

export default function Reservations() {
  const [submitted, setSubmitted] = useState(false);
  const [form, setForm] = useState({
    name: '',
    email: '',
    phone: '',
    date: '',
    time: '',
    guests: '2',
    message: '',
  });

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    const subject = encodeURIComponent('Pedido de Reserva - Flavors Restaurante');
    const body = encodeURIComponent(
      `Nome: ${form.name}\nEmail: ${form.email}\nTelefone: ${form.phone}\nData: ${form.date}\nHora: ${form.time}\nNúmero de pessoas: ${form.guests}\n\nMensagem:\n${form.message}`
    );
    window.location.href = `mailto:${siteInfo.email}?subject=${subject}&body=${body}`;
    setSubmitted(true);
  };

  return (
    <>
      <section className="page-hero">
        <div className="container">
          <h1>Faça a sua reserva</h1>
          <p>Introduza os seus dados e faremos o possível para escolher a melhor mesa para si.</p>
        </div>
      </section>

      <section className="section">
        <div className="container reservation-layout">
          <form className="reservation-form" onSubmit={handleSubmit}>
            <div className="form-row">
              <label>
                Nome *
                <input
                  type="text"
                  name="name"
                  required
                  value={form.name}
                  onChange={handleChange}
                />
              </label>
              <label>
                Email *
                <input
                  type="email"
                  name="email"
                  required
                  value={form.email}
                  onChange={handleChange}
                />
              </label>
            </div>

            <div className="form-row">
              <label>
                Telefone *
                <input
                  type="tel"
                  name="phone"
                  required
                  value={form.phone}
                  onChange={handleChange}
                />
              </label>
              <label>
                Número de pessoas
                <select name="guests" value={form.guests} onChange={handleChange}>
                  {[1, 2, 3, 4, 5, 6, 7, 8, 9, 10].map((n) => (
                    <option key={n} value={n}>
                      {n} {n === 1 ? 'pessoa' : 'pessoas'}
                    </option>
                  ))}
                  <option value="10+">Mais de 10</option>
                </select>
              </label>
            </div>

            <div className="form-row">
              <label>
                Data *
                <input
                  type="date"
                  name="date"
                  required
                  value={form.date}
                  onChange={handleChange}
                />
              </label>
              <label>
                Hora *
                <input
                  type="time"
                  name="time"
                  required
                  value={form.time}
                  onChange={handleChange}
                />
              </label>
            </div>

            <label>
              Mensagem / Pedidos especiais
              <textarea
                name="message"
                rows="4"
                value={form.message}
                onChange={handleChange}
                placeholder="Alergias, ocasião especial, preferência de mesa..."
              />
            </label>

            <button type="submit" className="btn btn-primary">
              Enviar Pedido de Reserva
            </button>

            {submitted && (
              <p className="form-success">
                O seu cliente de email foi aberto. Envie a mensagem para concluir o pedido de
                reserva. Também pode reservar diretamente no{' '}
                <a href={siteInfo.thefork} target="_blank" rel="noopener noreferrer">
                  TheFork
                </a>
                .
              </p>
            )}
          </form>

          <aside className="reservation-info">
            <h3>Informações</h3>
            <p>
              <strong>Telefone:</strong>{' '}
              <a href={siteInfo.phoneLink}>{siteInfo.phone}</a>
            </p>
            <p>
              <strong>Email:</strong>{' '}
              <a href={`mailto:${siteInfo.email}`}>{siteInfo.email}</a>
            </p>
            <p>
              <strong>Morada:</strong> {siteInfo.address}
            </p>
            <hr />
            <p>
              <strong>Almoço:</strong> {siteInfo.hours.lunch}
            </p>
            <p>
              <strong>Jantar:</strong> {siteInfo.hours.dinner}
            </p>
            <p>
              <strong>{siteInfo.hours.closed}</strong>
            </p>
            <hr />
            <a
              href={siteInfo.thefork}
              target="_blank"
              rel="noopener noreferrer"
              className="btn btn-outline"
            >
              Reservar no TheFork
            </a>
          </aside>
        </div>
      </section>
    </>
  );
}
