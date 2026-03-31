<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Product;
use App\Models\SupportTicket;
use App\Models\User;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $months = collect(range(5, 0))->map(function ($offset) {
            return Carbon::now()->subMonths($offset);
        })->push(Carbon::now());

        $chartLabels = $months->map(fn ($month) => $month->format('M Y'))->values();
        $usersSeries = $months->map(function ($month) {
            return User::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count();
        })->values();
        $productsSeries = $months->map(function ($month) {
            return Product::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count();
        })->values();
        $feedbackSeries = [
            Feedback::where('type', 'rating')->count(),
            Feedback::where('type', 'suggestion')->count(),
            Feedback::where('type', 'bug')->count(),
        ];

        return view('admin.dashboard', [
            'stats' => [
                'users' => User::count(),
                'products' => Product::count(),
                'admins' => User::all()->filter->isAdmin()->count(),
                'open_tickets' => SupportTicket::where('status', 'open')->count(),
                'feedback' => Feedback::count(),
            ],
            'chartLabels' => $chartLabels,
            'usersSeries' => $usersSeries,
            'productsSeries' => $productsSeries,
            'feedbackSeries' => $feedbackSeries,
        ]);
    }
}
