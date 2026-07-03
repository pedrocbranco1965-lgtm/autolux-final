import { Link } from 'react-router-dom';
import HeroSlider from '../components/HeroSlider';
import SectionCard from '../components/SectionCard';
import Testimonials from '../components/Testimonials';
import Partners from '../components/Partners';

export default function Home() {
  return (
    <>
      <HeroSlider />

      <section className="section intro">
        <div className="container narrow">
          <p className="lead">
            Proporcionamos uma experiência culinária onde a autenticidade da cozinha portuguesa se
            reinventa num cenário de beleza natural — o Espaço Fortuna Artes e Ofícios na Serra do
            Louro.
          </p>
        </div>
      </section>

      <SectionCard
        title="A Criatividade do Flavors"
        image="/images/caril.jpg"
        imageAlt="Caril de lulas e camarão"
        text={`Pleno de criatividade, o Flavors revela-se também na vontade de explorar pratos fora do mapa, para o sabor do dia a dia, de ingredientes frescos e sazonais. Deixe levar-se pelo melhor do mar e da terra, confecionado ao melhor estilo de cozinha de tacho.

Rico em propostas originais e sofisticadas, o menu Flavors tem a assinatura de quem andou um pouco por toda a parte, para nos trazer sugestões do outro mundo. A dedicação e a experiência do chef Pedro Castel-Branco servem de guia nesta variada geografia de sabores.`}
        linkTo="/menu"
        linkLabel="Ver Menu"
      />

      <section className="section cta-banner">
        <div className="container">
          <h2>Embarque nesta viagem</h2>
          <p>
            Para embarcar nesta viagem conduzida pela equipa do Flavors, e em que cada prato é um
            momento de degustação único, poderá fazer a sua reserva online ou através do nosso
            telefone ou e-mail.
          </p>
          <Link to="/reservas" className="btn btn-primary">
            Fazer Reserva
          </Link>
        </div>
      </section>

      <SectionCard
        title="A descoberta da experiência"
        image="/images/sala.jpg"
        imageAlt="Sala do restaurante Flavors"
        reverse
        text={`O Flavors convida-o a descobrir uma experiência gastronómica única onde a calma e serenidade estão presentes.

No coração da Serra do Louro em Palmela, Quinta do Anjo, localizado no Espaço Fortuna Artes e Ofícios, o Flavors convida-o a descobrir uma experiência gastronómica única.`}
        linkTo="/sobre"
        linkLabel="Sobre Nós"
      />

      <SectionCard
        title="Refúgio à agitação"
        image="/images/hero.jpg"
        imageAlt="Ambiente Flavors"
        text={`Espaço bastante agradável e calmo — o local ideal para quem pretende um refúgio à agitação diária. O interior apresenta uma decoração cuidada e simples, reveladora de bom gosto, misturando o clássico e o rústico em perfeita harmonia, criando um ambiente elegante e descontraído onde a calma e o sossego convidam a ir ficando.`}
      />

      <SectionCard
        title="Servimos emoções"
        image="/images/alheira.jpg"
        imageAlt="Alheira tradicional"
        reverse
        text={`No Flavors procuramos servir mais do que uma refeição — servimos emoções, identidade, conhecimento, resiliência, intensidade. Procuramos desenvolver uma cozinha verdadeira sem peneiras nem artifícios, que é também uma consequência das nossas experiências e do nosso passado.`}
      />

      <SectionCard
        title="Vinhos da Península de Setúbal"
        image="/images/bacalhau.jpg"
        imageAlt="Prato do Flavors"
        text={`A Carta de vinhos tem uma oferta cuidada e variada de alguns dos melhores vinhos da região da Península de Setúbal, mas é uma carta criada para a cozinha que se serve no Flavors.

Para além de um leque de rótulos incontornáveis dos produtores mais relevantes, a Carta do Flavors conta também com muitas surpresas e ilustres desconhecidos. Destaque para a enorme qualidade do vinho a copo e do vinho da casa com um serviço personalizado de qualidade.`}
        linkTo="/menu"
        linkLabel="Ver Carta de Vinhos"
      />

      <SectionCard
        title="Inspiração de sabores"
        image="/images/gyosas.webp"
        imageAlt="Gyosas Flavors"
        reverse
        text={`Cada prato é uma inspiração de sabores — a nossa culinária transcende a mera preparação de alimentos, revelando-se numa expressão apaixonada e profissional que eleva cada sabor e cada prato a uma experiência excecional e única.`}
      />

      <Testimonials />
      <Partners />

      <section className="section reservation-cta">
        <div className="container">
          <h2>Reservas — solicite uma mesa</h2>
          <p>Introduza os seus dados e faremos o possível para escolher a melhor mesa para si.</p>
          <Link to="/reservas" className="btn btn-primary">
            Reservar Agora
          </Link>
        </div>
      </section>
    </>
  );
}
