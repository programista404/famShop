@extends('layouts.app', ['title' => 'My Cart'])

@section('content')
    <div class="screen-shell screen-top">
        <div class="top-nav">
            <a href="/dashboard" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
            <i class="bi bi-trash3 text-danger"></i>
        </div>
        <div class="content-block">
            <div class="budget-card mb-3" style="background:#1d3557;color:white;">
                <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span class="fw-bold">{{ number_format((float) ($cart->total_cost ?? 0), 2) }} SAR</span></div>
                <div class="d-flex justify-content-between"><span>Cart Status</span><span class="fw-bold">{{ $cart ? 'Active' : 'Empty' }}</span></div>
            </div>

            @if ($cart?->member)
                <div class="cart-safety-overview {{ ($unsafeCount ?? 0) > 0 ? 'is-alert' : 'is-safe' }}">
                    <div class="cart-safety-overview-icon">
                        <i class="bi {{ ($unsafeCount ?? 0) > 0 ? 'bi-exclamation-triangle-fill' : 'bi-shield-check' }}"></i>
                    </div>
                    <div class="cart-safety-overview-copy">
                        <div class="cart-safety-overview-head">
                            <span class="cart-safety-overview-kicker">Safety overview</span>
                            <span class="cart-safety-overview-chip {{ ($unsafeCount ?? 0) > 0 ? 'is-alert' : 'is-safe' }}">
                                {{ ($unsafeCount ?? 0) > 0 ? 'Needs review' : 'All clear' }}
                            </span>
                        </div>
                        <h5>{{ $unsafeCount > 0 ? 'Some items need attention' : 'All items are safe for ' . $cart->member->name_member }}</h5>
                        <p>
                            @if (($unsafeCount ?? 0) > 0)
                                {{ $unsafeCount }} item(s) may not suit {{ $cart->member->name_member }}.
                                The card below shows the reason for each product.
                            @else
                                Safe for {{ $cart->member->name_member }}. No allergy or budget issues were detected.
                            @endif
                        </p>
                    </div>
                    <div class="cart-safety-overview-stats">
                        <div>
                            <span>Safe</span>
                            <strong>{{ $safeCount ?? 0 }}</strong>
                        </div>
                        <div>
                            <span>Review</span>
                            <strong>{{ $unsafeCount ?? 0 }}</strong>
                        </div>
                    </div>
                </div>
            @endif

            @if ($cart?->member?->budget)
                @php
                    $budget = $cart->member->budget;
                    $dailyLeft = ($budget->daily_budget ?? 0) - ($budget->daily_spent ?? 0);
                    $weeklyLeft = ($budget->weekly_budget ?? 0) - ($budget->weekly_spent ?? 0);
                    $monthlyLeft = ($budget->monthly_budget ?? 0) - ($budget->monthly_spent ?? 0);
                @endphp
                <div class="panel-card mb-3">
                    <div class="section-header mt-0">
                        <h5 class="mb-0">Selected Member Budget</h5>
                        <span class="badge-soft">{{ $cart->member->name_member }}</span>
                    </div>
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="budget-stat-box">
                                <span>Daily</span>
                                <strong>{{ number_format($dailyLeft, 2) }}</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="budget-stat-box">
                                <span>Weekly</span>
                                <strong>{{ number_format($weeklyLeft, 2) }}</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="budget-stat-box">
                                <span>Monthly</span>
                                <strong>{{ number_format($monthlyLeft, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="stack-list">
                @forelse (($cart->items ?? collect()) as $item)
                    @php $check = $itemChecks[$item->id] ?? null; @endphp
                    <div class="cart-item cart-item-safety {{ $check && ! $check['safe'] ? 'is-alert' : 'is-safe' }}">
                        <div class="item-main">
                            <div class="cart-item-head">
                                <div>
                                    <div class="item-title">{{ $item->product->pr_name }}</div>
                                    <div class="muted-note">{{ number_format($item->total_price, 2) }} SAR &bull; Qty {{ $item->quantity }}</div>
                                </div>
                                @if ($check)
                                    <span class="history-status-badge {{ $check['safe'] ? 'is-safe' : 'is-alert' }}">
                                        {{ $check['status'] }}
                                    </span>
                                @endif
                            </div>

                        </div>



                        <div class="inline-actions">
                            <form method="POST" action="/cart/{{ $item->id }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                <button class="mini-btn success" type="submit">+</button>
                            </form>
                            <form method="POST" action="/cart/{{ $item->id }}">
                                @csrf
                                @method('DELETE')
                                <button class="mini-btn danger" type="submit">Remove</button>
                            </form>
                        </div>

                        @if ($check)
                            <div class="cart-safety-box {{ $check['safe'] ? 'is-safe' : 'is-alert' }}">
                                <p>{{ $check['message'] }}</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="cart-item">
                        <div class="item-main">
                            <div class="item-title">Your cart is empty</div>
                            <div class="muted-note">Add safe alternatives or scanned products to continue.</div>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($cart && $cart->items->isNotEmpty())
                <div class="mt-3">
                    <form method="POST" action="/cart/checkout">
                        @csrf
                        <button class="btn btn-main" type="submit">Proceed to Checkout</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
