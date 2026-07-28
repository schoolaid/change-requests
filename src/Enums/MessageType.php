<?php

namespace Said\ChangeRequests\Enums;

enum MessageType: string
{
    case Request = 'request';
    case Question = 'question';
    case Answer = 'answer';
    case Result = 'result';
    case Feedback = 'feedback';
    case StatusChange = 'status_change';

    public function label(?string $locale = null): string
    {
        return __('change-requests::enums.messageType.' . $this->value, [], $locale);
    }

    /**
     * Tipos que un cliente del API puede elegir. `status_change` lo emite el
     * servicio y `result` es de la app-agente.
     *
     * @return array<int, string>
     */
    public static function replyableValues(): array
    {
        return [
            self::Request->value,
            self::Question->value,
            self::Answer->value,
            self::Feedback->value,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}
