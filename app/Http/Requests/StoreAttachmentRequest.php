<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttachmentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:204800', // 200 MB
                'mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,mp4,mov,webm,avi,mkv',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'Format non supporté. Acceptés : images, vidéos, PDF, Office, ZIP.',
            'file.max'   => 'Fichier trop volumineux (max 200 Mo).',
        ];
    }
}
