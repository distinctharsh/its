@extends('layouts.auth-layout')

@section('content')
<form action="{{ route('userRegister') }}" method="post">
    @csrf

    @if(\Session::has('error'))
        <p style="color: red;">{{ \Session::get('error') }}</p>
    @elseif(\Session::has('success'))
        <p style="color: green">{{ \Session::get('success') }}</p>
    @endif

    <h1>Register</h1>
    <fieldset>

        <label for="name">Name:</label>
        <input type="text" name="name" required>

        <label for="mail">Email:</label>
        <input type="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" required>

        <label for="passport_number">Passport Number:</label>
        <input type="text" name="passport_number" required>

        <label for="inspector_name">Inspector Name:</label>
        <input type="text" name="inspector_name" required>

    </fieldset>
    <button type="submit">Submit</button>
</form>
@endsection
