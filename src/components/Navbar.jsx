import { useState } from 'react';
import { Link, NavLink } from 'react-router-dom';
import { siteInfo } from '../data/site';

const navLinks = [
  { to: '/', label: 'Home' },
  { to: '/sobre', label: 'Sobre Nós' },
  { to: '/eventos', label: 'Eventos' },
  { to: '/menu', label: 'Menu' },
  { to: '/reservas', label: 'Reservar' },
  { to: '/blog', label: 'Blog' },
];

export default function Navbar() {
  const [open, setOpen] = useState(false);

  return (
    <header className="navbar">
      <div className="container navbar-inner">
        <Link to="/" className="navbar-logo" onClick={() => setOpen(false)}>
          <img src="/images/logo.png" alt={siteInfo.name} />
        </Link>

        <button
          className={`navbar-toggle ${open ? 'open' : ''}`}
          onClick={() => setOpen(!open)}
          aria-label="Abrir menu"
        >
          <span />
          <span />
          <span />
        </button>

        <nav className={`navbar-nav ${open ? 'open' : ''}`}>
          {navLinks.map((link) => (
            <NavLink
              key={link.to}
              to={link.to}
              end={link.to === '/'}
              onClick={() => setOpen(false)}
            >
              {link.label}
            </NavLink>
          ))}
        </nav>
      </div>
    </header>
  );
}
