import { useEffect, useState } from 'react';
import { heroSlides } from '../data/content';

export default function HeroSlider() {
  const [current, setCurrent] = useState(0);

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrent((prev) => (prev + 1) % heroSlides.length);
    }, 5000);
    return () => clearInterval(timer);
  }, []);

  return (
    <section className="hero">
      <div className="hero-bg">
        <img src="/images/hero.jpg" alt="Flavors Restaurante" />
        <div className="hero-overlay" />
      </div>

      <div className="hero-content">
        <h1 className="hero-title">
          {heroSlides.map((slide, i) => (
            <span key={slide} className={i === current ? 'active' : ''}>
              {slide}
            </span>
          ))}
        </h1>
        <p className="hero-subtitle">
          No Flavors Restaurante, convidamo-lo a explorar a genuína gastronomia portuguesa
          situado em Palmela, na Quinta do Anjo.
        </p>
      </div>

      <div className="hero-dots">
        {heroSlides.map((_, i) => (
          <button
            key={i}
            className={i === current ? 'active' : ''}
            onClick={() => setCurrent(i)}
            aria-label={`Slide ${i + 1}`}
          />
        ))}
      </div>
    </section>
  );
}
