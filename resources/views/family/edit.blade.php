@extends('layouts.app', ['title' => 'Edit Family Member'])

@section('content')
    <div class="screen-shell screen-top">
        <div class="top-nav">
            <a href="/family" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
        </div>
        <div class="content-block">
            <div class="panel-card">
                <h2 class="page-title mb-3" style="font-size:1.7rem;">Edit {{ $member->name_member }}</h2>
                <form method="POST" action="/family/{{ $member->id }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('family.partials.form', ['member' => $member, 'allergyOptions' => $allergyOptions])
                    <button class="btn btn-main mt-4" type="submit">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
@endsection
