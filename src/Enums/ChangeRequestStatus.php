<?php

namespace Said\ChangeRequests\Enums;

enum ChangeRequestStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case NeedsInfo = 'needs_info';
    case Testing = 'testing';
    case Done = 'done';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public function label(?string $locale = null): string
    {
        return __('change-requests::enums.status.' . $this->value, [], $locale);
    }

    /**
     * @return array<int, self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Open => [self::InProgress, self::Cancelled],
            self::InProgress => [self::NeedsInfo, self::Testing, self::Cancelled],
            self::NeedsInfo => [self::InProgress, self::Cancelled],
            self::Testing => [self::Done, self::Rejected, self::Cancelled],
            self::Rejected => [self::InProgress, self::Cancelled],
            self::Done, self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    public function isTerminal(): bool
    {
        return $this->allowedTransitions() === [];
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
