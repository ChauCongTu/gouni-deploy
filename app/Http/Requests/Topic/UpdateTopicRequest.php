<?php

namespace App\Http\Requests\Topic;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTopicRequest extends FormRequest
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
            'title' => 'required|min:5|unique:topics,title,' . $this->route('id'),
            'content' => 'required',
            'attachment' => 'nullable'
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Tiêu đề',
            'content' => 'Nội dung',
            'attachment' => 'Tệp đính kèm'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống',
            'min' => ':attribute phải có ít nhất :min kí tự',
            'unique' => ':attribute đã tồn tại',
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
