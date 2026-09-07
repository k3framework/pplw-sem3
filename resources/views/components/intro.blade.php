@props(['title', 'description' => ''])
<div class="intro">
    <h1>{{ $title }}</h1>
    @if($description)<p class="muted">{{ $description }}</p>@endif
</div>
@include('partials.feedback')
