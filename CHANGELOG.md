# Changelog

## [0.5.9] - 2026-03-04

- Revisión y homogeneización de docblocks en la suite de tests y ajuste final en `Infrastructure/Security/InputPatterns`.
- README ampliado con bloque explícito de instalación/arranque de la API y ejemplos `curl` de todos los endpoints disponibles.

## [0.5.8] - 2026-03-04

- Añadido `scripts/composerw` (Composer estable local) y adopción en `Makefile` + CI para evitar warnings deprecados del Composer de sistema.
- Nuevos tests unitarios de `PdoConnectionFactory` y tests de integración negativos del runner de migraciones (fallo de conexión y checksum mismatch).
- Runner de migraciones extendido con variables opcionales `CABIFY_ENV_FILE` y `CABIFY_MIGRATIONS_PATH`.
- Cobertura de líneas subida a `90.57%` (`394/435`) y umbral mínimo de `composer coverage:check` elevado a `90%`.

## [0.5.7] - 2026-03-04

- Nueva batería de tests unitarios para `JourneyGroup`, `JourneyState` y validaciones adicionales de `Car`.
- Cobertura de líneas subida a `85.29%` (`371/435`).
- Umbral mínimo de `composer coverage:check` elevado a `85%`.

## [0.5.6] - 2026-03-04

- Centralizado patrón de enteros en `Infrastructure/Security/InputPatterns::INTEGER`.
- Eliminados regex inline en controladores HTTP (`PUT /cars`, `POST /journey`, `POST /dropoff`, `POST /locate`) para reforzar consistencia de validación.
- Validación funcional adicional ejecutada: migraciones idempotentes y `composer smoke` en entorno local.

## [0.5.5] - 2026-03-04

- Aumentada la cobertura de líneas a `83.91%` con nuevos tests unitarios de infraestructura HTTP/configuración.
- Subido el umbral mínimo de `composer coverage:check` a `80%`.
- Actualizada la documentación de desarrollo y comandos para reflejar el nuevo umbral.

## [0.5.4] - 2026-03-04

- Añadidos comandos `composer coverage` y `composer coverage:check`.
- Nuevo script `scripts/check_coverage.php` para validar umbral mínimo de cobertura (70%).
- Pipeline CI actualizado para ejecutar cobertura y adjuntar `clover.xml` como artifact.
- Makefile extendido con targets `coverage` y `coverage-check`.
- Documentación de cobertura añadida en `README.md` y `docs/DEVELOPMENT.md`.

## [0.5.3] - 2026-03-04

- Nuevo pipeline de CI en GitHub Actions (`.github/workflows/ci.yml`).
- Ejecución automatizada con MySQL de `composer migrate`, `composer lint`, `composer test` y `composer smoke`.
- Documentación de CI añadida en `README.md` y `docs/DEVELOPMENT.md`.

## [0.5.2] - 2026-03-04

- Nuevo `scripts/smoke.sh` para validación rápida del flujo HTTP principal.
- Nuevo script de Composer `composer smoke`.
- Nuevo `Makefile` con targets de trabajo diario (`test`, `lint`, `migrate`, `smoke`, `benchmark`, `serve`).
- Actualización de documentación de instalación y desarrollo con flujo de smoke test.

## [0.5.1] - 2026-03-04

- Mejora del runner de migraciones con tabla `schema_migrations` y ejecución incremental idempotente.
- Validación por checksum para detectar cambios en migraciones ya aplicadas.
- Corrección de test de fairness para alinearlo con el comportamiento `first-fit` del planner.
- Ampliación de documentación operativa (`README`, instalación, configuración, desarrollo y troubleshooting).

## [0.5.0] - 2026-03-04

- Optimización de asignación mediante `FairJourneyAssignmentPlanner` y snapshot de plazas libres por coche.
- Integración de camino optimizado en `RegisterJourneyHandler` y `DropoffJourneyHandler`.
- Nuevos tests unitarios de `Router`, `Kernel` y `FairJourneyAssignmentPlanner`.
- Nuevos tests de integración para flujo completo y carga media.
- Nuevo comando `composer benchmark` para medir throughput de asignación.

## [0.4.1] - 2026-03-04

- Hardening de capa HTTP con tests unitarios de `Router` y `Kernel`.
- Tests de integración de flujo completo (`journey -> locate -> dropoff -> locate`).
- Cobertura de fairness en reasignación tras dropoff con capacidad limitada.

## [0.4.0] - 2026-03-04

- Implementación de `POST /dropoff` con liberación de plazas y reasignación de cola.
- Implementación de `POST /locate` con `200` asignado y `204` en espera.
- Nuevos casos de uso `DropoffJourneyHandler` y `LocateJourneyHandler`.
- Extensión del repositorio de journeys con `findJourneyById` y `removeJourneyById`.
- Tests unitarios para handlers/controladores de dropoff/locate.
- Cobertura de integración MySQL para locate/dropoff en repositorio de journeys.

## [0.3.0] - 2026-03-04

- Implementación de `POST /journey` con handler de aplicación y controlador HTTP.
- Adaptador MySQL de journeys con cola de espera y asignación por disponibilidad.
- Validación estricta de payload y duplicados con errores HTTP 400.
- Nueva migración `0002` para precisión de llegada con `created_at` en microsegundos.
- Tests unitarios para `RegisterJourneyHandler` y `PostJourneyController`.
- Tests de integración MySQL para operaciones de journeys.
- Ajuste de `PUT /cars` para devolver 400 en JSON inválido.

## [0.2.0] - 2026-03-04

- Implementación del flujo hexagonal completo para `PUT /cars`.
- Router HTTP, request/response tipados y kernel de manejo de errores.
- Validación estricta de payload JSON con regex centralizadas.
- Reseteo transaccional de estado (`groups_queue` + `cars`) en MySQL.
- Tests unitarios de aplicación/controlador y test de integración MySQL.
- Actualización de documentación técnica y de API.

## [0.1.0] - 2026-03-04

- Bootstrap inicial del proyecto.
- Estructura hexagonal de carpetas.
- Configuración de Composer PSR-4.
- Configuración inicial de PHPUnit.
- Base de infraestructura MySQL (PDO) y migración inicial.
- Documentación base del proyecto.
