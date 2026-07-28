<?php

namespace Said\ChangeRequests\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Said\ChangeRequests\Enums\ChangeRequestCategory;
use Said\ChangeRequests\Enums\ChangeRequestPriority;
use Said\ChangeRequests\Support\ModuleCatalog;

class StoreChangeRequestRequest extends FormRequest
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
        $modules = app(ModuleCatalog::class)->slugs();

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['nullable', Rule::in(ChangeRequestPriority::values())],
            'category' => ['nullable', Rule::in(ChangeRequestCategory::values())],
            'acceptanceCriteria' => ['required', 'string'],
            'reproSteps' => ['nullable', 'string'],
            'affectedArea' => ['nullable', 'string', 'max:255'],
            // Si la app no tiene catálogo de módulos, el campo queda libre.
            'affectedModule' => $modules === []
                ? ['nullable', 'string', 'max:100']
                : ['nullable', Rule::in($modules)],
            'constraints' => ['nullable', 'string'],
        ];
    }
}
