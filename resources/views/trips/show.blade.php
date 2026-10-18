@extends('layout')
@section('title', $trip->title)
@section('content')
<article class="story-page"><div class="story-topline"><a class="back-link" href="{{ route('trips.index') }}">← Все путешествия</a></div>
    <header class="story-heading"><span class="eyebrow muted">{{ $trip->location }}</span><h1>{{ $trip->title }}</h1><div class="story-byline"><span class="avatar">{{ mb_substr($trip->source_author ?? $trip->user->name, 0, 1) }}</span><strong>{{ $trip->source_author ?? $trip->user->name }}</strong></div></header>
    @if($trip->source_url)
        <aside class="report-source"><div><span class="eyebrow muted">ПО МАТЕРИАЛАМ РЕАЛЬНОГО ПУТЕШЕСТВИЯ</span><p>Краткий пересказ отчёта · оригинал: {{ $trip->source_author }} · {{ $trip->sourceName() }}, {{ $trip->source_published_at?->format('d.m.Y') }}</p><small>Адаптация от лица путешественницы, не прямая речь автора оригинала. Маршрут и расходы — по источнику.</small></div><a class="button button-outline" href="{{ $trip->source_url }}" target="_blank" rel="noopener noreferrer">Читать оригинал ↗</a></aside>
    @endif
    <img class="story-cover" src="{{ $trip->photoUrl() }}" alt="{{ $trip->location }} — {{ $trip->title }}">
    @if($trip->photo_credit)<p class="photo-credit">{{ $trip->photo_credit }} · <a href="{{ $trip->photo_source_url ?? $trip->source_url }}" target="_blank" rel="noopener noreferrer">Источник фотографии ↗</a></p>@endif
    <div class="story-layout"><div class="story-body"><span class="eyebrow muted">ЗАМЕТКИ ИЗ ПУТЕШЕСТВИЯ</span><h2>История этой поездки</h2><div class="prose">{{ $trip->body }}</div><section class="places"><span class="eyebrow muted">СОХРАНИТЕ В МАРШРУТ</span><h2>Места, которые стоит увидеть</h2><ol>@foreach($trip->places as $place)<li><span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $place }}</li>@endforeach</ol></section></div>
        <aside class="trip-summary"><span class="eyebrow muted">ПОЕЗДКА В ДЕТАЛЯХ</span><h3>Немного о маршруте</h3><dl><div><dt>⌖ Направление</dt><dd>{{ $trip->location }}</dd></div><div><dt>₽ Стоимость поездки</dt><dd class="budget">{{ number_format((float) $trip->budget, 2, ',', ' ') }} ₽</dd>@if($trip->budget_note)<dd class="budget-note">{{ $trip->budget_note }}</dd>@endif</div></dl><a class="button full-width" href="{{ route('trips.create') }}">Записать свою историю ↗</a></aside>
    </div>
</article>
@endsection
