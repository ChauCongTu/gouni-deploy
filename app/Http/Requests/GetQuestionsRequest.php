<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GetQuestionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true; // Thay đổi thành true nếu không cần kiểm tra quyền truy cập
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'numb' => 'required|integer',
            'subject_id' => 'nullable|integer',
            'chapter_id' => 'nullable|integer',
            'grade' => 'nullable|integer',
            'level' => 'nullable|integer',
            'data' => 'nullable|array',
            // Các quy tắc xác thực khác nếu cần
        ];
    }

    public function attributes(): array
    {
        return [
            'numb' => 'Số lượng câu hỏi',
            'subject_id' => 'Môn học',
            'chapter_id' => 'Chương',
            'level' => 'Mức độ',
            'data' => 'Dữ liệu'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống',
            'integer' => ':attribute phải là một số nguyên',
            'array' => ':attribute phải là một mảng'
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
