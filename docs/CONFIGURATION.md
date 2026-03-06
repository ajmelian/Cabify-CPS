# CONFIGURATION

## Variables de entorno (`.env`)

Usar `.env.example` como base y completar valores según el entorno.

- `APP_ENV`: `local|test|prod`
- `APP_DEBUG`: `0|1`
- `APP_PORT`: puerto HTTP local (por defecto `9091`)
- `DB_HOST`: host de MySQL (`127.0.0.1` o `localhost`)
- `DB_PORT`: puerto de MySQL (por defecto `3306`)
- `DB_NAME`: nombre de base de datos
- `DB_USER`: usuario de base de datos
- `DB_PASS`: contraseña de base de datos
- `DB_CHARSET`: `utf8mb4` recomendado

## Ejemplo mínimo

```dotenv
APP_ENV=local
APP_DEBUG=1
APP_PORT=9091
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=cabify
DB_USER=change_me
DB_PASS=change_me
DB_CHARSET=utf8mb4
```

## Reglas

- `.env` no debe versionarse.
- Los secretos no deben imprimirse en logs.
- Cualquier cambio de configuración persistente debe reflejarse también en `.env.example`.
