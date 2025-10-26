<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComapnyRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:companies,name',
            'code' => 'required|string|max:50|unique:companies,code',
            'email' => 'required|email|unique:companies,email',
            'phone' => 'required|string|max:15|unique:companies,phone',
            
        ];
    }


    public function messages(): array
{
    return [
        'name.required' => 'الاسم مطلوب.',
        'name.string' => 'الاسم يجب أن يكون نصًا.',
        'name.max' => 'الاسم لا يمكن أن يزيد عن 255 حرفًا.',

        'code.required' => 'الكود مطلوب.',
        'code.string' => 'الكود يجب أن يكون نصًا.',
        'code.max' => 'الكود لا يمكن أن يزيد عن 50 حرفًا.',
        'code.unique' => 'هذا الكود مستخدم من قبل.',

        'email.required' => 'البريد الإلكتروني مطلوب.',
        'email.email' => 'البريد الإلكتروني غير صحيح.',
        'email.unique' => 'هذا البريد الإلكتروني مستخدم من قبل.',

        'phone.required' => 'رقم الهاتف مطلوب.',
        'phone.string' => 'رقم الهاتف يجب أن يكون نصًا.',
        'phone.max' => 'رقم الهاتف لا يمكن أن يزيد عن 15 رقمًا.',
    ];
}
    // Handling Validation Error
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Validation في التعديل فقط
        throw new \Illuminate\Validation\ValidationException(
            $validator,
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal', 'create') // هنا نحدد مودال التعديل
        );
    }


}