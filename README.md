# VenderMeuImovel - Landing + Admin sem framework

Projeto em PHP + MySQL + JS/CSS/HTML, sem framework, com área administrativa para editar o conteúdo da landing.

## Estrutura

- `public/` frontend público (`index.php`, assets e uploads)
- `app/config/db.php` conexão PDO e leitura de `.env`
- `app/helpers/` autenticação, CSRF, sanitização, upload
- `app/models/` acesso a dados (`Settings`, `Pages`, `Sections`, `Leads`)
- `app/views/` view da home e partials
- `admin/` login, dashboard e CRUDs
- `scripts/schema.sql` schema MySQL
- `scripts/seed.php` seed inicial

## Configuração

1. Requisitos: PHP 8.1+ e MySQL 8+
2. Crie um arquivo `.env` na raiz:

```ini
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=vendermeuimovel
DB_USER=root
DB_PASS=

ADMIN_NAME=Administrador
ADMIN_EMAIL=admin@local.test
ADMIN_PASSWORD=admin123
```

3. Crie o banco no MySQL:

```sql
CREATE DATABASE vendermeuimovel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

4. Rode a seed (cria tabelas + conteúdo inicial):

```bash
php scripts/seed.php
```

5. Suba o servidor embutido apontando para `public/`:

```bash
php -S localhost:8000 -t public
```

## Acesso

- Site: `http://localhost:8000`
- Admin login: `http://localhost:8000/admin/login.php`
- Credenciais padrão: definidas em `.env` (`ADMIN_EMAIL` / `ADMIN_PASSWORD`)

## Funcionalidades de Admin

- Login seguro com `password_hash` / `password_verify`
- Proteção CSRF em formulários POST
- Dashboard
- CRUD de settings (`key/value`)
- CRUD de páginas
- CRUD de seções e items
- Upload de mídia para `public/uploads`
- Leads: listagem e exportação CSV

