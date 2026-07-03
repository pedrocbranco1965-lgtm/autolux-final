Flavors Restaurante — Site estático
====================================

Réplica do site https://www.flavors-restaurante.pt, criada para alojamento independente
(sem dependência do Wix).

Tecnologias
-----------
- React + Vite
- React Router
- CSS puro (sem frameworks)

Páginas
-------
- Home (/)
- Sobre Nós (/sobre)
- Menu (/menu)
- Eventos (/eventos)
- Reservas (/reservas)
- Blog (/blog)

Desenvolvimento local
---------------------
npm install
npm run dev

Build para produção
-------------------
npm run build

O resultado fica em dist/ — pode ser alojado em qualquer servidor estático:
- Netlify, Vercel, GitHub Pages
- Apache / Nginx
- Qualquer hosting com suporte a ficheiros estáticos

Para Nginx, configure fallback para index.html nas rotas do React Router.

Reservas
--------
O formulário de reservas abre o cliente de email com os dados preenchidos.
Para reservas online automáticas, pode integrar:
- TheFork (já referenciado no site)
- Um backend próprio com API de reservas
- Serviços como Cal.com ou similar

Imagens
-------
As imagens foram descarregadas do site original e estão em public/images/.
Pode substituí-las pelas suas versões em alta resolução.

Contactos do restaurante
------------------------
Telefone: +351 912 323 847
Email: flavors.geral@gmail.com
Morada: Espaço Fortuna Artes e Ofícios, Estrada Nacional, Quinta do Anjo, Palmela
