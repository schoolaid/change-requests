<?php

namespace Said\ChangeRequests\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Said\ChangeRequests\Enums\ChangeRequestStatus;

class ChangeStatusRequest extends FormRequest
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
            'status' => ['required', Rule::in(ChangeRequestStatus::values())],
        ];
    }

    public function targetStatus(): ChangeRequestStatus
    {
        return ChangeRequestStatus::from((string) $this->input('status'));
    }
}
