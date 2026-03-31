@extends('layouts.app', ['title' => 'Scan Product'])

@section('content')
    <div class="screen-shell screen-top">
        <div class="top-nav">
            <a href="/dashboard" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
            <span class="badge-soft">{{ $activeMember?->name_member ?? 'No active member' }}</span>
        </div>

        <div class="content-block">
            <div class="panel-card scan-panel mb-3">
                <div class="text-center mb-3">
                    <h2 class="page-title mb-2" style="font-size:1.8rem;">Scan Product</h2>
                    <p class="tagline mb-0">Use the camera or enter a barcode manually.</p>
                </div>

                <div class="scan-container">
                    <div id="scanner-container" class="scanner-viewfinder">
                        <div class="scan-line"></div>
                    </div>
                </div>

                <div class="scan-help-card">
                    <div class="scan-help-icon">
                        <i class="bi bi-lightbulb"></i>
                    </div>
                    <div>
                        <h6>How to scan</h6>
                        <p>Hold the phone steady, place the barcode inside the frame, and use good lighting. The scan will submit automatically once detected.</p>
                    </div>
                </div>
            </div>

            <div class="panel-card">
                <form id="scan-form" method="POST" action="/scan/barcode">
                    @csrf
                    <div class="custom-input-group">
                        <input id="barcode-input" class="custom-input" type="text" name="barcode" placeholder="Barcode number">
                    </div>
                    <button class="btn btn-main" type="submit">Check Product</button>
                </form>
                <p class="muted-note mb-0">If the camera does not start, type the barcode number manually.</p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/@ericblade/quagga2/dist/quagga.min.js"></script>
    <script src="{{ asset('assets/js/scanner.js') }}"></script>
@endsection
