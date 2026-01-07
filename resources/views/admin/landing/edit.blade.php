@extends('layouts.app')

@section('content')
    <link rel="icon" type="image/png" href="{{ asset('img/avatar/dried-fish-logo.png') }}">
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Edit Section: {{ ucfirst($content->section) }}</h4>
        <a href="{{ route('admin.landing.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.landing.update', $content->section) }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Section field --}}
            <div class="form-group mb-3">
                <label>Section</label>
                <input type="text" class="form-control" value="{{ $content->section }}" readonly>
                <input type="hidden" name="section" value="{{ $content->section }}">
            </div>

            {{-- Section Title --}}
            <div class="form-group mb-3">
                <label>Section Title</label>
                <input type="text" name="title" class="form-control" value="{{ $content->title }}">
            </div>

            {{-- Section Content --}}
            <div class="form-group mb-3">
                <label>Section Content</label>
                <textarea name="content" class="form-control" rows="4">{{ $content->content }}</textarea>
            </div>

            {{-- Special logic for HERO section --}}
            @if($content->section === 'hero')
                {{-- Hero Background Image --}}
                <div class="form-group mb-3">
                    <label>Hero Background Image</label><br>
                    @if(!empty($content->image))
                        <img src="{{ asset($content->image) }}" alt="Hero Image" class="rounded mb-2" width="300">
                    @endif
                    <input type="file" name="image" class="form-control">
                    <small class="text-muted">Upload a new image to change the landing page background.</small>
                </div>

            {{-- Special logic for FAQ section --}}
            @elseif($content->section === 'faq')
                <div class="alert alert-info">
                    <strong>FAQ Section:</strong> Add and manage frequently asked questions below.
                </div>

                @foreach($cards as $index => $card)
                    <div class="border p-3 mb-3 rounded bg-light">
                        <input type="hidden" name="cards[{{ $card->id }}][id]" value="{{ $card->id }}">

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">FAQ #{{ $index + 1 }}</h5>
                            <span class="badge bg-secondary">Order: {{ $card->order ?? 0 }}</span>
                        </div>

                        <div class="form-group mb-2">
                            <label><strong>Question</strong></label>
                            <input type="text" name="cards[{{ $card->id }}][title]" class="form-control" value="{{ $card->title }}" placeholder="Enter FAQ question">
                        </div>

                        <div class="form-group mb-2">
                            <label><strong>Answer</strong></label>
                            <textarea name="cards[{{ $card->id }}][content]" class="form-control" rows="3" placeholder="Enter FAQ answer">{{ $card->content }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    <label>Display Order</label>
                                    <input type="number" name="cards[{{ $card->id }}][order]" class="form-control" value="{{ $card->order ?? 0 }}">
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check">
                                    <input type="checkbox" name="cards[{{ $card->id }}][delete]" value="1" class="form-check-input">
                                    <label class="form-check-label text-danger">Delete this FAQ</label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-3">
                    <button type="button" class="btn btn-success" id="addCardBtn">+ Add New FAQ</button>
                </div>

                <div id="newCardForm" class="border p-3 mt-3 bg-light rounded" style="display: none;">
                    <h5>Add New FAQ</h5>

                    <div class="form-group mb-2">
                        <label><strong>Question</strong></label>
                        <input type="text" name="cards[new][title]" class="form-control" placeholder="Enter FAQ question">
                    </div>

                    <div class="form-group mb-2">
                        <label><strong>Answer</strong></label>
                        <textarea name="cards[new][content]" class="form-control" rows="3" placeholder="Enter FAQ answer"></textarea>
                    </div>

                    <div class="form-group mb-2">
                        <label>Display Order</label>
                        <input type="number" name="cards[new][order]" class="form-control" placeholder="e.g. 11">
                    </div>
                </div>

            {{-- Articles section - title and content only --}}
            @elseif($content->section === 'articles')
                <div class="alert alert-info">
                    <strong>Articles Section:</strong> Only section title and content can be edited.
                </div>

            @else
                {{-- Default cards for other sections --}}
                @foreach($cards as $card)
                    <div class="border p-3 mb-3 rounded">
                        <input type="hidden" name="cards[{{ $card->id }}][id]" value="{{ $card->id }}">

                        <div class="form-group mb-2">
                            <label>Card Title</label>
                            <input type="text" name="cards[{{ $card->id }}][title]" class="form-control" value="{{ $card->title }}">
                        </div>

                        <div class="form-group mb-2">
                            <label>Card Content</label>
                            <textarea name="cards[{{ $card->id }}][content]" class="form-control">{{ $card->content }}</textarea>
                        </div>

                        <div class="form-group mb-2">
                            <label>Order</label>
                            <input type="number" name="cards[{{ $card->id }}][order]" class="form-control" value="{{ $card->order ?? 0 }}">
                        </div>

                        <div class="form-check">
                            <input type="checkbox" name="cards[{{ $card->id }}][delete]" value="1">
                            <label class="form-check-label text-danger">Delete Card</label>
                        </div>
                    </div>
                @endforeach

                <div class="mt-3">
                    <button type="button" class="btn btn-success" id="addCardBtn">+ Add New Card</button>
                </div>

                <div id="newCardForm" class="border p-3 mt-3 bg-light rounded" style="display: none;">
                    <h5>Add New Card</h5>

                    <div class="form-group mb-2">
                        <label>Title</label>
                        <input type="text" name="cards[new][title]" class="form-control">
                    </div>

                    <div class="form-group mb-2">
                        <label>Content</label>
                        <textarea name="cards[new][content]" class="form-control"></textarea>
                    </div>

                    <div class="form-group mb-2">
                        <label>Order</label>
                        <input type="number" name="cards[new][order]" class="form-control" placeholder="e.g. 1">
                    </div>
                </div>
            @endif

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- JavaScript for all non-hero and non-articles sections --}}
@push('scripts')
@if($content->section !== 'hero' && $content->section !== 'articles')
<script>
    document.getElementById('addCardBtn').addEventListener('click', function() {
        var form = document.getElementById('newCardForm');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    });
</script>
@endif
@endpush
@endsection