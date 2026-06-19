import { formatCurrency, formatKm } from '../utils/formatters.js';

function ComparePanel({ vehicles, onRemove, onClear }) {
  return (
    <section className="section compare-section" id="comparar">
      <div className="section-heading with-action">
        <div>
          <p className="eyebrow">Comparador</p>
          <h2>Compare até 3 viaturas</h2>
          <p>Veja preço, ano, quilómetros, potência e combustível numa grelha simples.</p>
        </div>
        {vehicles.length > 0 && (
          <button className="button button-ghost" type="button" onClick={onClear}>
            Limpar comparação
          </button>
        )}
      </div>

      {vehicles.length === 0 ? (
        <div className="empty-state">
          <h3>Nenhuma viatura selecionada.</h3>
          <p>Use o botão "Comparar" nos cards do catálogo para adicionar veículos aqui.</p>
        </div>
      ) : (
        <div className="compare-grid">
          {vehicles.map((vehicle) => (
            <article className="compare-card" key={vehicle.id}>
              <img src={vehicle.imagem} alt={`${vehicle.marca} ${vehicle.modelo}`} loading="lazy" />
              <div>
                <p className="eyebrow">{vehicle.marca}</p>
                <h3>{vehicle.modelo}</h3>
              </div>
              <ul>
                <li>
                  <span>Preço</span>
                  <strong>{formatCurrency(vehicle.preco)}</strong>
                </li>
                <li>
                  <span>Ano</span>
                  <strong>{vehicle.ano}</strong>
                </li>
                <li>
                  <span>Km</span>
                  <strong>{formatKm(vehicle.km)}</strong>
                </li>
                <li>
                  <span>Potência</span>
                  <strong>{vehicle.potencia}</strong>
                </li>
                <li>
                  <span>Combustível</span>
                  <strong>{vehicle.combustivel}</strong>
                </li>
              </ul>
              <button className="button button-small button-ghost" type="button" onClick={() => onRemove(vehicle.id)}>
                Remover
              </button>
            </article>
          ))}
        </div>
      )}
    </section>
  );
}

export default ComparePanel;
