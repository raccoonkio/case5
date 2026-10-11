<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SessionController extends Controller
{
    public function create(): View
    {
        return view('auth', ['register' => false]);
    }

    public function register(): View
    {
        return view('auth', ['register' => true]);
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate(['email' => ['required', 'email', 'max:254'], 'password' => ['required', 'string', 'max:200']], ['required' => 'Заполните поле «:attribute».', 'email.email' => 'Введите корректный адрес почты.', 'max' => 'Поле «:attribute» слишком длинное.'], ['email' => 'Электронная почта', 'password' => 'Пароль']);
        $credentials['email'] = mb_strtolower($credentials['email']);
        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages(['email' => 'Неверная почта или пароль.']);
        }
        $request->session()->regenerate();

        return redirect()->intended(route('diary'))->with('success', 'С возвращением! Впереди новые истории.');
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $request->merge(['email' => mb_strtolower(trim((string) $request->input('email', '')))]);
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:80'],
            'email' => ['required', 'email', 'max:254', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:200', 'confirmed'],
        ], ['required' => 'Заполните поле «:attribute».', 'name.min' => 'Имя должно содержать не менее 2 символов.', 'max' => 'Поле «:attribute» слишком длинное.', 'email.email' => 'Введите корректный адрес почты.', 'email.unique' => 'Эта почта уже зарегистрирована.', 'password.min' => 'Пароль должен содержать не менее 8 символов.', 'password.confirmed' => 'Пароли не совпадают.'], ['name' => 'Ваше имя', 'email' => 'Электронная почта', 'password' => 'Пароль']);
        Auth::login(User::create($validated));
        $request->session()->regenerate();

        return redirect()->route('diary')->with('success', 'Аккаунт создан. Запишите своё первое путешествие!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('trips.index')->with('success', 'Вы вышли из аккаунта.');
    }
}
