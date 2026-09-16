Este repositório contém dois projetos AutoLux:
  - raiz/            Projeto final de Frontend (React + Vite) - descrito abaixo
  - autolux-backend/ Projeto final de Backend (PHP + MySQL + Node.js) - ver autolux-backend/README.txt

===========================================================================

AutoLux - Projeto Final Frontend com React

Aluno: Pedro Castel-Branco com algumas ajudas do ChatGPT principalmente com a consola pois deu montes de erros quando se fez npm install
npm run dev
npm run build
Projeto: AutoLux - Stand de automóveis online

Descrição:
Este projeto é uma aplicação React criada com Vite para um stand automóvel online. Permite consultar um catálogo de viaturas, aplicar filtros, abrir a página de detalhe de cada veículo, adicionar/remover favoritos e simular um pedido de contacto.

Tecnologias utilizadas:
- React
- Vite
- React Router DOM
- Context API
- CSS puro organizado
- Fetch API

Fonte de dados utilizada:
Ficheiro JSON local.
Os dados das viaturas estão em public/data/vehicles.json e são carregados com fetch().

Funcionalidades implementadas:
- Página inicial com banner, pesquisa rápida e 4 viaturas em destaque.
- Catálogo com listagem de viaturas em cards reutilizáveis.
- Filtros por marca, combustível, preço máximo, ano mínimo e pesquisa por marca/modelo.
- Página de detalhe com rota dinâmica /veiculo/:id.
- Sistema de favoritos com Context API.
- Contador de favoritos visível na navbar em todas as páginas.
- Página de favoritos com opção de remover veículos e limpar a lista.
- Formulário de contacto com campos obrigatórios, validação de email e seleção de uma ou mais viaturas.
- Botão Pedir Contacto na página de detalhe que pré-seleciona a viatura no formulário.
- Página Sobre Nós estática.
- Página 404 para rotas inexistentes.
- Layout responsivo.

Estrutura principal:
src/components - Componentes reutilizáveis
src/pages - Páginas da aplicação
src/context - Context API dos favoritos
src/hooks - Hook de carregamento das viaturas
src/styles - Ficheiro CSS global
public/data - Ficheiro JSON local com 10 viaturas

Como correr o projeto:
1. Instalar dependências:
   npm install

2. Iniciar o servidor de desenvolvimento:
   npm run dev

3. Abrir o endereço indicado no terminal, normalmente:
   http://localhost:5173

Como criar versão de produção:
npm run build

Notas:
O projeto foi preparado para cumprir os requisitos técnicos: componentes funcionais, props, useState, useEffect, Context API, React Router, rota dinâmica, fetch, JSX com map/key, CSS organizado e estrutura de pastas clara.


Nota: os dados do ficheiro vehicles.json foram atualizados com a lista fornecida pelo professor, incluindo os campos potencia, caixa, cor e destaque.
