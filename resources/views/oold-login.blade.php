@extends('layouts.auth-layout')

@section('content')
<form action="{{ route('userLogin') }}" method="post">
    @csrf

    @if(\Session::has('error'))
    <p style="color: red;">{{ \Session::get('error') }}</p>
    @endif

    <h1>Inspector Tracking Software</h1>
    <fieldset class="mb-0">

        <label for="mail">Email:</label>
        <input type="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <div class="form-group">
            <label for="captcha">Enter Captcha</label>
            <div style="position: relative;">
                <img id="captchaImage" src="{{ route('captcha') }}" alt="captcha" class="captcha-image">
                <i class="fa-solid fa-arrows-rotate" style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="refreshCaptcha()"></i>
            </div>
            <input type="text" name="captcha" class="form-control" minlength="6" maxlength="6" required>
        </div>

    </fieldset>
    <button type="submit">Login</button>
</form>
@endsection

@push('style')


@endpush