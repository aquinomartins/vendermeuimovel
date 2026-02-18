# Aurora Imóveis (PHP + MySQL)

Home pública baseada no layout de `site1/index.html`, com conteúdo dinâmico via MySQL e painel Admin para edição.

## Estrutura

- `public/index.php`: Home pública (layout Aurora)
- `public/css/styles.css`: CSS base copiado do `site1`
- `public/js/main.js`: carrega dados em `/api/home-data.php`
- `public/uploads/`: imagens
- `api/home-data.php`: JSON para chips, métricas, cards, depoimentos e pins
- `admin/`: painel para login e edição de conteúdo
- `scripts/schema.sql`: schema do banco
- `scripts/seed.php`: seed inicial

## Configuração

1. Configure credenciais MySQL via `.env` (ou variáveis de ambiente):

```ini
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=vendermeuimovel
DB_USER=root
DB_PASS=
```

2. Execute seed:

```bash
php scripts/seed.php
```

3. Rode o servidor PHP apontando para `public/`:

```bash
php -S 0.0.0.0:8080 -t public
```

## Acesso Admin

- URL: `/admin/login.php`
- Usuário padrão: `admin@aurora.local`
- Senha padrão: `123456`

## Segurança implementada

- `password_hash` e `password_verify`
- `session_regenerate_id` no login
- CSRF em todos os POST do admin
- Prepared statements (PDO)
- Upload com validação MIME real + nome por hash SHA-256
