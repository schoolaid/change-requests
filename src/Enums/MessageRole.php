<?php

namespace Said\ChangeRequests\Enums;

enum MessageRole: string
{
    case Admin = 'admin';
    case Agent = 'agent';

    public function label(?string $locale = null): string
    {
        return __('change-requests::enums.messageRole.' . $this->value, [], $locale);
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
