<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SeoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentPath = $request->path();
        $fullUrl = $request->url();
        // Check for exact path like 'about-us' or '/about-us' or the full URL
        $seoSetting = \App\Models\SeoSetting::where('route_name', $currentPath)
            ->orWhere('route_name', '/' . $currentPath)
            ->orWhere('route_name', $fullUrl)
            ->first();

        // If not found, try route name
        if (!$seoSetting && $request->route()) {
            $seoSetting = \App\Models\SeoSetting::where('route_name', $request->route()->getName())->first();
        }

        view()->share('customSeoSetting', $seoSetting);

        return $next($request);
    }
}
