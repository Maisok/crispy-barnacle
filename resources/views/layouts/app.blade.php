<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ГдеЗапчасть.рф</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cookie-notice.css') }}"> <!-- Подключение стилей уведомления -->
    <link rel="shortcut icon" href="{{asset('images/Group 438.png')}}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

</head>
<style>
    body {
    font-family: 'Nunito', sans-serif;
}
</style>
<body class="bg-white">


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
{{-- 
    <div class="container">
        @yield('content')
    </div> 
    я хз че это 
    --}}

    <div id="cookieNotice">
        Мы используем файлы cookie. Оставаясь с нами, вы соглашаетесь на использование <a href="{{ route('cookie.policy') }}">файлов cookie</a><br>
        <button onclick="acceptCookieNotice()">Понятно</button>
    </div>

    <script src="{{ asset('js/cookie-notice.js') }}"></script> <!-- Подключение скрипта уведомления -->
    @include('components.footer')

</body>
</html>