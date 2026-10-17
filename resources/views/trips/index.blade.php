@extends('layout')
@section('title', $mine ? 'Мой дневник' : 'Истории, которые вдохновляют')
@section('content')
<section class="hero {{ $mine ? 'hero-personal' : '' }}">
    <img class="hero-photo" src="{{ asset('assets/crimea-report.jpg') }}" alt="Вид на море с мыса Фиолент в Крыму" fetchpriority="high">
    <div class="hero-content">
        <span class="eyebrow"><span class="tiny-line"></span> ДНЕВНИК ПУТЕШЕСТВИЙ</span>
        <h1>@if($mine)Ваши маршруты.<br>Ваши <em>истории.</em>@elseМир большой.<br>Сохраните <em>своё.</em>@endif</h1>
        <p>{{ $mine ? 'Места, люди и моменты, к которым хочется возвращаться. Соберите их в своём дневнике.' : 'Записывайте впечатления, делитесь открытиями и находите вдохновение для следующего путешествия.' }}</p>
        <a class="button button-cream" href="{{ route('trips.create') }}">{{ $mine ? 'Добавить путешествие' : 'Начать свою историю' }} <span aria-hidden="true">↗</span></a>
    </div>
    <div class="hero-caption"><span>↟</span><div>Где-то за пределами привычного<small>Крым · фото из отчёта Александра Кота</small></div></div>
    <span class="hero-stamp" aria-hidden="true">БОЛЬШЕ ДОРОГ<br><b>✳</b><br>БОЛЬШЕ ИСТОРИЙ</span>
</section>
<section class="stories" id="stories">
    <div class="section-heading"><div><span class="eyebrow muted">{{ $mine ? 'ЛИЧНАЯ КОЛЛЕКЦИЯ' : 'ВДОХНОВЕНИЕ РЯДОМ' }}</span><h2>{{ $mine ? 'Мои путешествия' : 'Истории с разных дорог' }}</h2></div><p>{{ $mine ? 'Все ваши открытия в одном месте' : 'Реальные отчёты. Проверяемые источники.' }}</p></div>
    <div class="trip-grid">
        @forelse($trips as $trip)
            <article class="trip-card">
                <a class="card-image" href="{{ route('trips.show', $trip) }}" tabindex="-1" aria-hidden="true"><img src="{{ $trip->photoUrl() }}" alt="{{ $trip->location }}" loading="lazy"><span class="card-arrow">↗</span></a>
                <div class="card-content"><div class="card-location"><span aria-hidden="true">⌖</span> {{ $trip->location }}</div>
                    <h3><a href="{{ route('trips.show', $trip) }}">{{ $trip->title }}</a></h3><p>{{ Illuminate\Support\Str::limit($trip->body, 110) }}</p>
                    <div class="card-meta"><span>{{ number_format((float) $trip->budget, 0, ',', ' ') }} ₽ <small>{{ $trip->source_url ? 'по отчёту' : 'за поездку' }}</small></span></div>
                    <div class="card-author"><span class="avatar tone-{{ $trip->user_id % 3 }}">{{ mb_substr($trip->source_author ?? $trip->user->name, 0, 1) }}</span><span>{{ $trip->source_author ?? $trip->user->name }}</span>@if($trip->source_url)<a class="author-label source-link" href="{{ $trip->source_url }}" target="_blank" rel="noopener noreferrer">{{ $trip->sourceName() }} · источник ↗</a>@elseif(auth()->id() === $trip->user_id)<span class="your-story">Ваша история</span>@else<span class="author-label">Путешественник</span>@endif</div>
                </div>
            </article>
        @empty
            <div class="empty-state"><span>⌁</span><h3>{{ $mine ? 'Ваша первая история впереди' : 'Здесь пока нет историй' }}</h3><p>{{ $mine ? 'Сохраните воспоминания о поездке — с фотографией, бюджетом и любимыми местами.' : 'Добавьте первое путешествие и поделитесь своими впечатлениями.' }}</p><a class="button" href="{{ $mine ? route('trips.create') : route('trips.index') }}">{{ $mine ? 'Записать путешествие' : 'Все путешествия' }} ↗</a></div>
        @endforelse
    </div>
    @if($trips->hasPages())<nav class="pagination" aria-label="Страницы путешествий">@if(!$trips->onFirstPage())<a class="button button-light" href="{{ $trips->previousPageUrl() }}#stories">← Назад</a>@endif<span>{{ $trips->currentPage() }} / {{ $trips->lastPage() }}</span>@if($trips->hasMorePages())<a class="button button-light" href="{{ $trips->nextPageUrl() }}#stories">Дальше →</a>@endif</nav>@endif
</section>
<aside class="closing-note"><span aria-hidden="true">✳</span><div><h2>Самые ценные сувениры — истории.</h2><p>Пусть ни одно путешествие не останется только в памяти.</p></div><a class="button button-outline" href="{{ route('trips.create') }}">Поделиться своей ↗</a></aside>
@endsection
