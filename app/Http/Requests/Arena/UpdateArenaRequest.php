<?php

namespace App\Http\Requests\Arena;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateArenaRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:3|max:255',
            'max_users' => 'required|numeric',
            'time' => 'required|numeric',
            'grade' => 'nullable',
            'questions' => 'required|array',
            'question_count' => 'required|numeric',
            'start_at' => 'required|date_format:Y-m-d H:i:s',
            'type' => 'required',
            'subject_id' => 'nullable',
            'password' => 'nullable|min:6',
        ];
    }

    public function attributes(): array {
        return [
            'name' => 'Tên sân chơi',
            'max_users' => 'Số người chơi tối đa',
            'time' => 'Thời gian',
            'grade' => 'Khối lớp',
            'subject_id' => 'Môn học',
            'questions' => 'Câu hỏi',
            'question_count' => 'Số lượng câu hỏi',
            'start_at' => 'Thời gian bắt đầu',
            'type' => 'Loại',
            'password' => 'Mật khẩu'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống',
            'min' => ':attribute phải có ít nhất :min kí tự',
            'max' => ':attribute không được vượt quá :max kí tự',
            'numeric' => ':attribute phải là một số',
            'array' => ':attribute phải là một mảng',
            'date_format' => ':attribute phải có định dạng Y-m-d H:i:s'
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
