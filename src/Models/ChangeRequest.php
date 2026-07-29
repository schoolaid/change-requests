<?php

namespace Said\ChangeRequests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Said\ChangeRequests\Casts\SafeEnum;
use Said\ChangeRequests\Enums\ChangeRequestPriority;
use Said\ChangeRequests\Enums\ChangeRequestStatus;
use Said\ChangeRequests\Enums\MessageRole;

/**
 * @property int $id
 * @property string $uuid
 * @property string $title
 * @property string $description
 * @property string|null $category
 * @property ChangeRequestPriority|null $priority Null si la BD trae un valor fuera del enum (ver SafeEnum)
 * @property ChangeRequestStatus|null $status Null si la BD trae un valor fuera del enum (ver SafeEnum)
 * @property string $created_by_id
 * @property string $created_by_name
 * @property string $created_by_email
 * @property string|null $school_id
 * @property string|null $school_name
 * @property string $source_app
 * @property MessageRole|null $last_actor Null si la BD trae un valor fuera del enum (ver SafeEnum)
 * @property array<int, array<string, string>> $messages
 * @property array<string, string>|null $metadata
 * @property array<int, string>|null $images
 * @property \Illuminate\Support\Carbon|null $closed_at
 */
class ChangeRequest extends Model
{
    use SoftDeletes;

    protected $table = 'change_requests';

    protected $fillable = [
        'uuid',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'created_by_id',
        'created_by_name',
        'created_by_email',
        'school_id',
        'school_name',
        'source_app',
        'last_actor',
        'messages',
        'metadata',
        'images',
        'closed_at',
    ];

    /**
     * Se resuelve en runtime (no con $connection) para que los tests puedan
     * apuntar el paquete a SQLite sin tocar la BD compartida.
     */
    public function getConnectionName(): ?string
    {
        return config('change-requests.connection');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SafeEnum::class . ':' . ChangeRequestStatus::class,
            'priority' => SafeEnum::class . ':' . ChangeRequestPriority::class,
            'last_actor' => SafeEnum::class . ':' . MessageRole::class,
            'messages' => 'array',
            'metadata' => 'array',
            'images' => 'array',
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }

            if ($model->messages === null) {
                $model->messages = [];
            }
        });
    }
}
