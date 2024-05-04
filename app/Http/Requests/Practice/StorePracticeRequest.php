<?php

namespace App\Http\Requests\Practice;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePracticeRequest extends FormRequest
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
            'name' => 'required|min:5|max:255|unique:practices,name',
            'questions' => 'required|array',
            'subject_id' => 'required|numeric|exists:subjects,id',
            'chapter_id' => 'numeric|exists:chapters,id',
            'question_count' => 'required|numeric'
        ];
    }

    public function attributes(): array {
        return [
            'name' => 'Tên bộ câu hỏi',
            'questions' => 'Câu hỏi',
            'subject_id' => 'Môn học',
            'chapter_id' => 'Chương',
            'question_count' => 'Số lượng câu hỏi'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống',
            'min' => ':attribute phải có ít nhất :min kí tự',
            'max' => ':attribute không được vượt quá :max kí tự',
            'unique' => ':attribute đã tồn tại',
            'array' => ':attribute phải là một mảng',
            'numeric' => ':attribute phải là một số',
            'exists' => ':attribute không tồn tại'
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
