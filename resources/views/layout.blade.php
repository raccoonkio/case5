<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Дневник путешествий: сохраняйте фотографии, маршруты и впечатления, делитесь открытиями и планируйте новые поездки.">
    <title>@yield('title', 'Дневник путешествий') · Попутно</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
    <script src="{{ asset('app.js') }}" defer></script>
</head>
<body>
<a class="skip-link" href="#main">Перейти к содержимому</a>
<header class="header">
    <div class="header-inner">
        <a class="brand" href="{{ route('trips.index') }}" aria-label="Попутно — главная"><span class="brand-symbol">✳</span> попутно<span class="brand-dot">.</span></a>
        <nav aria-label="Основная навигация">
            <a @class(['nav-link', 'active' => request()->routeIs('trips.index', 'trips.show')]) href="{{ route('trips.index') }}">Все путешествия</a>
            <a @class(['nav-link', 'active' => request()->routeIs('diary')]) href="{{ route('diary') }}">Мой дневник</a>
        </nav>
        <div class="header-actions">
            @auth
                <span class="profile"><span class="avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</span><span>{{ explode(' ', auth()->user()->name)[0] }}</span></span>
                <form action="{{ route('logout') }}" method="post">@csrf<button class="text-button" type="submit">Выйти</button></form>
            @else
                <a class="login-link" href="{{ route('login') }}">Войти</a>
            @endauth
            <a class="button button-small" href="{{ route('trips.create') }}"><span aria-hidden="true">＋</span><span>Записать путешествие</span></a>
        </div>
    </div>
</header>
<main id="main" class="container">
    @if(session('success'))<div class="notice" role="status"><span>✓ {{ session('success') }}</span><button type="button" data-dismiss aria-label="Закрыть уведомление">×</button></div>@endif
    @yield('content')
</main>
<footer class="footer container"><a class="brand footer-brand" href="{{ route('trips.index') }}">✳ попутно.</a><span>Путешествия заканчиваются. Истории остаются.</span><small>Дневник путешествий · 2026</small></footer>
</body>
</html>
