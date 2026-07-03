import { partners } from '../data/content';

export default function Partners() {
  return (
    <section className="section partners">
      <div className="container">
        <h2 className="section-title">Quem nos acompanha na nossa viagem — os nossos parceiros</h2>
        <div className="partners-grid">
          {partners.map((p) =>
            p.url ? (
              <a key={p.name} href={p.url} target="_blank" rel="noopener noreferrer">
                <img src={p.image} alt={p.name} />
              </a>
            ) : (
              <div key={p.name} className="partner-item">
                <img src={p.image} alt={p.name} />
              </div>
            )
          )}
        </div>
      </div>
    </section>
  );
}
