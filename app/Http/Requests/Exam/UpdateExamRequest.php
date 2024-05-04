<?php

namespace App\Http\Requests\Exam;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateExamRequest extends FormRequest
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
            'name' => 'required|min:3|max:255|unique:exams,name,' . $this->route('id'),
            'time' => 'required|numeric',
            'questions' => 'required|array',
            'question_count' => 'required|numeric',
            'subject_id' => 'required|numeric|exists:subjects,id',
            'chapter_id' => 'nullable',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tên kỳ thi',
            'time' => 'Thời gian',
            'questions' => 'Câu hỏi',
            'question_count' => 'Số lượng câu hỏi',
            'subject_id' => 'Môn học',
            'chapter_id' => 'Chương'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống',
            'min' => ':attribute phải có ít nhất :min kí tự',
            'max' => ':attribute không được vượt quá :max kí tự',
            'unique' => ':attribute đã tồn tại',
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
