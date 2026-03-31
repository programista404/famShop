@extends('layouts.app', ['title' => 'Budget Tracker'])

@section('content')
    <div class="screen-shell screen-top">
        <div class="top-nav">
            <a href="/family" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
            <span class="badge-soft">{{ $member->name_member }}</span>
        </div>

        @php
            $budget = $budget ?? $member->budget;
            $monthlyBudget = (float) ($budget->monthly_budget ?? 0);
            $monthlySpent = (float) ($budget->monthly_spent ?? 0);
            $percentage = $monthlyBudget > 0 ? min(100, ($monthlySpent / $monthlyBudget) * 100) : 0;
            $remaining = $monthlyBudget - $monthlySpent;
            $barClass = $percentage < 50 ? 'budget-ok' : ($percentage < 80 ? 'budget-low' : 'budget-out');
        @endphp

        <div class="content-block">
            <div class="budget-card mb-3" style="background:#1d3557;color:white;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <p class="mb-0 text-uppercase small" style="opacity:.7;">Monthly Remaining</p>
                    <span class="badge-soft" style="background:rgba(255,255,255,.12);color:white;">Member #{{ $member->id }}</span>
                </div>
                <div class="display-6 fw-bold">{{ number_format($remaining, 2) }} <span class="fs-6">SAR</span></div>
                <div class="mt-4">
                    <div class="d-flex justify-content-between small fw-semibold mb-2">
                        <span>Spent: {{ number_format($monthlySpent, 2) }}</span>
                        <span>Budget: {{ number_format($monthlyBudget, 2) }}</span>
                    </div>
                    <div class="budget-bar" style="background:rgba(255,255,255,.12);">
                        <div class="budget-bar-fill {{ $barClass }}" style="width:{{ $percentage }}%"></div>
                    </div>
                </div>
            </div>

            <div class="panel-card mb-3">
                <h5 class="mb-3">Budget Limits</h5>
                <div class="row g-3">
                    <div class="col-4">
                        <div class="panel-card text-center p-3" style="box-shadow:none;">
                            <div class="small text-muted">Daily</div>
                            <div class="fw-bold">{{ number_format($budget->daily_budget ?? 0, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="panel-card text-center p-3" style="box-shadow:none;">
                            <div class="small text-muted">Weekly</div>
                            <div class="fw-bold">{{ number_format($budget->weekly_budget ?? 0, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="panel-card text-center p-3" style="box-shadow:none;">
                            <div class="small text-muted">Monthly</div>
                            <div class="fw-bold">{{ number_format($budget->monthly_budget ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-card">
                <h5 class="mb-3">Update Budget</h5>
                <form method="POST" action="/budget/{{ $member->id }}">
                    @csrf
                    @method('PUT')
                    <div class="custom-input-group">
                        <input class="custom-input" type="number" step="0.01" name="daily_budget" value="{{ old('daily_budget', $budget->daily_budget ?? 0) }}" placeholder="Daily budget">
                    </div>
                    <div class="custom-input-group">
                        <input class="custom-input" type="number" step="0.01" name="weekly_budget" value="{{ old('weekly_budget', $budget->weekly_budget ?? 0) }}" placeholder="Weekly budget">
                    </div>
                    <div class="custom-input-group">
                        <input class="custom-input" type="number" step="0.01" name="monthly_budget" value="{{ old('monthly_budget', $budget->monthly_budget ?? 0) }}" placeholder="Monthly budget">
                    </div>
                    <button class="btn btn-main" type="submit">Update Budget</button>
                </form>
            </div>
        </div>
    </div>
@endsection
