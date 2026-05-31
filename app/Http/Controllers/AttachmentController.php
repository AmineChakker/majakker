<?php
namespace App\Http\Controllers;

use App\Models\PostAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttachmentController extends Controller
{
    private const VIDEO_MIMES = [
        'video/mp4','video/webm','video/quicktime',
        'video/avi','video/x-matroska','video/x-msvideo',
    ];
    private const IMAGE_MIMES = [
        'image/jpeg','image/jpg','image/png',
        'image/webp','image/gif','image/bmp',
    ];

    public function store(Request $request)
    {
        // Validate — produce JSON errors (request sends Accept: application/json)
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:204800', // 200 MB in KB
                'mimes:jpg,jpeg,png,webp,gif,bmp,pdf,doc,docx,xls,xlsx,ppt,pptx,zip,mp4,mov,webm,avi,mkv',
            ],
        ], [
            'file.required' => 'Aucun fichier reçu. Le fichier est peut-être trop volumineux (limite serveur : ' . ini_get('upload_max_filesize') . ').',
            'file.max'      => 'Le fichier dépasse la limite autorisée (200 Mo).',
            'file.mimes'    => 'Format non supporté. Formats acceptés : images, vidéos, PDF, Office, ZIP.',
        ]);

        $file = $request->file('file');

        // Guard: file must be valid (PHP may have dropped it silently)
        if (!$file || !$file->isValid()) {
            $phpLimit = ini_get('upload_max_filesize');
            return response()->json([
                'errors' => ['file' => [
                    "Fichier non reçu — il dépasse probablement la limite PHP ({$phpLimit}). "
                    . "Redémarrez avec : php -d upload_max_filesize=200M artisan serve"
                ]],
            ], 422);
        }

        $mime = $file->getMimeType() ?? $file->getClientMimeType();

        $kind = match(true) {
            in_array($mime, self::IMAGE_MIMES)                 => 'image',
            in_array($mime, self::VIDEO_MIMES)                 => 'video',
            str_starts_with($mime ?? '', 'image/')             => 'image',
            str_starts_with($mime ?? '', 'video/')             => 'video',
            default                                            => 'file',
        };

        $year = now()->format('Y');
        $month = now()->format('m');
        $uuid  = Str::uuid();
        $ext   = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $dir   = "attachments/{$year}/{$month}";
        $name  = "{$uuid}.{$ext}";

        // Store on the 'public' disk → storage/app/public/{dir}/{name}
        // accessible via public/storage/{dir}/{name} through the symlink
        $file->storeAs($dir, $name, 'public');

        $attachment = PostAttachment::create([
            'post_id'       => null,
            'kind'          => $kind,
            'path'          => "{$dir}/{$name}",
            'original_name' => $file->getClientOriginalName(),
            'file_size'     => $file->getSize(),
            'mime_type'     => $mime,
        ]);

        return response()->json([
            'id'              => $attachment->id,
            'url'             => asset("storage/{$dir}/{$name}"),
            'kind'            => $kind,
            'mime'            => $mime,
            'original_name'   => $attachment->original_name,
            'file_size'       => $attachment->file_size,
            'file_size_human' => $attachment->file_size_human,
        ]);
    }
}
