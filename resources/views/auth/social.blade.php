<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Ciudad Fácil') }}</title>

    <!-- Styles
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Scripts
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script> -->
</head>
<body>
    <center>
        <img style="width: 20%; heigth:20%;" src="{{ asset('img/ciudad_facil.png') }}" alt="Ciudad Fácil">
    </center>
    <pre hidden>
        {{ $respuesta }}
    </pre>
</body>
