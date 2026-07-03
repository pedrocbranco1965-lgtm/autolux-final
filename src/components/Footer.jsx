import { Link } from 'react-router-dom';
import { siteInfo } from '../data/site';

export default function Footer() {
  return (
    <footer className="footer">
      <div className="container footer-grid">
        <div className="footer-brand">
          <img src="/images/logo.png" alt={siteInfo.name} className="footer-logo" />
          <p>{siteInfo.tagline}</p>
        </div>

        <div>
          <h4>Contactos</h4>
          <p>{siteInfo.address}</p>
          <p>
            <a href={siteInfo.phoneLink}>{siteInfo.phone}</a>
          </p>
          <p>
            <a href={`mailto:${siteInfo.email}`}>{siteInfo.email}</a>
          </p>
        </div>

        <div>
          <h4>Horário</h4>
          <p>Almoço: {siteInfo.hours.lunch}</p>
          <p>Jantar: {siteInfo.hours.dinner}</p>
          <p>{siteInfo.hours.closed}</p>
        </div>

        <div>
          <h4>Redes Sociais</h4>
          <div className="footer-social">
            <a href={siteInfo.facebook} target="_blank" rel="noopener noreferrer">
              Facebook
            </a>
            <a href={siteInfo.instagram} target="_blank" rel="noopener noreferrer">
              Instagram
            </a>
            <a href={siteInfo.thefork} target="_blank" rel="noopener noreferrer">
              TheFork
            </a>
          </div>
          <Link to="/reservas" className="btn btn-outline btn-sm">
            Reservar Mesa
          </Link>
        </div>
      </div>

      <div className="footer-bottom">
        <div className="container">
          <p>&copy; {new Date().getFullYear()} {siteInfo.name}. Todos os direitos reservados.</p>
        </div>
      </div>
    </footer>
  );
}
