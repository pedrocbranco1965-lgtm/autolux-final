import { useEffect, useMemo, useState } from 'react';
import ComparePanel from './components/ComparePanel.jsx';
import ContactForm from './components/ContactForm.jsx';
import FilterPanel from './components/FilterPanel.jsx';
import Header from './components/Header.jsx';
import VehicleCard from './components/VehicleCard.jsx';
import { vehicles } from './data/vehicles.js';
import { formatCurrency, pluralize } from './utils/formatters.js';

const initialFilters = {
  search: '',
  brand: '',
  fuel: '',
  gearbox: '',
  maxPrice: '',
  sort: 'featured'
};

function readStoredFavorites() {
  try {
    return JSON.parse(localStorage.getItem('autolux-pro-favorites')) || [];
  } catch {
    return [];
  }
}

function sortVehicles(list, sortMode) {
  const sorted = [...list];

  switch (sortMode) {
    case 'price-asc':
      return sorted.sort((a, b) => a.preco - b.preco);
    case 'price-desc':
      return sorted.sort((a, b) => b.preco - a.preco);
    case 'year-desc':
      return sorted.sort((a, b) => b.ano - a.ano);
    case 'km-asc':
      return sorted.sort((a, b) => a.km - b.km);
    default:
      return sorted.sort((a, b) => Number(b.destaque) - Number(a.destaque) || a.preco - b.preco);
  }
}

