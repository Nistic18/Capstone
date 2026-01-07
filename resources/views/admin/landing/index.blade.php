@extends('layouts.app')

@section('content')
<link rel="icon" type="image/png" href="{{ asset('img/avatar/dried-fish-logo.png') }}">

<div class="section-body">
    <div class="section" style="height: 100px; display: flex; align-items: flex-end;">
    <h1>Edit Landing Page</h1>
</div>
    <div class="row">
        {{-- Hero Section --}}
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Hero Section</h4>
                </div>
                <div class="card-body text-center">
                    <a href="{{ route('admin.landing.edit', ['section' => 'hero']) }}" class="btn btn-primary btn-block">Edit Hero</a>
                </div>
            </div>
        </div>

        {{-- About Section --}}
        {{-- <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card card-info">
                <div class="card-header">
                    <h4>About Section</h4>
                </div>
                <div class="card-body text-center">
                    <a href="{{ route('admin.landing.edit', ['section' => 'about']) }}" class="btn btn-info btn-block">Edit About</a>
                </div>
            </div>
        </div> --}}

        {{-- Articles Section --}}
        {{-- <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h4>Articles Section</h4>
                </div>
                <div class="card-body text-center">
                    <a href="{{ route('admin.landing.edit', ['section' => 'articles']) }}" class="btn btn-warning btn-block">Edit Articles</a>
                </div>
            </div>
        </div> --}}

        {{-- FAQ Section --}}
        {{-- <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card card-success">
                <div class="card-header">
                    <h4>FAQ Section</h4>
                </div>
                <div class="card-body text-center">
                    <a href="{{ route('admin.landing.edit', ['section' => 'faq']) }}" class="btn btn-success btn-block">Edit FAQ</a>
                </div>
            </div>
        </div> --}}

        {{-- Contact Section --}}
        {{-- <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card card-dark">
                <div class="card-header">
                    <h4>Contact Section</h4>
                </div>
                <div class="card-body text-center">
                    <a href="{{ route('admin.landing.edit', ['section' => 'contact']) }}" class="btn btn-dark btn-block">Edit Contact</a>
                </div>
            </div>
        </div> --}}

        {{-- Final CTA Section --}}
        {{-- <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h4>Final CTA Section</h4>
                </div>
                <div class="card-body text-center">
                    <a href="{{ route('admin.landing.edit', ['section' => 'cta']) }}" class="btn btn-danger btn-block">Edit CTA</a>
                </div>
            </div>
        </div> --}}
    </div>
</div>
@endsection
