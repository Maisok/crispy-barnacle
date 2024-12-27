<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Подтверждение почты</title>
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
<body>
    <h2>Подтверждение почты</h2>
    <p>Пожалуйста, подтвердите ваш адрес электронной почты, перейдя по ссылке, отправленной на вашу почту.</p>
    <form action="{{ route('verification.send') }}" method="POST">
        @csrf
        <button type="submit">Отправить ссылку повторно</button>
    </form>
</body>
</html>