import { formatCurrency, formatKm } from '../utils/formatters.js';

function VehicleCard({
  vehicle,
  isFavorite,
  isCompared,
  canAddToCompare,
  onToggleFavorite,
  onToggleCompare,
  onRequestContact
}) {
  return (
    <article className="vehicle-card" id={`viatura-${vehicle.id}`}>
      <div className="vehicle-media">
        <img src={vehicle.imagem} alt={`${vehicle.marca} ${vehicle.modelo}`} loading="lazy" />
        {vehicle.destaque && <span className="tag tag-highlight">Destaque</span>}
        <span className="tag tag-soft">{vehicle.etiqueta}</span>
      </div>

      <div className="vehicle-body">
        <div className="vehicle-title">
          <div>
            <p className="eyebrow">{vehicle.marca}</p>
            <h3>{vehicle.modelo}</h3>
          </div>
          <button
            className={`icon-button ${isFavorite ? 'is-active' : ''}`}
            type="button"
            onClick={() => onToggleFavorite(vehicle.id)}
            aria-label={isFavorite ? 'Remover dos favoritos' : 'Adicionar aos favoritos'}
          >
            {isFavorite ? '★' : '☆'}
          </button>
        </div>

        <p className="vehicle-description">{vehicle.descricao}</p>

        <dl className="spec-grid">
          <div>
            <dt>Ano</dt>
            <dd>{vehicle.ano}</dd>
          </div>
          <div>
            <dt>Km</dt>
            <dd>{formatKm(vehicle.km)}</dd>
          </div>
          <div>
            <dt>Combustível</dt>
            <dd>{vehicle.combustivel}</dd>
          </div>
          <div>
            <dt>Caixa</dt>
            <dd>{vehicle.caixa}</dd>
          </div>
        </dl>

        <div className="vehicle-footer">
          <strong>{formatCurrency(vehicle.preco)}</strong>
          <div className="card-actions">
            <button
              className="button button-small button-ghost"
              type="button"
              disabled={!isCompared && !canAddToCompare}
              onClick={() => onToggleCompare(vehicle.id)}
            >
              {isCompared ? 'Retirar' : 'Comparar'}
            </button>
            <button className="button button-small" type="button" onClick={() => onRequestContact(vehicle.id)}>
              Contacto
            </button>
          </div>
        </div>
      </div>
    </article>
  );
}

export default VehicleCard;
