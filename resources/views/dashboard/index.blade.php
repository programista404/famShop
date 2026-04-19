@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
    @php
        $selfMember = $members->first(function ($member) {
            return strcasecmp($member->name_member, auth()->user()->name) === 0;
        });
        $budget = $activeMember?->budget;
        $dailyLeft = $budget ? ($budget->daily_budget ?? 0) - ($budget->daily_spent ?? 0) : null;
        $weeklyLeft = $budget ? ($budget->weekly_budget ?? 0) - ($budget->weekly_spent ?? 0) : null;
        $monthlyLeft = $budget ? ($budget->monthly_budget ?? 0) - ($budget->monthly_spent ?? 0) : null;
        $dailyProgress = $budget && ($budget->daily_budget ?? 0) > 0
            ? min(100, max(0, (($budget->daily_spent ?? 0) / $budget->daily_budget) * 100))
            : 0;
        $statusLabel = static function ($status) {
            return ucfirst(str_replace('_', ' ', $status));
        };
        $statusClass = static function ($status) {
            return str_contains($status, 'unsafe') || str_contains($status, 'over_budget') ? 'is-alert' : 'is-safe';
        };
    @endphp

    <div class="screen-shell screen-top">
        <div class="top-nav">
            <a href="/profile" class="profile-chip text-decoration-none">
                @if (auth()->user()->profile_photo)
                    <img src="{{ famshopUserPhoto(auth()->user()->profile_photo) }}" alt="{{ auth()->user()->name }}">
                @else
                    <span class="avatar-circle d-flex align-items-center justify-content-center"><i class="bi bi-person-fill"></i></span>
                @endif
                <span>{{ auth()->user()->name }}</span>
            </a>
            <a href="/cart" class="text-decoration-none text-reset cart-link">
                <span class="nav-icon-wrap">
                    <i class="bi bi-cart3"></i>
                    @if (($globalCartCount ?? 0) > 0)
                        <span class="cart-badge">{{ $globalCartCount }}</span>
                    @endif
                </span>
            </a>
        </div>

        <div class="content-block home-clean">
            <section class="home-summary-card">
                <div class="home-summary-head">
                    <div>
                        <p class="home-kicker">Home</p>
                        <h6>{{ $activeMember?->name_member ?? 'Select a family member' }}</h6>
                        <p class="muted-note mb-0">
                            @if ($activeMember)
                                Ready for product scans and budget tracking.
                            @else
                                Choose a member to personalize recommendations and safety checks.
                            @endif
                        </p>
                    </div>
                    <a href="/scan" class="home-scan-visual text-decoration-none">
                        <div class="scan-illustration" aria-hidden="true">
                            <div class="scan-illustration-frame">
                                <i style="    font-size: 78px;
    color: #00000094;" class="bi bi-upc-scan"></i>
                            </div>
{{--                            <div class="scan-illustration-lines">--}}
{{--                                <span></span>--}}
{{--                                <span></span>--}}
{{--                                <span></span>--}}
{{--                                <span></span>--}}
{{--                            </div>--}}
                        </div>
                        <span class="home-scan-label">Scan Product</span>
                    </a>
                </div>

                <div class="home-meta-row">
                    <div class="home-meta-box">
                        <div class="home-meta-icon family">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <span>Family : <strong>{{ $members->count() }}</strong></span>

                    </div>
                    <div class="home-meta-box">
                        <div class="home-meta-icon cart">
                            <i class="bi bi-basket2-fill"></i>
                        </div>
                        <span>Cart :    <strong>{{ $globalCartCount ?? 0 }}</strong></span>

                    </div>
                    <div class="home-meta-box">
                        <div class="home-meta-icon scan">
                            <i class="bi bi-upc-scan"></i>
                        </div>
                        <span>Scans :     <strong>{{ $history->count() }}</strong> </span>

                    </div>
                </div>
            </section>

            <section class="panel-card home-member-card">
                <div class="section-header mt-0">
                    <h5 class="mb-0">Active member</h5>
                    <a href="/family" class="see-all">Family</a>
                </div>

                <div class="home-self-shop-card mb-3">
                    <div class="home-self-shop-icon">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div class="home-self-shop-copy">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="home-self-shop-kicker">Shop for myself</span>
                            @if ($selfMember)
                                <span class="home-self-shop-chip">Ready</span>
                            @endif
                        </div>
                        <p class="mb-0">
                            {{ $selfMember ? 'Use your own family profile while shopping and scanning products.' : 'Create a family profile with your name first, then shop under it.' }}
                        </p>
                    </div>
                    <div class="home-self-shop-action">
                        @if ($selfMember)
                            <form method="POST" action="/scan/member" class="m-0">
                                @csrf
                                <input type="hidden" name="member_id" value="{{ $selfMember->id }}">
                                <button class="btn btn-main home-self-shop-btn" type="submit">
                                    <i class="bi bi-person-badge"></i>
                                    <span>Use my profile</span>
                                </button>
                            </form>
                        @else
                            <a href="/family" class="btn btn-soft-neutral home-self-shop-btn text-decoration-none">
                                <i class="bi bi-plus-lg"></i>
                                <span>Create profile</span>
                            </a>
                        @endif
                    </div>
                </div>

                <form method="POST" action="/scan/member">
                    @csrf
                    <div class="custom-input-group mb-3">
                        <select class="custom-select" name="member_id">
                            @forelse ($members as $member)
                                <option value="{{ $member->id }}" {{ $activeMember && $activeMember->id === $member->id ? 'selected' : '' }}>
                                    {{ $selfMember && $selfMember->id === $member->id ? 'My profile - ' . $member->name_member : $member->name_member }}
                                </option>
                            @empty
                                <option value="">No family members available</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="home-inline-actions">
                        <button class="btn btn-main home-member-save-btn" type="submit" {{ $members->isEmpty() ? 'disabled' : '' }}>Save</button>
                    </div>
                </form>
            </section>

            @if ($activeMember)
                <section class="panel-card home-budget-card">
                    <div class="section-header mt-0">
                        <h5 class="mb-0">Budget</h5>
                        <a href="/budget/{{ $activeMember->id }}" class="see-all">Manage</a>
                    </div>

                    @if ($budget)
                        <div class="home-budget-top">
                            <div>
                                <span class="home-budget-label">Daily remaining</span>
                                <h3>{{ number_format($dailyLeft, 2) }}</h3>
                            </div>
                            <div class="home-budget-note">
                                {{ number_format($budget->daily_spent ?? 0, 2) }} of {{ number_format($budget->daily_budget ?? 0, 2) }}
                            </div>
                        </div>

                        <div class="budget-bar mb-3">
                            <div class="budget-bar-fill {{ $dailyLeft <= 0 ? 'budget-out' : ($dailyProgress >= 70 ? 'budget-low' : 'budget-ok') }}" style="width: {{ $dailyProgress }}%;"></div>
                        </div>

                        <div class="home-budget-stats">
                            <div class="budget-stat-box">
                                <span>Daily</span>
                                <strong>{{ number_format($dailyLeft, 2) }}</strong>
                            </div>
                            <div class="budget-stat-box">
                                <span>Weekly</span>
                                <strong>{{ number_format($weeklyLeft, 2) }}</strong>
                            </div>
                            <div class="budget-stat-box">
                                <span>Monthly</span>
                                <strong>{{ number_format($monthlyLeft, 2) }}</strong>
                            </div>
                        </div>
                    @else
                        <div class="home-empty">
                            <h6>No budget added</h6>
                            <p class="mb-0">Set a budget for {{ $activeMember->name_member }} to track spending.</p>
                        </div>
                    @endif
                </section>
            @endif



            <section>
                <div class="section-header">
                    <h5 class="mb-0">Recent scans</h5>
                    <a href="/scan/history" class="see-all">See all</a>
                </div>

                <div class="stack-list">
                    @forelse ($history as $record)
                        <div class="history-card home-history-card">
                            <div class="home-history-thumb">
                                <img src="{{ famshopProductImage($record->product->image_url ?? null) }}" alt="{{ $record->product->pr_name ?? 'Product' }}">
                            </div>
                            <div class="home-history-main">
                                <h6>{{ $record->product->pr_name ?? 'Product' }}</h6>
                                <p>{{ $record->member->name_member ?? 'No member' }}</p>
                            </div>
                            <div class="home-history-side">
                                <span class="history-status-badge {{ $statusClass($record->match_status) }}">{{ $statusLabel($record->match_status) }}</span>
                                <small>{{ optional($record->scan_date)->format('M d') }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="history-card home-history-card">
                            <div class="home-history-main">
                                <h6>No scans yet</h6>
                                <p>Your recent product checks will appear here.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
