<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'body'            => ['required', 'string', 'max:5000'],
            'title'           => ['nullable', 'string', 'max:255'],
            'group_id'        => ['nullable', 'integer', 'exists:groups,id'],
            'attachment_ids'  => ['nullable', 'array'],
            'attachment_ids.*'=> ['integer', 'exists:post_attachments,id'],
        ];
    }
}
