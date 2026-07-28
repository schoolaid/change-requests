<?php

namespace Said\ChangeRequests\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Said\ChangeRequests\Enums\ChangeRequestPriority;
use Said\ChangeRequests\Enums\ChangeRequestStatus;
use Said\ChangeRequests\Enums\MessageRole;

class IndexChangeRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::in(ChangeRequestStatus::values())],
            'priority' => ['nullable', Rule::in(ChangeRequestPriority::values())],
            'lastActor' => ['nullable', Rule::in(MessageRole::values())],
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', Rule::in(['created_at', '-created_at', 'updated_at', '-updated_at'])],
            'perPage' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array{status?: string, priority?: string, lastActor?: string, search?: string, sort?: string}
     */
    public function filters(): array
    {
        return array_filter($this->only(['status', 'priority', 'lastActor', 'search', 'sort']));
    }

    public function perPage(): int
    {
        return (int) ($this->input('perPage') ?? config('change-requests.per_page'));
    }
}
