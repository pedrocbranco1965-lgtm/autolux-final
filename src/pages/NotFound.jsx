import { Link } from 'react-router-dom';

export default function NotFound() {
  return (
    <section className="section not-found">
      <div className="container narrow" style={{ textAlign: 'center' }}>
        <h1>404</h1>
        <p>Página não encontrada.</p>
        <Link to="/" className="btn btn-primary">
          Voltar à Home
        </Link>
      </div>
    </section>
  );
}
