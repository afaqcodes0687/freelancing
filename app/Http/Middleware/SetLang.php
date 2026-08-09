<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SetLang
{

    public function handle($request, Closure $next)
    {
        $defaultLang = Language::where('default',1)->first() ?? '';
        $lang = null;

        if ($request->hasHeader('x-lang')) {
            $lang = $request->header('x-lang');
        } elseif ($request->has('lang')) {
            $lang = $request->get('lang');
        } elseif (session()->has('lang')) {
            $lang = session()->get('lang');
        }

        if ($lang) {
            $current_lang = Language::where('slug', $lang)->first();
            if ($current_lang) {
                Carbon::setLocale($current_lang->slug);
                app()->setLocale($current_lang->slug);
            } else {
                Carbon::setLocale($defaultLang->slug ?? '');
                app()->setLocale($defaultLang->slug ?? '');
            }
        } elseif ($request->is('api/*')) {
            Carbon::setLocale('en_GB');
            app()->setLocale('en_GB');
        } else {
            Carbon::setLocale($defaultLang->slug ?? '');
            app()->setLocale($defaultLang->slug ?? '');
        }

        return $next($request);
    }
}
