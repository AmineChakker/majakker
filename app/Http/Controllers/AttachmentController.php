<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreAttachmentRequest;
use App\Models\PostAttachment;
use Illuminate\Support\Str;

class AttachmentController extends Controller
{
    public function store(StoreAttachmentRequest $request)
    {
        $file = $request->file('file');
        $mime = $file->getMimeType();
        $isImage = str_starts_with($mime, 'image/');
        $kind = $isImage ? 'image' : 'file';

        $year  = now()->format('Y');
        $month = now()->format('m');
        $uuid  = Str::uuid();
        $ext   = $file->getClientOriginalExtension();
        $path  = "attachments/{$year}/{$month}/{$uuid}.{$ext}";

        $file->storeAs('public/' . dirname($path), basename($path));

        $attachment = PostAttachment::create([
            'post_id'       => null, // linked when post is submitted
            'kind'          => $kind,
            'path'          => $path,
            'original_name' => $file->getClientOriginalName(),
            'file_size'     => $file->getSize(),
            'mime_type'     => $mime,
        ]);

        return response()->json([
            'id'            => $attachment->id,
            'url'           => asset('storage/' . $path),
            'kind'          => $kind,
            'original_name' => $attachment->original_name,
            'file_size'     => $attachment->file_size,
        ]);
    }
}
