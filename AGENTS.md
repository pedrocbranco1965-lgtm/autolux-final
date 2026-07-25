# AGENTS.md

## Cursor Cloud specific instructions

AutoLux is a single-page **Vite + React** frontend (car dealership catalog). There is no backend; vehicle data is loaded via `fetch()` from the static file `public/data/vehicles.json`.

- Start dev server: `npm run dev` (Vite, serves on `http://localhost:5173/`). See `README.txt` for feature overview (Portuguese).
- Build: `npm run build`; preview a build with `npm run preview`.
- There are **no lint or automated test scripts** configured in `package.json`.
- Dependencies in `package.json` are pinned to `latest`, so `package-lock.json` is the source of truth; use `npm install` (npm lockfile) rather than another package manager.
- Routing uses `react-router-dom` (dynamic route `/veiculo/:id`); favorites use the Context API in `src/context/FavoritesContext.jsx`.
