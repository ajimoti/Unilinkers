<?php

namespace App\Http\Requests;

use App\Enums\SizeUnit;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoomRequest extends FormRequest
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
            'property_id' => ['required', 'integer', 'exists:properties,id'],
            'name' => ['required', 'string', 'max:100', 'min:2'],
            'size' => ['required', 'integer', 'min:1'],
            'size_unit' => ['required', 'string', 'in:' . implode(',', SizeUnit::values())],
        ];
    }
}
