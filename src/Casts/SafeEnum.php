<?php

namespace Said\ChangeRequests\Casts;

use BackedEnum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Cast tolerante al leer, estricto al escribir.
 *
 * La BD `orchestrator` es compartida y la app-agente escribe `status` y
 * `last_actor` sin pasar por este código. Un valor fuera del vocabulario no
 * puede reventar la hidratación: un solo ticket raro tumbaría el listado
 * entero, no solo su fila. Al escribir sí se exige un case válido, para que
 * marketaid nunca sea el origen de un valor desconocido.
 *
 * @implements CastsAttributes<BackedEnum|null, BackedEnum|string|int|null>
 */
class SafeEnum implements CastsAttributes
{
    /**
     * @param class-string<BackedEnum> $enum
     */
    public function __construct(private readonly string $enum)
    {
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?BackedEnum
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $this->enum::tryFrom((string) $value);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof BackedEnum) {
            return (string) $value->value;
        }

        return (string) $this->enum::from((string) $value)->value;
    }
}
