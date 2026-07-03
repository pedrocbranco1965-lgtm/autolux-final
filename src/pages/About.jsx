export default function About() {
  return (
    <>
      <section className="page-hero">
        <div className="container">
          <h1>Sobre Nós</h1>
          <p>Em Palmela</p>
        </div>
      </section>

      <section className="section">
        <div className="container narrow">
          <p className="lead">
            No coração de Palmela, na natureza da Serra do Louro, entre a tradição e a serenidade da
            região, nasce o Flavors Restaurante em Palmela na Quinta do Anjo — um espaço dedicado à
            celebração da gastronomia portuguesa com elegância, autenticidade e alma.
          </p>
          <p>
            Mais do que um restaurante, o Flavors é o reflexo da paixão por cozinhar bem e receber
            melhor. Criámos um ambiente calmo, acolhedor e familiar, onde cada detalhe foi pensado
            para proporcionar momentos memoráveis à mesa. Aqui valorizamos a qualidade e a
            tranquilidade.
          </p>
        </div>
      </section>

      <section className="section about-highlight">
        <div className="container about-grid">
          <div>
            <h2>Reportagens SIC — Boa Cama Boa Mesa</h2>
            <p>Venha visitar Palmela</p>
            <p className="muted">Boa cama boa mesa — Martim Cabral</p>
          </div>
          <div className="about-image">
            <img src="/images/boa-cama.png" alt="Boa Cama Boa Mesa" />
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container about-split">
          <div className="about-image large">
            <img src="/images/sala.jpg" alt="Interior do Flavors Restaurante" />
          </div>
          <div>
            <h2>Raízes Portuguesas</h2>
            <p>
              A nossa cozinha inspira-se nas raízes portuguesas, combinando ingredientes frescos e
              locais com um toque contemporâneo e criativo. Cada prato é preparado com cuidado,
              respeitando a tradição e elevando os sabores que definem a nossa identidade.
            </p>
            <p>
              Seja para um almoço em família, um jantar especial ou a celebração de um evento
              marcante, o Flavors está preparado para tornar cada momento único e inesquecível. O
              nosso compromisso é com a qualidade, o serviço personalizado e o prazer de bem servir.
            </p>
          </div>
        </div>
      </section>

      <section className="section about-split reverse">
        <div className="container about-split">
          <div>
            <h2>Olaria — o Espaço Fortuna Artes e Ofícios</h2>
            <p>
              Instalado numa antiga olaria artesanal, o nosso espaço preserva a alma do passado e
              transforma-a num ambiente elegante, acolhedor e intimista. Cada canto conta uma
              história — e cada prato prolonga essa narrativa à mesa.
            </p>
            <p>
              A liderar a cozinha está o Chef Pedro Castel-Branco, cuja paixão pela gastronomia
              portuguesa e pela gastronomia oriental se reflete em cada criação. Com uma abordagem
              contemporânea, mas sempre respeitosa das raízes e ingredientes locais, o Chef propõe uma
              viagem de sabores cuidadosamente pensada para surpreender o paladar mais exigente. Uma
              viagem por Portugal, Índia, Japão e Tailândia.
            </p>
            <p>
              O reconhecimento chegou naturalmente: o Flavors já foi destacado por vários anos no
              prestigiado Guia da Boa Cama e Boa Mesa, e contou com várias reportagens no programa
              televisivo de mesmo nome, reforçando o nosso compromisso com a excelência, a
              autenticidade e o cuidado em cada detalhe.
            </p>
          </div>
          <div className="about-image large">
            <img src="/images/hero.jpg" alt="Espaço Fortuna Artes e Ofícios" />
          </div>
        </div>
      </section>

      <section className="section cta-banner">
        <div className="container">
          <p>
            Um almoço em família, um jantar romântico ou um evento especial — o Flavors oferece um
            ambiente calmo e requintado, ideal para criar memórias à volta da mesa.
          </p>
        </div>
      </section>
    </>
  );
}
