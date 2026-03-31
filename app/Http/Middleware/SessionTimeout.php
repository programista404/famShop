<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Setting;

class SessionTimeout
{
    public function handle($request, Closure $next)
    {
//        if (Auth::check()) {
//            $lastActivity = Session::get('lastActivityTime');
//            $sessionPeriod = Setting::first()->session_period ?? 120;
//            if ($lastActivity && (time() - $lastActivity) > ($sessionPeriod * 60)) {
//                Auth::logout();
//                Session::flush();
//                return redirect()->route('main_admin.login')
//                ->withErrors(['message' => 'Session expired. Please log in again.']);
//            }
//
//            Session::put('lastActivityTime', time());
//        }

        return $next($request);
    }
}

