<?php

namespace App\Http\Requests\Arena;

use Illuminate\Foundation\Http\FormRequest;

class ProgressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'progress' => 'nullable',
            'arenaId' => 'required',
            'userId' => 'nullable'
        ];
    }
}
