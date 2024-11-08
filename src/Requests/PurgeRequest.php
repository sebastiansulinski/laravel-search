<?php

namespace SebastianSulinski\Search\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $index
 */
class PurgeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'index' => [
                'required',
                'string',
            ],
        ];
    }
}
