# habbo-gamedata

Reusable Habbo gamedata package for Laravel.

This package stores Habbo gamedata in your database, imports source files using Artisan commands, and serves hashed endpoints for client consumption with long-lived cache headers.

## Requirements

- PHP 8.3+
- Laravel 13.x (Illuminate 13.x components)

## Installation

```bash
composer require damienjolly/habbo-gamedata
```

The service provider is auto-discovered by Laravel.

## Configuration

Publish config (optional):

```bash
php artisan vendor:publish --tag=habbo-gamedata-config
```

Config file: `config/habbo-gamedata.php`

Default endpoint paths:

- `/gamedata/hashes`
- `/gamedata/external_flash_texts`
- `/gamedata/external_variables`
- `/gamedata/override/external_flash_override_texts`
- `/gamedata/productdata_xml`
- `/gamedata/figuredata`
- `/gamedata/furnidata_xml`

Set `routes.auto_register` to `false` if you want to register routes yourself.

## Database Migrations

Migrations are loaded automatically by the package service provider.

Run migrations:

```bash
php artisan migrate
```

If you want to copy migration files into your app:

```bash
php artisan vendor:publish --tag=habbo-gamedata-migrations
```

## Routes

Routes are loaded automatically when `routes.auto_register = true`.

To publish package routes:

```bash
php artisan vendor:publish --tag=habbo-gamedata-routes
```

## Import Commands

Each command accepts an optional `path` argument. If omitted, the default path under `storage/app/imports` is used.

- `php artisan gamedata:import-external-texts {path?}`
  - default: `storage/app/imports/external_flash_texts.txt`
- `php artisan gamedata:import-external-variables {path?}`
  - default: `storage/app/imports/external_variables.txt`
- `php artisan gamedata:import-external-override-texts {path?}`
  - default: `storage/app/imports/external_flash_override_texts.txt`
- `php artisan gamedata:import-product-data {path?}`
  - default: `storage/app/imports/productdata.xml`
- `php artisan gamedata:import-figure-data {path?}`
  - default: `storage/app/imports/figuredata.xml`
- `php artisan gamedata:import-furni-data {path?}`
  - default: `storage/app/imports/furnidata.xml`

All import commands clear relevant caches after successful import.

## Endpoint Behavior

All data endpoints use hash-based URLs.

- If the requested hash is missing or outdated, the endpoint responds with `302` to the latest hashed URL.
- Current hashed URLs are exposed via `/gamedata/hashes`.
- Responses include:
  - `Cache-Control: public, max-age=31536000, immutable`
  - `ETag: "<current-hash>"`

Content types:

- `external_flash_texts`, `external_variables`, `external_flash_override_texts`: `text/plain; charset=UTF-8`
- `productdata_xml`, `figuredata`, `furnidata_xml`: `application/xml; charset=UTF-8`

## Hashes Endpoint

`GET /gamedata/hashes` returns:

```json
{
  "hashes": [
    { "name": "furnidata", "url": "...", "hash": "..." },
    { "name": "productdata", "url": "...", "hash": "..." },
    { "name": "external_variables", "url": "...", "hash": "..." },
    { "name": "external_texts", "url": "...", "hash": "..." },
    { "name": "external_override_texts", "url": "...", "hash": "..." },
    { "name": "figurepartlist", "url": "...", "hash": "..." }
  ]
}
```

## Typical Flow

1. Import your source files with the Artisan commands.
2. Call `/gamedata/hashes` from your client/bootstrap service.
3. Use returned URLs/hashes to fetch and cache gamedata assets.
4. Re-import data when source files change.

## License

MIT
