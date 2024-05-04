<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class QueryRequest extends FormRequest
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
            'with' => 'nullable',
            'filterBy' => 'nullable',
            'value' => 'nullable',
            'condition' => 'nullable',
            'page' => 'nullable|numeric',
            'perPage' => 'nullable|numeric',
            'sort' => 'nullable',
            'order' => 'nullable'
        ];
    }

    public function attributes(): array {
        return [
            'with' => 'Chứa',
            'filterBy' => 'Lọc theo',
            'value' => 'Giá trị',
            'condition' => 'Điều kiện',
            'page' => 'Trang',
            'perPage' => 'Số mục trên mỗi trang',
            'sort' => 'Sắp xếp',
            'order' => 'Thứ tự sắp xếp'
        ];
    }

    public function messages(): array
    {
        return [
            'numeric' => ':attribute phải là một số',
            'nullable' => ':attribute có thể để trống'
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
