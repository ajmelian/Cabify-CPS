# API

## Estado actual

### GET /status

- Respuesta: `200 OK`
- `Content-Type: application/json; charset=utf-8`

Ejemplo:

```json
{
  "service": "cabify-car-pooling",
  "status": "ok",
  "path": "/status"
}
```

### PUT /cars

- Reemplaza la flota completa.
- Resetea el estado operativo previo (coches y cola de grupos).
- `Content-Type` obligatorio: `application/json`.

Request body:

```json
[
  { "id": 1, "seats": 4 },
  { "id": 2, "seats": 6 }
]
```

Validaciones:

- `id`: entero positivo (`^[1-9][0-9]*$`)
- `seats`: entero entre 1 y 8 (`^[1-8]$`)
- IDs de coche no duplicados en el payload

Respuestas:

- `200 OK`

```json
{
  "status": "fleet_replaced"
}
```

- `400 Bad Request` para JSON inválido, schema inválido o validación regex/semántica.
- `404 Not Found` para ruta inexistente.
- `500 Internal Server Error` sin detalles internos.

### POST /journey

- Registra un grupo nuevo con `id` y `people`.
- El sistema intenta asignar coche respetando el orden de llegada cuando sea posible.
- `Content-Type` obligatorio: `application/json`.

Request body:

```json
{
  "id": 7,
  "people": 4
}
```

Validaciones:

- `id`: entero positivo (`^[1-9][0-9]*$`)
- `people`: entero de 1 a 6 (`^[1-6]$`)
- `id` de grupo no puede repetirse

Respuestas:

- `200 OK` (asignado)

```json
{
  "id": 7,
  "status": "assigned",
  "car_id": 2
}
```

- `202 Accepted` (en espera)

```json
{
  "id": 7,
  "status": "waiting"
}
```

- `400 Bad Request` para payload inválido/duplicado.
- `404 Not Found` para ruta inexistente.
- `500 Internal Server Error` sin detalles internos.

### POST /dropoff

- Finaliza un grupo y libera plazas del coche asignado.
- Tras dropoff se reevalúa la cola de espera para asignar grupos cuando sea posible.
- `Content-Type` obligatorio: `application/json`.

Request body:

```json
{
  "id": 7
}
```

Respuestas:

- `200 OK`

```json
{
  "id": 7,
  "status": "dropped_off"
}
```

- `400 Bad Request` para payload inválido.
- `404 Not Found` si el grupo no existe.

### POST /locate

- Localiza estado actual de un grupo.
- `Content-Type` obligatorio: `application/json`.

Request body:

```json
{
  "id": 7
}
```

Respuestas:

- `200 OK` cuando está asignado:

```json
{
  "id": 7,
  "status": "assigned",
  "car_id": 2
}
```

- `204 No Content` cuando el grupo está en espera.
- `404 Not Found` si el grupo no existe.
- `400 Bad Request` para payload inválido.

## Endpoints del reto (pendientes de implementación)

- Ninguno en el alcance actual.
