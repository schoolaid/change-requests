<?php

namespace Said\ChangeRequests\DataTransferObjects;

use Said\ChangeRequests\Enums\MessageRole;
use Said\ChangeRequests\Enums\MessageType;

final class Message
{
    public function __construct(
        public readonly MessageRole $role,
        public readonly MessageType $type,
        public readonly string $body,
        public readonly string $authorName,
        public readonly string $at,
    ) {
    }

    public static function make(
        MessageRole $role,
        MessageType $type,
        string $body,
        string $authorName,
    ): self {
        return new self($role, $type, $body, $authorName, now()->toDateTimeString());
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            MessageRole::from((string) $data['role']),
            MessageType::from((string) $data['type']),
            (string) $data['body'],
            (string) ($data['authorName'] ?? ''),
            (string) ($data['at'] ?? ''),
        );
    }

    /**
     * @return array{role: string, type: string, body: string, authorName: string, at: string}
     */
    public function toArray(): array
    {
        return [
            'role' => $this->role->value,
            'type' => $this->type->value,
            'body' => $this->body,
            'authorName' => $this->authorName,
            'at' => $this->at,
        ];
    }
}
