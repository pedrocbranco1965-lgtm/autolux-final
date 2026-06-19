function FilterPanel({ filters, options, onChange, onReset }) {
  function updateFilter(event) {
    const { name, value } = event.target;
    onChange({ ...filters, [name]: value });
  }

  return (
    <section className="filter-panel" aria-label="Filtros do catálogo">
      <label>
        Pesquisa
        <input
          type="search"
          name="search"
          value={filters.search}
          placeholder="Marca, modelo ou cor"
          onChange={updateFilter}
        />
      </label>

      <label>
        Marca
        <select name="brand" value={filters.brand} onChange={updateFilter}>
          <option value="">Todas</option>
          {options.brands.map((brand) => (
            <option key={brand} value={brand}>
              {brand}
            </option>
          ))}
        </select>
      </label>

      <label>
        Combustível
        <select name="fuel" value={filters.fuel} onChange={updateFilter}>
          <option value="">Todos</option>
          {options.fuels.map((fuel) => (
            <option key={fuel} value={fuel}>
              {fuel}
            </option>
          ))}
        </select>
      </label>

      <label>
        Caixa
        <select name="gearbox" value={filters.gearbox} onChange={updateFilter}>
          <option value="">Todas</option>
          {options.gearboxes.map((gearbox) => (
            <option key={gearbox} value={gearbox}>
              {gearbox}
            </option>
          ))}
        </select>
      </label>

      <label>
        Preço máximo
        <select name="maxPrice" value={filters.maxPrice} onChange={updateFilter}>
          <option value="">Sem limite</option>
          <option value="20000">Até 20.000 €</option>
          <option value="30000">Até 30.000 €</option>
          <option value="40000">Até 40.000 €</option>
        </select>
      </label>

      <label>
        Ordenar por
        <select name="sort" value={filters.sort} onChange={updateFilter}>
          <option value="featured">Destaques primeiro</option>
          <option value="price-asc">Preço mais baixo</option>
          <option value="price-desc">Preço mais alto</option>
          <option value="year-desc">Ano mais recente</option>
          <option value="km-asc">Menos quilómetros</option>
        </select>
      </label>

      <button className="button button-ghost" type="button" onClick={onReset}>
        Limpar filtros
      </button>
    </section>
  );
}

export default FilterPanel;
