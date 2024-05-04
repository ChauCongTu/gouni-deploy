<?php

namespace App\Http\Requests\Profile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
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
            'username' => 'required|unique:users,username,'. Auth::id(),
            'phone' => 'required|numeric',
            'gender' => 'required|in:nam,nữ,khác',
            'dob' => 'required',
            'address' => 'required|min:5|max:255',
            'school' => 'required|min:5|max:255',
            'class' => 'required|min:1|max:255',
            'test_class' => 'required|min:1|max:255',
            'grade' => 'required|numeric'
        ];
    }
    public function attributes(): array {
        return [
            'username' => 'Tên người dùng',
            'phone' => 'Số điện thoại',
            'gender' => 'Giới tính',
            'dob' => 'Ngày sinh',
            'address' => 'Địa chỉ',
            'school' => 'Trường học',
            'class' => 'Tổ hợp theo học',
            'test_class' => 'Tổ hợp ôn thi',
            'grade' => 'Khối lớp'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống',
            'unique' => ':attribute :input đã tồn tại',
            'numeric' => ':attribute phải là số',
            'min' => ':attribute phải có ít nhất :min kí tự',
            'max' => ':attribute chỉ được chứa tối đa :max kí tự',
            'in' => ':attribute không hợp lệ',
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
