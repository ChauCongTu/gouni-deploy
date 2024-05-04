<?php

namespace App\Http\Requests\Lesson;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateLessonRequest extends FormRequest
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
            'name' => 'required|min:5|max:255|unique:lessons,name,'. $this->route('id'),
            'chap_id' => 'required|numeric|exists:chapters,id',
            'content' => 'required',
            'type' => 'required|in:lythuyet,giaibt',
        ];
    }

    public function attributes(): array {
        return [
            'name' => 'Tên bài học',
            'chap_id' => 'Chương',
            'content' => 'Nội dung',
            'type' => 'Loại'
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
            'exists' => ':attribute không tồn tại',
            'in' => ':attribute không hợp lệ, phải là `lythuyet` hoặc `giaibt`',
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
