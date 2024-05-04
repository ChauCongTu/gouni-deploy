<?php

namespace App\Http\Requests\Question;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreQuestionRequest extends FormRequest
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
            'question' => 'required',
            'answer_1' => 'required',
            'answer_2' => 'required',
            'answer_3' => 'nullable',
            'answer_4' => 'nullable',
            'answer_correct' => 'required|in:1,2,3,4',
            'answer_detail' => 'nullable',
            'subject_id' => 'required',
            'grade' => 'nullable',
            'chapter_id' => 'nullable',
            'level' => 'required|in:1,2,3,4,5'
        ];
    }

    public function attributes(): array {
        return [
            'question' => 'Câu hỏi',
            'answer_1' => 'Đáp án 1',
            'answer_2' => 'Đáp án 2',
            'answer_3' => 'Đáp án 3',
            'answer_4' => 'Đáp án 4',
            'answer_correct' => 'Đáp án đúng',
            'answer_detail' => 'Giải chi tiết',
            'subject_id' => 'Môn học',
            'chapter_id' => 'Chương',
            'level' => 'Mức độ'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống',
            'in' => ':attribute không hợp lệ',
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
