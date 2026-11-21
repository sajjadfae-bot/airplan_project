<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // API-only پروژه: هیچ روت login وجود ندارد، بنابراین همیشه 401 JSON برگردانده میشود.
        return null;
    }
}
