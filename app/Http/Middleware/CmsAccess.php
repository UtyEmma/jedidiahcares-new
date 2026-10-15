<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CmsAccess
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('fluppy/*') || $request->is('livewire*/upload-file')) {
            abort_unless($request->user()?->canAccessCms(), 403);
        }
        $response = $next($request);
        if ($request->is('admin', 'admin/*')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}
