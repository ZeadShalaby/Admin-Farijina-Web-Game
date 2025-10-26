<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = Auth::guard('sanctum')->user()->id;

        return [
            'email' => "sometimes|email|unique:users,email,{$userId}",
            'phone' => "sometimes|string|unique:users,phone,{$userId}",
            'name' => 'sometimes|string|min:2|max:255',
            'username' => "sometimes|string|min:3|max:255|unique:users,username,{$userId}",
            'gander' => 'sometimes|in:male,female',
            'date' => 'sometimes|date_format:Y-m-d',
        ];
    }
}
