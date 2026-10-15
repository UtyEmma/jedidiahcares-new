<?php

namespace App\Http\Controllers;

use App\Models\CmsMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    public function __invoke(Request $request, string $media, string $conversion = 'original'): BinaryFileResponse
    {
        $item = CmsMedia::where('uuid', $media)->firstOrFail();
        abort_unless($item->isPublic() || $request->user()?->canAccessCms(), 404);
        abort_unless(in_array($conversion, ['original', 'thumb'], true), 404);
        abort_if($conversion !== 'original' && ! $item->hasGeneratedConversion($conversion), 404);
        $path = $item->getPathRelativeToRoot($conversion === 'original' ? '' : $conversion);
        $disk = Storage::disk($conversion === 'original' ? $item->disk : ($item->conversions_disk ?: $item->disk));
        abort_unless($disk->exists($path), 404);
        $response = response()->file($disk->path($path), [
            'Content-Type' => $conversion === 'thumb' ? 'image/webp' : ($item->collection_name === 'captions' ? 'text/vtt' : $item->mime_type),
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
        if (! $item->isPublic()) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }
        if ($item->mime_type === 'application/pdf') {
            $response->setContentDisposition('attachment', $item->file_name);
        }
        $response->setPrivate();

        return $response;
    }
}
