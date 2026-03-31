@extends('layouts.app', ['title' => 'Scan History'])

@section('content')
    <div class="screen-shell screen-top">
        <div class="top-nav">
            <a href="/dashboard" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
            <i class="bi bi-clock-history"></i>
        </div>
        <div class="content-block">
            <div class="section-header mt-0">
                <h5>Scan History</h5>
            </div>
            <div class="stack-list">
                @forelse ($records as $record)
                    <div class="history-card">
                        <div class="history-details">
                            <h6>{{ $record->product->pr_name ?? 'Product' }}</h6>
                            <p>{{ optional($record->scan_date)->format('Y-m-d H:i') }} • {{ $record->reason ?: 'No details saved' }}</p>
                        </div>
                        <div class="inline-actions">
                            <form method="POST" action="/scan/rescan/{{ $record->id }}">
                                @csrf
                                <button class="mini-btn" type="submit">Rescan</button>
                            </form>
                            <form method="POST" action="/scan/history/{{ $record->id }}">
                                @csrf
                                @method('DELETE')
                                <button class="mini-btn danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="history-card">
                        <div class="history-details">
                            <h6>No history yet</h6>
                            <p>Your last scanned products will appear here.</p>
                        </div>
                    </div>
                @endforelse
            </div>
            <div class="mt-3">{{ $records->links() }}</div>
        </div>
    </div>
@endsection
