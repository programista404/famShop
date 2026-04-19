<?php

use App\Models\ShoppingCart;
use App\Models\Vendor;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Str;
function isNavbarActive(string $url): string
{
    return Request()->is($url) ? 'active' : '';
}
function isNavbarTreeActive(string $url): string
{
    return Request()->is(app()->getLocale().'/'.$url) ? 'is-expanded' : '';
}
function isFullUrl(string $url): string
{
    return Request()->fullUrl() == url(app()->getLocale().'/'.$url) ? 'active' : '';
}
function getAuthByGuard(string $guard): Authenticatable
{
    return auth()->guard($guard)->user();
}
function hasRole($roleName): bool
{
    $user = Auth::user();
    if ($user->id == 1 && $roleName != 'normal') {
        return true;
    }
    // Ensure user is authenticated and has a role
    if ($user && $user->role) {
        return $user->role->name === $roleName;
    }

    return false;
}
function hasPermission($permissionName)
{
    $user = Auth::user();
    if ($user->id == 1) {
        return true;
    }

    // Ensure user is authenticated and has permissions through their role
    if ($user && $user->role) {
        return $user->role->permissions()->where('perm_name', $permissionName)->exists();
    }

    return false;
}



 function getNotifications()
{
    $vendor = Vendor::where('user_id', Auth::id())->first();
    return Order::whereHas('orderItems.product', function($q) use ($vendor) {
        $q->where('vendor_id', $vendor->vendor_id);
    })->latest('order_date')->take(5)->get();

}

function famshopDefaultProductImage(): string
{
    return 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=600&q=80';
}

function famshopProductImage(?string $imageUrl): string
{
    return filled($imageUrl) ? $imageUrl : famshopDefaultProductImage();
}

function famshopUserPhoto(?string $photoPath): string
{
    if (! filled($photoPath)) {
        return '';
    }

    if (str_starts_with($photoPath, 'http://') || str_starts_with($photoPath, 'https://')) {
        return $photoPath;
    }

    $cleanPath = ltrim($photoPath, '/');

    if (file_exists(public_path($cleanPath))) {
        return asset($cleanPath);
    }

    if (file_exists(public_path('storage/' . $cleanPath))) {
        return asset('storage/' . $cleanPath);
    }

    return asset($cleanPath);
}

function famshopStorePublicUpload($file, string $folder): string
{
    $folder = trim($folder, '/');
    $targetDir = public_path($folder);

    if (! File::exists($targetDir)) {
        File::makeDirectory($targetDir, 0755, true);
    }

    $filename = now()->format('YmdHis') . '-' . Str::uuid() . '.' . $file->getClientOriginalExtension();
    $file->move($targetDir, $filename);

    return $folder . '/' . $filename;
}

function famshopCartCount(): int
{
    if (! Auth::check()) {
        return 0;
    }

    return (int) ShoppingCart::query()
        ->where('user_id', Auth::id())
        ->whereNull('purchase_date')
        ->withSum('items', 'quantity')
        ->value('items_sum_quantity');
}
