@extends('layouts.app', ['title' => 'Payment'])

@section('content')
    <div class="screen-shell screen-top">
        <div class="top-nav">
            <a href="/cart" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
            <span class="badge-soft">Fake Payment</span>
        </div>

        <div class="content-block">
            <div class="hero-panel text-center mb-3">
                <div class="avatar-circle mx-auto d-flex align-items-center justify-content-center profile-security-avatar">
                    <i class="bi bi-credit-card-2-front"></i>
                </div>
                <h3 class="mt-3 mb-1">Payment Preview</h3>
                <p class="muted-note mb-0">This is a demo payment page only. No real payment will be processed and no cart data will be changed.</p>
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
                    <h5 class="mb-0">Payment Details</h5>
                </div>
                <div class="custom-input-group">
                    <input class="custom-input" type="text" value="4111 1111 1111 1111" readonly>
                </div>
                <div class="row g-2">
                    <div class="col-7">
                        <div class="custom-input-group mb-0">
                            <input class="custom-input" type="text" value="Demo User" readonly>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="custom-input-group mb-0">
                            <input class="custom-input" type="text" value="12/30" readonly>
                        </div>
                    </div>
                </div>
                <div class="custom-input-group mt-3">
                    <input class="custom-input" type="text" value="123" readonly>
                </div>
                <div class="support-modal-actions mt-2">
                    <a href="/cart" class="btn btn-soft-neutral text-decoration-none">Back to Cart</a>
                    <form method="POST" action="/cart/payment/done" class="w-100">
                        @csrf
                        <button class="btn btn-main" type="submit">Done Checkout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
