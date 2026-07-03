import { menuData, menuNote } from '../data/menu';

export default function Menu() {
  return (
    <>
      <section className="page-hero">
        <div className="container">
          <h1>Menu</h1>
          <p className="menu-note">{menuNote}</p>
        </div>
      </section>

      <section className="section menu-section">
        <div className="container">
          {menuData.map((category) => (
            <div key={category.id} className="menu-category" id={category.id}>
              <h2 className="menu-category-title">{category.title}</h2>
              {category.subtitle && <p className="menu-category-subtitle">{category.subtitle}</p>}
              <ul className="menu-list">
                {category.items.map((item) => (
                  <li key={item.name} className="menu-item">
                    <div className="menu-item-info">
                      <h3>{item.name}</h3>
                      {item.description && <p>{item.description}</p>}
                    </div>
                    <span className="menu-item-price">{item.price} €</span>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>
      </section>
    </>
  );
}
