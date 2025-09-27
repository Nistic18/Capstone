@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-3">Gemini AI Chat / Text Generation</h2>

    <textarea id="prompt" class="form-control mb-2" rows="4" placeholder="Enter your prompt"></textarea>
    <button onclick="generateText()" class="btn btn-primary mb-3">Generate</button>

    <h4>Output:</h4>
    <pre id="output"></pre>

    <hr>

    <h2>Embedding Search</h2>
    <input type="text" id="embedText" class="form-control mb-2" placeholder="Enter text for embedding">
    <button onclick="getEmbedding()" class="btn btn-success mb-3">Get Embedding</button>

    <pre id="embeddingOutput"></pre>
</div>

<script>
async function generateText() {
    const response = await fetch('/gemini/generate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            model: 'gemini-2.0-flash',
            prompt: document.getElementById('prompt').value
        })
    });
    const data = await response.json();
    document.getElementById('output').innerText = JSON.stringify(data, null, 2);
}

async function getEmbedding() {
    const response = await fetch('/gemini/embed', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            model: 'gemini-embedding-1.0',
            text: document.getElementById('embedText').value
        })
    });
    const data = await response.json();
    document.getElementById('embeddingOutput').innerText = JSON.stringify(data, null, 2);
}
</script>
@endsection
