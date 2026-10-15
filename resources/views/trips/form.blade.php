@extends('layout')
@section('title', 'Новое путешествие')
@section('content')
<div class="page-top"><a class="back-link" href="{{ route('diary') }}">← Назад к дневнику</a><span class="eyebrow muted">СОХРАНЯЕМ ВАЖНОЕ</span><h1>Каким было ваше путешествие?</h1><p>Большие впечатления складываются из маленьких деталей.</p></div>
<form class="editor-layout" method="post" enctype="multipart/form-data" action="{{ route('trips.store') }}" data-submit-form>
    @csrf
    <div class="editor-main">
        @if($errors->any())<div class="error-box" role="alert"><strong>Проверьте данные</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <section class="form-panel"><div class="panel-title"><span>01</span><h2>О путешествии</h2></div>
            <label>Название истории<input name="title" required minlength="3" maxlength="120" value="{{ old('title') }}" placeholder="Например, неделя среди гор Алтая"></label>
            <label>Куда вы ездили?<input name="location" required minlength="2" maxlength="120" value="{{ old('location') }}" placeholder="Город или регион, страна"></label>
            <label>Стоимость поездки, ₽<input name="budget" type="number" min="0" max="100000000" step="0.01" required value="{{ old('budget') }}" placeholder="Например, 42000"><small>Общий бюджет на поездку. Если расходов не было, укажите 0.</small></label>
        </section>
        <section class="form-panel"><div class="panel-title"><span>02</span><h2>Самое запоминающееся</h2></div><label>Ваша история<textarea name="body" rows="8" required minlength="10" maxlength="15000" placeholder="Какие места вас удивили? Что стоит знать тем, кто поедет следом?">{{ old('body') }}</textarea></label><label>Места для посещения<textarea name="places" rows="4" required maxlength="2000" placeholder="Одно место на каждой строке">{{ old('places') }}</textarea><small>Ваш личный список: достопримечательности, тропы, уютные кафе.</small></label></section>
        <div class="editor-bottom"><p>История будет доступна всем посетителям.</p><div class="form-actions"><a class="button button-light" href="{{ route('diary') }}">Отмена</a><button class="button" type="submit">Опубликовать историю ↗</button></div></div>
    </div>
    <aside class="editor-aside"><section class="form-panel"><div class="panel-title"><span>03</span><h2>Тот самый кадр</h2></div><label class="photo-upload"><img id="photo-preview" alt="Предпросмотр фотографии" hidden><span class="upload-prompt"><b>＋</b><strong>Добавьте фотографию</strong><span>JPG, PNG или WebP · до 4 МБ</span></span><input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" required></label><p class="photo-error" id="photo-error" role="alert"></p><p class="help-text">Выберите кадр, который передаёт атмосферу поездки. Он станет обложкой вашей истории.</p>@if($errors->any())<p class="help-text">После ошибки проверки выберите фотографию повторно.</p>@endif</section><div class="writing-note"><span>✳</span><h3>Пишите по-своему</h3><p>Не обязательно быть писателем. Расскажите так, как рассказали бы другу за чашкой кофе.</p></div></aside>
</form>
@endsection
