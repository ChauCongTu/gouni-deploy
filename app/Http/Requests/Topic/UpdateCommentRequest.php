<?php

namespace App\Http\Requests\Topic;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCommentRequest extends FormRequest
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
            'content' => 'required',
            'attachment' => 'nullable'
        ];
    }

    public function attributes(): array {
        return [
            'content' => 'Nội dung',
            'attachment' => 'Tệp đính kèm'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống',
            'numeric' => ':attribute phải là một số',
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
