# DEVELOPMENT

## Comandos

```bash
composer test
composer lint
composer coverage
composer coverage:check
composer migrate
composer smoke
composer benchmark
```

Alternativa recomendada para evitar warnings deprecados de Composer de sistema:

```bash
./scripts/composerw test
./scripts/composerw lint
./scripts/composerw coverage
./scripts/composerw coverage:check
./scripts/composerw migrate
./scripts/composerw smoke
```

## Tests por suite

```bash
vendor/bin/phpunit --configuration phpunit.xml --testsuite unit
vendor/bin/phpunit --configuration phpunit.xml --testsuite integration
```

Las pruebas de integración usan MySQL local y esperan esquema aplicado.

## Migraciones

- Las migraciones SQL se almacenan en `migrations/NNNN_descripcion.sql`.
- `composer migrate` crea/usa la tabla `schema_migrations`.
- Una migración ya aplicada se omite automáticamente.
- Si cambia el contenido de una migración ya aplicada, el runner falla por checksum para evitar drift silencioso.
- Variables opcionales del runner: `CABIFY_ENV_FILE` y `CABIFY_MIGRATIONS_PATH`.

## Arranque local

```bash
php -S 127.0.0.1:9091 -t public
```

## Smoke test manual

```bash
curl -i http://127.0.0.1:9091/status

curl -i -X PUT http://127.0.0.1:9091/cars \
  -H 'Content-Type: application/json' \
  -d '[{\"id\":1,\"seats\":4},{\"id\":2,\"seats\":6}]'

curl -i -X POST http://127.0.0.1:9091/journey \
  -H 'Content-Type: application/json' \
  -d '{\"id\":7,\"people\":4}'

curl -i -X POST http://127.0.0.1:9091/dropoff \
  -H 'Content-Type: application/json' \
  -d '{\"id\":7}'

curl -i -X POST http://127.0.0.1:9091/locate \
  -H 'Content-Type: application/json' \
  -d '{\"id\":7}'
```

## Benchmark local (carga)

```bash
composer benchmark
composer benchmark -- --cars=2000 --groups=20000 --max-group-size=4
```

## Cobertura

```bash
composer coverage
composer coverage:check
```

- Reporte Clover: `build/coverage/clover.xml`
- Umbral mínimo actual: `90%`

## Smoke test HTTP

Con servidor levantado en `APP_PORT`:

```bash
composer smoke
```

O indicando URL base explícita:

```bash
bash scripts/smoke.sh http://127.0.0.1:9091
```

## Make targets

```bash
make test
make lint
make coverage
make coverage-check
make migrate
make smoke
make benchmark
```

## Integración continua

- Workflow: `.github/workflows/ci.yml`
- Trigger: `push` a `main/master` y `pull_request`
- Entorno: PHP 8.4 + MySQL 8.4
- Pasos: generar `.env` de CI, `./scripts/composerw migrate`, `./scripts/composerw lint`, `./scripts/composerw test`, `./scripts/composerw coverage`, `./scripts/composerw coverage:check`, `./scripts/composerw smoke`

## Estructura

Se sigue el árbol hexagonal definido en `AGENTS.md`.
