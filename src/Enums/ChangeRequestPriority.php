<?php

namespace Said\ChangeRequests\Enums;

enum ChangeRequestPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Urgent = 'urgent';

    public function label(?string $locale = null): string
    {
        return __('change-requests::enums.priority.' . $this->value, [], $locale);
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
