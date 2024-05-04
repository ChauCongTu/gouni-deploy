<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
            'username' => 'required|min:5|max:255|unique:users,username',
            'name' => 'required|min:5|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:255'
        ];
    }
    public function attributes(): array {
        return [
            'username' => 'Tên người dùng',
            'name' => 'Họ tên',
            'email' => 'Địa chỉ Email',
            'password' => 'Mật khẩu'
        ];
    }
    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống',
            'min' => ':attribute phải có ít nhất :min kí tự',
            'max' => ':attribute chỉ được chứa tối đa :max kí tự',
            'unique' => ':attribute :input đã tồn tại',
            'email' => ':attribute không đúng định dạng'
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
