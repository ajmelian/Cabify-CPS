# ARCHITECTURE

## Estilo

Arquitectura Hexagonal (Ports & Adapters):

- Dominio: `src/*`
- Aplicación: `Application/*`
- Infraestructura: `Infrastructure/*`

## Flujo implementado (`PUT /cars`)

- Adapter de entrada HTTP:
  - `Infrastructure/Request/HttpRequest`
  - `Infrastructure/Controller/PutCarsController`
  - `Infrastructure/Router/Router`
  - `Infrastructure/Http/Kernel`
- Caso de uso:
  - `Application/Command/ReplaceFleetCommand`
  - `Application/Handler/ReplaceFleetHandler`
- Adapter de salida MySQL:
  - `Infrastructure/Persistence/MySql/MySqlCarRepository`

## Flujo implementado (`POST /journey`)

- Adapter de entrada HTTP:
  - `Infrastructure/Controller/PostJourneyController`
- Caso de uso:
  - `Application/Command/RegisterJourneyCommand`
  - `Application/Handler/RegisterJourneyHandler`
- Adapter de salida MySQL:
  - `Infrastructure/Persistence/MySql/MySqlJourneyRepository`

## Flujos implementados (`POST /dropoff`, `POST /locate`)

- `POST /dropoff`:
  - `Infrastructure/Controller/PostDropoffController`
  - `Application/Handler/DropoffJourneyHandler`
  - `Infrastructure/Persistence/MySql/MySqlJourneyRepository`
- `POST /locate`:
  - `Infrastructure/Controller/PostLocateController`
  - `Application/Handler/LocateJourneyHandler`
  - `Infrastructure/Persistence/MySql/MySqlJourneyRepository`

## Optimización de asignación

- `src/Service/FairJourneyAssignmentPlanner` calcula asignaciones en memoria con snapshot de capacidad.
- `MySqlJourneyRepository` expone snapshot de plazas libres por coche en una única consulta agregada.
- `RegisterJourneyHandler` y `DropoffJourneyHandler` usan ese snapshot cuando el adapter lo soporta.

## Reglas de dependencia

- Permitido: `Application -> Domain`, `Infrastructure -> Application + Domain`
- Prohibido: `Domain/Application -> Infrastructure`

## Entrypoint

- Único entrypoint web: `public/index.php`
