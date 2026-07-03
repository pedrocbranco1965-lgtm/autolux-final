import { siteInfo } from '../data/site';

export default function Events() {
  return (
    <>
      <section className="page-hero">
        <div className="container">
          <h1>Flavors Eventos</h1>
          <p>Casamentos, batizados, eventos corporativos ou eventos sociais</p>
        </div>
      </section>

      <section className="section">
        <div className="container narrow">
          <p className="lead">
            Uma referência para eventos em Palmela com uma oferta diferenciada. O Flavors é o espaço
            onde se celebram os grandes momentos da vida. Organiza e recebe a produção de eventos
            corporativos, sociais e celebrações mais intimistas, promovendo a partilha de
            experiências, emoções e momentos memoráveis: casamentos, aniversários, batizados ou
            outras ocasiões especiais, bem como festas empresariais.
          </p>
        </div>
      </section>

      <section className="section events-grid-section">
        <div className="container events-grid">
          <article className="event-card">
            <img src="/images/hero.jpg" alt="Casamentos e batizados" />
            <div>
              <h2>Faça o seu evento</h2>
              <p>
                Quer se trate de um casamento ou de um batizado, torne os seus sonhos uma realidade.
                Temos uma equipa profissional e com experiência que estará ao seu dispor para ajudá-lo
                a criar o melhor dia da sua vida ou dos seus familiares.
              </p>
            </div>
          </article>

          <article className="event-card">
            <img src="/images/sala.jpg" alt="Eventos corporativos" />
            <div>
              <h2>Eventos Corporativos</h2>
              <p>
                Caso se trate de eventos corporativos, temos o cenário perfeito para os seus eventos.
                Ajudamos a tornar o seu evento único, seja o lançamento de um produto, o aniversário
                da sua empresa, uma grande conferência ou apenas um pequeno encontro.
              </p>
            </div>
          </article>

          <article className="event-card">
            <img src="/images/caril.jpg" alt="Eventos sociais" />
            <div>
              <h2>Eventos Sociais</h2>
              <p>
                A nossa experiência e atenção ao detalhe irá ajudá-lo a criar um evento
                personalizado e original. Desde cocktails privados a festas de aniversário, jantares
                e festas temáticas — não há limites para a nossa criatividade e para a vossa
                imaginação.
              </p>
            </div>
          </article>
        </div>
      </section>

      <section className="section cta-banner">
        <div className="container">
          <h2>Veja e faça a sua solicitação em casamentos.pt</h2>
          <a
            href={siteInfo.casamentos}
            target="_blank"
            rel="noopener noreferrer"
            className="btn btn-primary"
          >
            Visitar casamentos.pt
          </a>
        </div>
      </section>
    </>
  );
}
