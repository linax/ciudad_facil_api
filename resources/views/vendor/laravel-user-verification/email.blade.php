<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Ciudad Fácil') }}</title>


    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body>
    </br>
    <center>
        <img style="width: 20%; heigth:20%;" src="{{ asset('img/ciudad_facil.png') }}" alt="Ciudad Fácil">
    </center>
    <hr>
    <center>
        <pr>
            <a href="{{ $link = route('email-verification.check', $user->verification_token) . '?email=' . urlencode($user->email) }}">Verificar correo electrónico</a>
        </pr>
    </center>
</body>