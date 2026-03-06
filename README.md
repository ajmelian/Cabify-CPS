# Cabify Car Pooling Service

Servicio de car pooling en PHP 8.4 nativo, arquitectura hexagonal (Ports & Adapters), MySQL con PDO y TDD con PHPUnit.

## Estado del proyecto

- Endpoints implementados: `GET /status`, `PUT /cars`, `POST /journey`, `POST /dropoff`, `POST /locate`.
- Persistencia MySQL con repositorios PDO y consultas parametrizadas.
- Migraciones SQL incrementales con registro interno en `schema_migrations`.
- Tests unitarios e integración activos.

## Quickstart

```bash
./scripts/composerw install
cp .env.example .env
./scripts/composerw migrate
./scripts/composerw test
php -S 127.0.0.1:9091 -t public
```

Opcional:

```bash
./scripts/composerw benchmark
./scripts/composerw smoke
```

## Instalación y arranque de la API

1. Instalar dependencias:

```bash
./scripts/composerw install
```

2. Crear configuración local:

```bash
cp .env.example .env
```

3. Editar `.env` con tus credenciales MySQL.

4. Aplicar migraciones:

```bash
./scripts/composerw migrate
```

5. Arrancar la API:

```bash
php -S 127.0.0.1:9091 -t public
```

6. Comprobar que está levantada:

```bash
curl -i http://127.0.0.1:9091/status
```

## Endpoints

### `GET /status`

Healthcheck del servicio.

```bash
curl -i http://127.0.0.1:9091/status
```

Respuesta esperada (`200`):

```json
{"status":"ok"}
```

### `PUT /cars`

Reemplaza la flota completa y resetea estado operativo.

```bash
curl -i -X PUT http://127.0.0.1:9091/cars \
  -H 'Content-Type: application/json' \
  -d '[{"id":1,"seats":4},{"id":2,"seats":6}]'
```

Respuesta esperada (`200`):

```json
{"status":"fleet_replaced"}
```

### `POST /journey`

Registra un grupo. Devuelve `200` si queda asignado o `202` si queda en espera.

```bash
curl -i -X POST http://127.0.0.1:9091/journey \
  -H 'Content-Type: application/json' \
  -d '{"id":101,"people":4}'
```

Respuesta ejemplo asignado (`200`):

```json
{"id":101,"status":"assigned","car_id":1}
```

Respuesta ejemplo en espera (`202`):

```json
{"id":103,"status":"waiting"}
```

### `POST /dropoff`

Finaliza trayecto de un grupo y libera plazas.

```bash
curl -i -X POST http://127.0.0.1:9091/dropoff \
  -H 'Content-Type: application/json' \
  -d '{"id":101}'
```

Respuesta esperada (`200`):

```json
{"id":101,"status":"dropped_off"}
```

### `POST /locate`

Localiza estado de un grupo.

```bash
curl -i -X POST http://127.0.0.1:9091/locate \
  -H 'Content-Type: application/json' \
  -d '{"id":101}'
```

Respuesta ejemplo asignado (`200`):

```json
{"id":101,"status":"assigned","car_id":1}
```

Respuesta ejemplo en espera (`204`): sin cuerpo.

Contrato completo en [docs/API.md](docs/API.md).

## Comandos útiles

- `composer test`: suite completa.
- `composer lint`: validación sintáctica de PHP.
- `composer coverage`: genera reporte de cobertura en `build/coverage/clover.xml`.
- `composer coverage:check`: valida umbral mínimo (90%).
- `composer migrate`: aplica migraciones pendientes.
- `composer smoke`: prueba rápida de endpoints principales.
- `composer benchmark`: benchmark local de asignaciones.
- `./scripts/composerw <comando>`: ejecuta Composer estable local (evita warnings deprecados del Composer del sistema).

## Makefile

También puedes usar:

```bash
make test
make lint
make coverage
make coverage-check
make migrate
make smoke
make serve
```

## CI

El repositorio incluye pipeline en GitHub Actions:

- Workflow: `.github/workflows/ci.yml`
- Checks: `./scripts/composerw migrate`, `./scripts/composerw lint`, `./scripts/composerw test`, `./scripts/composerw coverage`, `./scripts/composerw coverage:check`, `./scripts/composerw smoke`

## Documentación

- [docs/INSTALLATION.md](docs/INSTALLATION.md)
- [docs/CONFIGURATION.md](docs/CONFIGURATION.md)
- [docs/API.md](docs/API.md)
- [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md)
- [docs/SECURITY.md](docs/SECURITY.md)
- [docs/DEVELOPMENT.md](docs/DEVELOPMENT.md)
- [docs/VERSIONING.md](docs/VERSIONING.md)
- [docs/TROUBLESHOOTING.md](docs/TROUBLESHOOTING.md)
- [CHANGELOG.md](CHANGELOG.md)

## Autor

Aythami Melián Perdomo
