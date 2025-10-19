@extends('layouts.app')

@section('content')
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

            {{-- Loop existing cards --}}
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

                    {{-- <div class="form-group mb-2">
                        <label>Card Image</label><br>
                        @if($card->image)
                            <img src="{{ asset('storage/'.$card->image) }}" width="150" class="mb-2 rounded"><br>
                        @endif
                        <input type="file" name="cards[{{ $card->id }}][image]" class="form-control">
                    </div> --}}

                    <div class="form-group mb-2">
                        <label>Order</label>
                        <input type="number" name="cards[{{ $card->id }}][order]" class="form-control" value="{{ $card->order ?? 0 }}">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="cards[{{ $card->id }}][delete]" value="1">
                        <label for="delete_{{ $card->id }}" class="form-check-label text-danger">Delete Card</label>
                    </div>
                </div>
            @endforeach

            {{-- Button to show new card form --}}
            <div class="mt-3">
                <button type="button" class="btn btn-success" id="addCardBtn">+ Add New Card</button>
            </div>

            {{-- Hidden Add New Card Form --}}
            <div id="newCardForm" class="border p-3 mt-3 bg-light rounded" style="display: none;">
                <h5>Add New Card</h5>

                <div class="form-group mb-2">
                    <label>Section</label>
                    <input type="text" class="form-control" value="{{ $content->section }}" readonly>
                    <input type="hidden" name="cards[new][section]" value="{{ $content->section }}">
                </div>

                <div class="form-group mb-2">
                    <label>Title</label>
                    <input type="text" name="cards[new][title]" class="form-control">
                </div>

                <div class="form-group mb-2">
                    <label>Content</label>
                    <textarea name="cards[new][content]" class="form-control"></textarea>
                </div>

                {{-- <div class="form-group mb-2">
                    <label>Image</label>
                    <input type="file" name="cards[new][image]" class="form-control">
                </div> --}}

                <div class="form-group mb-2">
                    <label>Order</label>
                    <input type="number" name="cards[new][order]" class="form-control" placeholder="e.g. 1">
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- JavaScript to toggle the new card form --}}
@push('scripts')
<script>
    document.getElementById('addCardBtn').addEventListener('click', function() {
        var form = document.getElementById('newCardForm');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    });
</script>
@endpush

@endsection
