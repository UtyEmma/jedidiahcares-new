<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LegacyRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $parameters = $request->route('slug') !== null ? ['slug' => $request->route('slug')] : [];

        return redirect()->route($request->route('redirectRoute'), $parameters, 301);
    }
}
