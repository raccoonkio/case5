@extends('layout')
@section('title', $register ? 'Создать аккаунт' : 'Вход в дневник')
@section('content')
<div class="auth-layout">
    <section class="auth-art"><img src="{{ asset('assets/karelia-report.jpg') }}" alt="База отдыха в Карелии — фотография из отчёта автора Ромчик в Т—Ж"><div><span class="eyebrow">ИСТОРИИ НАЧИНАЮТСЯ С ВАС</span><h1>Дороги меняются.<br><em>Воспоминания<br>остаются.</em></h1><p>Ваше место для маленьких открытий и больших путешествий.</p></div></section>
    <section class="auth-form"><span class="eyebrow muted">ВАШ ЛИЧНЫЙ ДНЕВНИК</span><h1>{{ $register ? 'Навстречу новому' : 'С возвращением' }}<span class="brand-dot">.</span></h1><p class="subheading">{{ $register ? 'Создайте аккаунт и начните собирать свои истории.' : 'Войдите, чтобы продолжить свою историю.' }}</p>
        @if($errors->any())<div class="error-box" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form method="post" action="{{ $register ? route('register.store') : route('login.store') }}" data-submit-form>
            @csrf
            @if($register)<label>Ваше имя<input name="name" value="{{ old('name') }}" required minlength="2" maxlength="80" autocomplete="name" placeholder="Как вас зовут?"></label>@endif
            <label>Электронная почта<input name="email" value="{{ old('email') }}" type="email" required maxlength="254" autocomplete="email" placeholder="you@example.com"></label>
            <label>Пароль<input name="password" type="password" required minlength="{{ $register ? 8 : 1 }}" maxlength="200" autocomplete="{{ $register ? 'new-password' : 'current-password' }}" placeholder="{{ $register ? 'Не менее 8 символов' : 'Ваш пароль' }}"></label>
            @if($register)<label>Повторите пароль<input name="password_confirmation" type="password" required minlength="8" maxlength="200" autocomplete="new-password" placeholder="Ещё раз, чтобы не ошибиться"></label>@endif
            <button class="button full-width" type="submit">{{ $register ? 'Создать аккаунт' : 'Войти в дневник' }} <span>↗</span></button>
        </form>
        <p class="auth-switch">{{ $register ? 'Уже с нами?' : 'Пока нет аккаунта?' }} <a href="{{ $register ? route('login') : route('register') }}">{{ $register ? 'Войти' : 'Зарегистрироваться' }}</a></p>
        @if(!$register && app()->isLocal())<div class="demo-note"><strong>Заглянуть в демо-дневник</strong><span>anna@example.com · Travel2026!</span></div>@endif
    </section>
</div>
@endsection
