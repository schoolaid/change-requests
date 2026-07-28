<?php

namespace Said\ChangeRequests\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Said\ChangeRequests\Enums\MessageType;

class ReplyChangeRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * `status_change` lo emite el servicio y `result` es de la app-agente: si
     * llegan desde el cliente se degradan a `answer` en vez de fallar. Ausencia
     * de `type` también cae a `answer` (es el default).
     *
     * Cualquier otro valor NO se degrada: cae en `Rule::in` y devuelve 422. Si
     * se degradara todo, un typo del cliente se persistiría como `answer` para
     * siempre (el hilo es append-only) y `rules()` sería código muerto.
     */
    protected function prepareForValidation(): void
    {
        $type = $this->input('type');

        $reserved = [
            MessageType::StatusChange->value,
            MessageType::Result->value,
        ];

        if ($type === null || in_array($type, $reserved, true)) {
            $this->merge(['type' => MessageType::Answer->value]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', function (string $attribute, mixed $value, \Closure $fail): void {
                if (trim((string) $value) === '') {
                    $fail('El mensaje no puede estar vacío.');
                }
            }],
            'type' => ['required', Rule::in(MessageType::replyableValues())],
        ];
    }

    public function messageType(): MessageType
    {
        return MessageType::from((string) $this->input('type'));
    }
}
