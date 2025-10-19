@extends('layouts.app')

@section('content')
<div class="section-header">
    <h1>Edit Landing Page</h1>
</div>

<div class="section-body">
    <div class="card">
        <div class="card-body">
            <ul>
                <li><a href="{{ route('admin.landing.edit', ['section' => 'hero']) }}">Hero Section</a></li>
                <li><a href="{{ route('admin.landing.edit', ['section' => 'about']) }}">About Section</a></li>
                <li><a href="{{ route('admin.landing.edit', ['section' => 'articles']) }}">Articles Section</a></li>
                <li><a href="{{ route('admin.landing.edit', ['section' => 'faq']) }}">FAQ Section</a></li>
                <li><a href="{{ route('admin.landing.edit', ['section' => 'stores']) }}">Stores Section</a></li>
                <li><a href="{{ route('admin.landing.edit', ['section' => 'contact']) }}">Contact Section</a></li>
                <li><a href="{{ route('admin.landing.edit', ['section' => 'cta']) }}">Final CTA Section</a></li>
            </ul>
        </div>
    </div>
</div>
@endsection
