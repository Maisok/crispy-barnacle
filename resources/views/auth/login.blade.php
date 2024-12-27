<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
<body class=" bg-gray-100">
    @include('components.header-seller')    

    <div class="header text-center mt-28"> 
        <a href="{{ route('register') }}" class="text-blue-500 hover:underline">Нет аккаунта? Зарегистрируйся</a>
    </div>

    <div class="container mx-auto mt-8 p-4 bg-white rounded shadow-md max-w-md">
        <h2 class="text-2xl font-bold text-center mb-4">Авторизация</h2>

        @if ($errors->any())
            <div class="text-red-500 mb-4">
                <strong>{{ $errors->first() }}</strong>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <input type="email" name="email" placeholder="Email" required class="w-full p-3 border rounded-md">
            
            <!-- Поле для пароля с иконкой для скрытия/показа -->
            <div class="relative">
                <input type="password" name="password" id="passwordInput" placeholder="Пароль" required class="w-full p-3 border rounded-md pr-10">
                <span class="toggle-password absolute right-2 top-1/2 transform -translate-y-1/2 cursor-pointer" onclick="togglePasswordVisibility()">
                    <img src="images/close_password.png" alt="Показать/Скрыть пароль" class="w-5 h-5">
                </span>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="mr-2">
                <label for="remember" class="text-sm">Запомнить меня</label>
            </div>
            <button type="submit" class="w-full p-3 bg-blue-500 text-white rounded-md hover:bg-blue-600">Войти</button>
        </form>
    </div>

    <!-- Добавляем отступ снизу для мобильных устройств -->
    <div class="mt-20"></div>

    <script>
        // Функция для переключения видимости пароля
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('passwordInput');
            const toggleIcon = document.querySelector('.toggle-password img');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text'; // Показываем пароль
                toggleIcon.src = "images/close_password.png"; // Меняем иконку на "скрыть"
            } else {
                passwordInput.type = 'password'; // Скрываем пароль
                toggleIcon.src = "images/open_password.png"; // Меняем иконку на "показать"
            }
        }
    </script>
</body>
</html>