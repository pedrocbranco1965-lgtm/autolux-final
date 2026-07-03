import { Link } from 'react-router-dom';

export default function SectionCard({ title, text, image, imageAlt, reverse, linkTo, linkLabel }) {
  return (
    <section className={`section section-card ${reverse ? 'reverse' : ''}`}>
      <div className="container section-card-inner">
        <div className="section-card-image">
          <img src={image} alt={imageAlt || title} />
        </div>
        <div className="section-card-content">
          <h2>{title}</h2>
          {text.split('\n\n').map((paragraph) => (
            <p key={paragraph.slice(0, 40)}>{paragraph}</p>
          ))}
          {linkTo && (
            <Link to={linkTo} className="btn btn-primary">
              {linkLabel || 'Saber mais'}
            </Link>
          )}
        </div>
      </div>
    </section>
  );
}
