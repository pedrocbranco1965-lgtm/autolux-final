function Header({ favoriteCount }) {
  return (
    <header className="site-header">
      <a className="brand" href="#inicio" aria-label="AutoLux Pro página inicial">
        <span className="brand-mark">AL</span>
        <span>
          AutoLux
          <small>Pro</small>
        </span>
      </a>

      <nav className="nav-links" aria-label="Menu principal">
        <a href="#catalogo">Catálogo</a>
        <a href="#comparar">Comparar</a>
        <a href="#contacto">Contacto</a>
        <a href="#favoritos">Favoritos ({favoriteCount})</a>
      </nav>
    </header>
  );
}

export default Header;
