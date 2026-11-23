# schoolaid/change-requests

Solicitudes de cambio de las apps de SAID (MarketAid, SchoolAid): modelo, enums, DTOs, repositorio,
servicio, API HTTP, migraciones y traducciones sobre la tabla compartida `change_requests`, que
también lee la app-agente. Namespace `Said\ChangeRequests`. Laravel 11 y 12, PHP ^8.3.

Cada app ve solo sus tickets (`source_app`) y, en el modo propio, solo los de la organización del
contexto (`school_id`); sin organización, la consulta no devuelve nada.

## Instalación

```json
"repositories": {
    "change-requests": { "type": "vcs", "url": "https://github.com/schoolaid/change-requests" }
},
"require": {
    "schoolaid/change-requests": "^0.1.0"
}
```

El paquete **no** se descubre solo. Registra `Said\ChangeRequests\ChangeRequestsServiceProvider`
en la lista de providers de la app, **después** del provider que bindea tus implementaciones:
el paquete usa `bindIf`, así que la tuya gana solo si ya está registrada.

| Contrato | Para qué | Default del paquete |
|---|---|---|
| `Contracts\ContextResolverInterface` | Quién actúa, desde qué app y qué organización | `Support\DefaultContextResolver` (sin organización: no ve nada) |
| `Contracts\AuthorizerInterface` | Ver, crear, responder, cambiar estado | `Support\AllowAuthenticatedAuthorizer` |

## Configuración

`php artisan vendor:publish --tag=change-requests-config` copia `config/change-requests.php`
(prefijo y middleware de las rutas, `source_app`, `per_page`, `modules_path`).

Conexión: el paquete declara la conexión `CR_CONNECTION` (default `change_requests`) desde
`CR_DB_DRIVER`, `CR_DB_HOST`, `CR_DB_PORT`, `CR_DB_DATABASE`, `CR_DB_USERNAME` y
`CR_DB_PASSWORD`, salvo que la app ya declare una conexión con ese nombre en
`config/database.php`. `CR_SOURCE_APP` es el discriminador de app en la tabla.

## Migraciones

La tabla es compartida: las migraciones son aditivas y con guardas, y se corren a mano después del
deploy, sobre la conexión del paquete:

```bash
php artisan migrate --database=change_requests --path=vendor/schoolaid/change-requests/database/migrations/change_requests
```

## Tests

```bash
composer install && vendor/bin/pest
```

Corren sobre SQLite en memoria (`tests/Helpers/ChangeRequestsDatabase.php`), nunca sobre la BD
compartida. Los tests que dependen de la app (tenencia, permisos, su resolver) viven en cada app.

## Versiones

Un cambio al paquete se publica con un tag nuevo (`vX.Y.Z`) y cada app sube su restricción.
