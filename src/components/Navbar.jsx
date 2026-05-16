import { NavLink, Link } from 'react-router-dom';
import { useFavorites } from '../context/FavoritesContext.jsx';

function Navbar() {
  const { favorites } = useFavorites();

  return (
    <header className="navbar">
      <Link to="/" className="brand" aria-label="AutoLux página inicial">
        <span className="brand-mark">AL</span>
        <span>AutoLux</span>
      </Link>

      <nav className="nav-links" aria-label="Menu principal">
        <NavLink to="/">Início</NavLink>
        <NavLink to="/catalogo">Catálogo</NavLink>
        <NavLink to="/favoritos">Favoritos <span className="badge">{favorites.length}</span></NavLink>
        <NavLink to="/contacto">Contacto</NavLink>
        <NavLink to="/sobre">Sobre</NavLink>
      </nav>
    </header>
  );
}

export default Navbar;
