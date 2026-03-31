@extends('layouts.app', ['title' => 'Shopping List'])

@section('content')
    <div class="screen-shell screen-top">
        <div class="top-nav">
            <a href="/dashboard" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
            <i class="bi bi-list-check"></i>
        </div>
        <div class="content-block">
            <div class="panel-card mb-3">
                <h5 class="mb-3">Add Shopping List Item</h5>
                <form method="POST" action="/list">
                    @csrf
                    <div class="custom-input-group">
                        <select class="custom-select" name="member_id">
                            @foreach ($members as $member)
                                <option value="{{ $member->id }}" {{ $activeMemberId == $member->id ? 'selected' : '' }}>{{ $member->name_member }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="custom-input-group">
                        <input class="custom-input" type="text" name="item_name" placeholder="Add a list item">
                    </div>
                    <button class="btn btn-main" type="submit">Add Item</button>
                </form>
            </div>

            <div class="stack-list">
                @forelse ($items as $item)
                    <div class="cart-item">
                        <div class="item-main">
                            <div class="item-title {{ $item->is_checked ? 'text-decoration-line-through text-muted' : '' }}">{{ $item->item_name }}</div>
                        </div>
                        <div class="inline-actions">
                            <form method="POST" action="/list/{{ $item->id }}/toggle">
                                @csrf
                                @method('PATCH')
                                <button class="mini-btn success" type="submit">{{ $item->is_checked ? 'Undo' : 'Done' }}</button>
                            </form>
                            <form method="POST" action="/list/{{ $item->id }}">
                                @csrf
                                @method('DELETE')
                                <button class="mini-btn danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="cart-item">
                        <div class="item-main">
                            <div class="item-title">No shopping items yet</div>
                            <div class="muted-note">Pick a family member and add essentials here.</div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
