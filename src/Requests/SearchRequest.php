<?php

namespace SebastianSulinski\Search\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property string $index
 * @property array $params
 */
class SearchRequest extends FormRequest
{
    /**
     * Get validation rules.
     */
    public function rules(): array
    {
        return $this->attachMacro([
            'index' => [
                'required',
                Rule::in(config('search.indexes')),
            ],
            'params' => [
                'required',
                'array',
            ],
        ]);
    }

    /**
     * Attach defined macros.
     *
     * @return array<string, mixed>
     */
    private function attachMacro(array $rules): array
    {
        if (! $this->index || ! static::hasMacro($this->index)) {
            return $rules;
        }

        $macro = static::$macros[$this->index];

        return array_merge(
            $rules,
            is_callable($macro)
                ? call_user_func_array($macro, ['request' => $this]) :
                $macro
        );
    }

    /**
     * Get request payload.
     */
    public function payload(): array
    {
        return $this->validated('params');
    }
}
