<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LegacyRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $destination = str_replace(':slug', rawurlencode((string) $request->route('slug', '')), $request->route('redirectTo'));

        return redirect($destination, 301);
    }
}
