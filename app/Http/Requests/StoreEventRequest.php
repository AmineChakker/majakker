<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user->isDirector() || $user->isTeacher();
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'starts_at'   => ['required', 'date', 'after:now'],
            'location'    => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'color'       => ['nullable', 'in:blue,saffron,atlas,terracotta'],
        ];
    }
}
