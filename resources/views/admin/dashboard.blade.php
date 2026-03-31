@extends('layouts.admin', ['title' => 'Admin Dashboard'])

@section('content')
    <div class="admin-screen">
        <div class="admin-hero-card mb-3">
            <p class="admin-kicker">Welcome Back</p>
            <h3>Hello, {{ auth()->user()->name }}</h3>
            <p class="muted-note mb-0">Track platform growth, customer activity, and support health from one dashboard.</p>

            <div class="admin-stats-grid mt-4">
                <div class="admin-stat-card">
                    <div class="admin-stat-icon blue-bg"><i class="bi bi-box-seam"></i></div>
                    <span>Products</span>
                    <strong>{{ $stats['products'] }}</strong>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-icon green-bg"><i class="bi bi-people-fill"></i></div>
                    <span>Users</span>
                    <strong>{{ $stats['users'] }}</strong>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-icon sand-bg"><i class="bi bi-chat-square-heart"></i></div>
                    <span>Feedback</span>
                    <strong>{{ $stats['feedback'] }}</strong>
                </div>
                <div class="admin-stat-card">
                    <div class="admin-stat-icon red-bg"><i class="bi bi-life-preserver"></i></div>
                    <span>Open Tickets</span>
                    <strong>{{ $stats['open_tickets'] }}</strong>
                </div>
            </div>
        </div>

        <div class="admin-chart-grid">
            <div class="panel-card admin-chart-card">
                <div class="section-header mt-0">
                    <h5 class="mb-0">Users vs Products</h5>
                </div>
                <div id="adminGrowthChart" class="admin-chart-box"></div>
            </div>
            <div class="panel-card admin-chart-card">
                <div class="section-header mt-0">
                    <h5 class="mb-0">Feedback Types</h5>
                </div>
                <div id="adminFeedbackChart" class="admin-chart-box"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        const growthChart = new ApexCharts(document.querySelector('#adminGrowthChart'), {
            chart: {
                type: 'area',
                height: 320,
                toolbar: { show: false }
            },
            colors: ['#4a628a', '#8eb9cf'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            series: [
                { name: 'Users', data: @json($usersSeries) },
                { name: 'Products', data: @json($productsSeries) }
            ],
            xaxis: {
                categories: @json($chartLabels)
            },
            grid: {
                borderColor: '#edf1f5'
            },
            legend: {
                position: 'top'
            }
        });
        growthChart.render();

        const feedbackChart = new ApexCharts(document.querySelector('#adminFeedbackChart'), {
            chart: {
                type: 'donut',
                height: 320
            },
            labels: ['Ratings', 'Suggestions', 'Bug Reports'],
            series: @json($feedbackSeries),
            colors: ['#8eb9cf', '#d1e2c4', '#e8dcc4'],
            legend: {
                position: 'bottom'
            }
        });
        feedbackChart.render();
    </script>
@endsection
