import { testimonials } from '../data/content';

export default function Testimonials() {
  return (
    <section className="section testimonials">
      <div className="container">
        <h2 className="section-title">O que dizem os nossos clientes que embarcaram na nossa viagem</h2>
        <div className="testimonials-grid">
          {testimonials.map((t) => (
            <article key={t.author} className="testimonial-card">
              <blockquote>&ldquo;{t.quote}&rdquo;</blockquote>
              <p>{t.text}</p>
              <footer>
                <strong>{t.author}</strong>
                <span>{t.source}</span>
              </footer>
            </article>
          ))}
        </div>
      </div>
    </section>
  );
}
