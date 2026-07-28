<?php

namespace Said\ChangeRequests\Enums;

enum ChangeRequestCategory: string
{
    case Bug = 'bug';
    case Feature = 'feature';
    case Improvement = 'improvement';
    case Performance = 'performance';
    case Security = 'security';
    case DataFix = 'data_fix';

    public function label(?string $locale = null): string
    {
        return __('change-requests::enums.category.' . $this->value, [], $locale);
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
