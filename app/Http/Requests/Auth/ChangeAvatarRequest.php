<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangeAvatarRequest extends FormRequest
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
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }

    public function attributes(): array {
        return [
            'avatar' => 'Ảnh đại diện'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống',
            'image' => ':attribute phải là một ảnh',
            'mimes' => ':attribute chỉ được chấp nhận các định dạng: :values',
            'max' => ':attribute không được vượt quá :max KB'
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
