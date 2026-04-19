@extends('layouts.app', ['title' => 'Order Summary'])

@section('content')
    <div class="screen-shell screen-top">
        <div class="top-nav">
            <a href="/cart" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
            <span class="badge-soft">Summary</span>
        </div>

        <div class="content-block">
            <div class="hero-panel text-center mb-3">
                <div class="avatar-circle mx-auto d-flex align-items-center justify-content-center profile-security-avatar">
                    <i class="bi bi-receipt"></i>
                </div>
                <h3 class="mt-3 mb-1">Order Summary</h3>
                <p class="muted-note mb-0">Review the selected member, cart total, and items before confirming the checkout.</p>
            </div>

            <div class="panel-card mb-3">
                <div class="section-header mt-0">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Items</span>
                    <strong>{{ $cart->items->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Family member</span>
                    <strong>{{ $cart->member->name_member ?? 'Not selected' }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Total</span>
                    <strong>{{ number_format((float) $total, 2) }} SAR</strong>
                </div>
            </div>

            <div class="panel-card">
                <div class="section-header mt-0">
                    <h5 class="mb-0">Confirmation</h5>
                </div>
                <div class="support-modal-block mb-3">
                    <span>Next step</span>
                    <p class="mb-0">Press confirm to complete the demo checkout and clear the cart.</p>
                </div>
                <div class="support-modal-actions mt-2">
                    <a href="/cart" class="btn btn-soft-neutral text-decoration-none">Back to Cart</a>
                    <form method="POST" action="/cart/payment/done" class="w-100">
                        @csrf
                        <button class="btn btn-main" type="submit">Confirm Checkout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
