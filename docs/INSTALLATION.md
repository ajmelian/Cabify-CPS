# INSTALLATION

## Requisitos

- PHP 8.4 CLI
- Composer 2
- MySQL 8+
- Extensiones PHP: `ext-json`, `ext-pdo`, `pdo_mysql`

## Instalación paso a paso

1. Instalar dependencias:

```bash
./scripts/composerw install
```

2. Crear configuración local:

```bash
cp .env.example .env
```

3. Editar `.env` con credenciales reales de MySQL.

4. Aplicar migraciones:

```bash
./scripts/composerw migrate
```

El runner aplica solo migraciones no ejecutadas previamente y registra cada una en la tabla `schema_migrations`.

5. Ejecutar tests:

```bash
./scripts/composerw test
./scripts/composerw smoke
```

Si prefieres Composer global, puedes usar `composer ...`, pero `./scripts/composerw` evita warnings deprecados en entornos con Composer de sistema antiguo.

6. Arrancar servidor local:

```bash
php -S 127.0.0.1:9091 -t public
```

## Verificación rápida

```bash
curl -i http://127.0.0.1:9091/status
```

## Alternativa con Makefile

```bash
make install
make migrate
make test
make smoke
make serve
```
