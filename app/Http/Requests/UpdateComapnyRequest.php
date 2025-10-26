<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateComapnyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $companyId = $this->request->get('id'); // ID الريكورد اللي بيتعدل
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'name')->ignore($companyId)
            ],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('companies', 'code')->ignore($companyId)
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('companies', 'email')->ignore($companyId)
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('companies', 'phone')->ignore($companyId)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب.',
            'name.string' => 'الاسم يجب أن يكون نص.',
            'name.max' => 'الاسم لا يمكن أن يزيد عن 255 حرف.',
            'name.unique' => 'الاسم مستخدم من قبل.',

            'code.required' => 'الكود مطلوب.',
            'code.string' => 'الكود يجب أن يكون نص.',
            'code.max' => 'الكود لا يمكن أن يزيد عن 10 أحرف.',
            'code.unique' => 'الكود مستخدم من قبل.',

            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.max' => 'البريد الإلكتروني لا يمكن أن يزيد عن 255 حرف.',
            'email.unique' => 'البريد الإلكتروني مستخدم من قبل.',

            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.string' => 'رقم الهاتف يجب أن يكون نص.',
            'phone.max' => 'رقم الهاتف لا يمكن أن يزيد عن 20 حرف.',
            'phone.unique' => 'رقم الهاتف مستخدم من قبل.',
        ];
    }

    // التعامل مع Validation Error وفتح مودال التعديل
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException(
            $validator,
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal', 'edit') // مودال التعديل هيتفتح
        );
    }
}
