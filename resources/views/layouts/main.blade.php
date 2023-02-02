<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Google map restaurants search</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('assets/bootstrap/css/bootstrap.min.css')}}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@100;300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('assets/bootstrap/css/bootstrap.min.css')}}">

    <style>
        body {
            font-family: "Prompt", sans-serif;
            height: 100%;
            background-color: #f4f5fa;
        }
    </style>
</head>

<body style="">
    @yield('content')
</body>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
{{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
<script src="{{ asset('js/app.js?v='.date('Ymdhis'))}}') }}" defer></script>
</html>