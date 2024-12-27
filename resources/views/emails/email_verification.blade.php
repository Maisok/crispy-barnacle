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
    <h2>Добро пожаловать, {{ $user->username }}!</h2>
    <p>Пожалуйста, подтвердите ваш адрес электронной почты, перейдя по ссылке ниже:</p>
    <a href="{{ route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)]) }}">Подтвердить почту</a>
    <p>Если вы не регистрировались на нашем сайте, проигнорируйте это письмо.</p>
</body>
</html>