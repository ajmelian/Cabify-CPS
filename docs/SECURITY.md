# SECURITY

## Controles activos

- Prepared statements PDO para prevenir SQL Injection.
- Respuesta JSON con `Content-Type` seguro.
- Header `X-Content-Type-Options: nosniff`.
- Variables sensibles en `.env` (no versionadas).

## Reglas

- No exponer stack traces en respuestas HTTP.
- No registrar secretos en logs.
- Validar toda entrada con regex + semántica.
