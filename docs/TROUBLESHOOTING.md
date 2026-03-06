# TROUBLESHOOTING

## Error de conexión MySQL

- Verifica `.env`
- Comprueba que el servidor MySQL está levantado
- Revisa usuario/contraseña y permisos sobre `DB_NAME`

## Error en `composer migrate` por checksum

Si aparece un error de checksum en una migración, significa que el SQL de un fichero ya aplicado cambió.

- No edites migraciones históricas ya aplicadas.
- Crea una nueva migración incremental (`000X_*.sql`) con el cambio adicional.
- Si estás en entorno local desechable, puedes recrear la base de datos y volver a ejecutar migraciones.

## `vendor/autoload.php` no encontrado

Ejecuta:

```bash
composer install
```

## `composer smoke` falla por conexión rechazada

- Asegura que el servidor HTTP está levantado:
  `php -S 127.0.0.1:9091 -t public`
- Si usas otro host/puerto, pasa la URL:
  `bash scripts/smoke.sh http://127.0.0.1:PUERTO`

## Warnings/deprecations al ejecutar Composer con PHP 8.4

En algunos entornos, el Composer del sistema puede emitir deprecations de librerías internas.  
Mientras `composer test`, `composer lint` y `composer migrate` terminen con código `0`, el proyecto sigue siendo operativo.
