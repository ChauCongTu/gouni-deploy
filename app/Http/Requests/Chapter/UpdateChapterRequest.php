<?php

namespace App\Http\Requests\Chapter;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateChapterRequest extends FormRequest
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
            'name' => 'required|min:5|max:255',
            'subject_id' => 'required|numeric|exists:subjects,id'
        ];
    }

    public function attributes(): array {
        return [
            'name' => 'Tên chương',
            'subject_id' => 'Môn học'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống',
            'min' => ':attribute phải có ít nhất :min kí tự',
            'max' => ':attribute không được vượt quá :max kí tự',
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
