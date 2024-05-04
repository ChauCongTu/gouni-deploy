<?php

namespace App\Http\Requests\Subject;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSubjectRequest extends FormRequest
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
            'name' => 'required|max:255|unique:subjects,id,'. $this->route('id'),
            'grade' => 'required|in:0,10,11,12'
        ];
    }

    public function attributes(): array {
        return [
            'name' => 'Tên môn học',
            'grade' => 'Khối lớp'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống',
            'max' => ':attribute không được vượt quá :max kí tự',
            'unique' => ':attribute đã tồn tại',
            'in' => ':attribute phải thuộc vào các giá trị sau: :values'
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