function App() {
  const [filters, setFilters] = useState(initialFilters);
  const [favoriteIds, setFavoriteIds] = useState(readStoredFavorites);
  const [compareIds, setCompareIds] = useState([]);
  const [contactRequest, setContactRequest] = useState({ vehicleId: '', requestId: 0 });

  useEffect(() => {
    localStorage.setItem('autolux-pro-favorites', JSON.stringify(favoriteIds));
  }, [favoriteIds]);

  const options = useMemo(
    () => ({
      brands: [...new Set(vehicles.map((vehicle) => vehicle.marca))].sort(),
      fuels: [...new Set(vehicles.map((vehicle) => vehicle.combustivel))].sort(),
      gearboxes: [...new Set(vehicles.map((vehicle) => vehicle.caixa))].sort()
    }),
    []
  );

  const filteredVehicles = useMemo(() => {
    const normalizedSearch = filters.search.trim().toLowerCase();
    const filtered = vehicles.filter((vehicle) => {
      const searchableText = `${vehicle.marca} ${vehicle.modelo} ${vehicle.cor} ${vehicle.etiqueta}`.toLowerCase();
      const matchesSearch = !normalizedSearch || searchableText.includes(normalizedSearch);
      const matchesBrand = !filters.brand || vehicle.marca === filters.brand;
      const matchesFuel = !filters.fuel || vehicle.combustivel === filters.fuel;
      const matchesGearbox = !filters.gearbox || vehicle.caixa === filters.gearbox;
      const matchesPrice = !filters.maxPrice || vehicle.preco <= Number(filters.maxPrice);

      return matchesSearch && matchesBrand && matchesFuel && matchesGearbox && matchesPrice;
    });

    return sortVehicles(filtered, filters.sort);
  }, [filters]);

  const favoriteVehicles = useMemo(
    () => vehicles.filter((vehicle) => favoriteIds.includes(vehicle.id)),
    [favoriteIds]
  );

  const comparedVehicles = useMemo(
    () => vehicles.filter((vehicle) => compareIds.includes(vehicle.id)),
    [compareIds]
  );

  const stats = useMemo(() => {
    const averagePrice = vehicles.reduce((sum, vehicle) => sum + vehicle.preco, 0) / vehicles.length;
    const electrifiedCount = vehicles.filter((vehicle) => vehicle.combustivel.includes('Híbrido') || vehicle.combustivel === 'Elétrico').length;
    const lowestKm = Math.min(...vehicles.map((vehicle) => vehicle.km));

    return [
      { label: 'Viaturas disponíveis', value: vehicles.length },
      { label: 'Preço médio', value: formatCurrency(averagePrice) },
      { label: 'Eletrificadas', value: electrifiedCount },
      { label: 'Menor quilometragem', value: `${lowestKm.toLocaleString('pt-PT')} km` }
    ];
  }, []);

  function toggleFavorite(vehicleId) {
    setFavoriteIds((currentIds) =>
      currentIds.includes(vehicleId) ? currentIds.filter((id) => id !== vehicleId) : [...currentIds, vehicleId]
    );
  }

  function toggleCompare(vehicleId) {
    setCompareIds((currentIds) => {
      if (currentIds.includes(vehicleId)) {
        return currentIds.filter((id) => id !== vehicleId);
      }

      if (currentIds.length >= 3) {
        return currentIds;
      }

      return [...currentIds, vehicleId];
    });
  }

  function requestContact(vehicleId) {
    setContactRequest((currentRequest) => ({
      vehicleId,
      requestId: currentRequest.requestId + 1
    }));
    document.getElementById('contacto')?.scrollIntoView({ behavior: 'smooth' });
  }

  return (
    <div className="app-shell">
      <Header favoriteCount={favoriteIds.length} />

      <main>
        <section className="hero" id="inicio">
          <div className="hero-copy">
            <p className="eyebrow">Nova aplicação AutoLux</p>
            <h1>Um stand automóvel mais moderno, rápido e interativo.</h1>
            <p>
              Esta versão mantém a ideia do projeto original, mas melhora a pesquisa, os favoritos,
              a comparação e a apresentação visual.
            </p>
            <div className="hero-actions">
              <a className="button" href="#catalogo">
                Explorar catálogo
              </a>
              <a className="button button-ghost" href="#comparar">
                Ver comparador
              </a>
            </div>
          </div>

          <div className="hero-panel" aria-label="Resumo do catálogo">
            {stats.map((stat) => (
              <div className="stat-card" key={stat.label}>
                <span>{stat.label}</span>
                <strong>{stat.value}</strong>
              </div>
            ))}
          </div>
        </section>

        <section className="section catalog-section" id="catalogo">
          <div className="section-heading with-action">
            <div>
              <p className="eyebrow">Catálogo</p>
              <h2>Escolha a próxima viatura</h2>
              <p>Filtre por marca, combustível, caixa, preço e ordene os resultados em tempo real.</p>
            </div>
            <p className="results-pill">
              {filteredVehicles.length} {pluralize(filteredVehicles.length, 'resultado', 'resultados')}
            </p>
          </div>

          <FilterPanel
            filters={filters}
            options={options}
            onChange={setFilters}
            onReset={() => setFilters(initialFilters)}
          />

          {filteredVehicles.length === 0 ? (
            <div className="empty-state">
              <h3>Não foram encontradas viaturas.</h3>
              <p>Experimente remover filtros ou pesquisar por outro termo.</p>
            </div>
          ) : (
            <div className="vehicle-grid">
              {filteredVehicles.map((vehicle) => (
                <VehicleCard
                  key={vehicle.id}
                  vehicle={vehicle}
                  isFavorite={favoriteIds.includes(vehicle.id)}
                  isCompared={compareIds.includes(vehicle.id)}
                  canAddToCompare={compareIds.length < 3}
                  onToggleFavorite={toggleFavorite}
                  onToggleCompare={toggleCompare}
                  onRequestContact={requestContact}
                />
              ))}
            </div>
          )}
        </section>

        <ComparePanel
          vehicles={comparedVehicles}
          onRemove={(vehicleId) => setCompareIds((currentIds) => currentIds.filter((id) => id !== vehicleId))}
          onClear={() => setCompareIds([])}
        />

        <section className="section favorites-section" id="favoritos">
          <div className="section-heading">
            <p className="eyebrow">Favoritos persistentes</p>
            <h2>Favoritos guardados no browser</h2>
            <p>Os favoritos ficam guardados em localStorage mesmo depois de fechar a página.</p>
          </div>

          {favoriteVehicles.length === 0 ? (
            <div className="empty-state">
              <h3>Ainda não tem favoritos.</h3>
              <p>Use a estrela nos cards do catálogo para guardar viaturas.</p>
            </div>
          ) : (
            <div className="favorite-list">
              {favoriteVehicles.map((vehicle) => (
                <a key={vehicle.id} href={`#viatura-${vehicle.id}`}>
                  {vehicle.marca} {vehicle.modelo}
                  <span>{formatCurrency(vehicle.preco)}</span>
                </a>
              ))}
            </div>
          )}
        </section>

        <ContactForm
          vehicles={vehicles}
          selectedVehicleId={contactRequest.vehicleId}
          requestId={contactRequest.requestId}
        />
      </main>

      <footer className="site-footer">
        <div>
          <strong>AutoLux Pro</strong>
          <p>Nova aplicação de demonstração para curso de Front-End.</p>
        </div>
        <a href="#inicio">Voltar ao topo</a>
      </footer>
    </div>
  );
}

export default App;
