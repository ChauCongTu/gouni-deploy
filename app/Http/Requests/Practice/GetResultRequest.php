<?php

namespace App\Http\Requests\Practice;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetResultRequest extends FormRequest
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
            'time'=> 'required|numeric',
            'res' => 'nullable|array'
        ];
    }

    public function attributes(): array {
        return [
            'time' => 'Thời gian',
            'res' => 'Kết quả'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống',
            'numeric' => ':attribute phải là một số',
            'array' => ':attribute phải là một mảng',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->getMessageBag()
        ]));
    }
}
