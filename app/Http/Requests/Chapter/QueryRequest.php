<?php

namespace App\Http\Requests\Chapter;

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
            'getWith' => 'nullable|array',
            'getWith.*' => 'in:questions,lessons,subject',
            'page' => 'nullable|numeric',
            'perPage' => 'nullable|numeric',
            'sort' => 'nullable',
            'order' => 'nullable|in:ASC,DESC'
        ];
    }

    public function attributes(): array {
        return [
            'getWith' => 'Lấy với',
            'page' => 'Trang',
            'perPage' => 'Số mục trên mỗi trang',
            'sort' => 'Sắp xếp',
            'order' => 'Thứ tự sắp xếp'
        ];
    }

    public function messages(): array
    {
        return [
            'in_array' => 'Trường :attribute phải là một mảng',
            'in' => ':attribute không hợp lệ',
            'numeric' => ':attribute phải là một số',
            'nullable' => ':attribute có thể để trống',
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
