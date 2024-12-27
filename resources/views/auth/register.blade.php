<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://api-maps.yandex.ru/2.1/?apikey=bef13086-2418-4e93-bac9-45e709948f50&lang=ru_RU&suggest_apikey=bef13086-2418-4e93-bac9-45e709948f50" type="text/javascript"></script>
    <script src="{{ asset('js/register.js') }}" type="text/javascript"></script> <!-- Подключение JavaScript файла -->
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

    <div class="header text-center mt-10">
        <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Есть аккаунт? Войти</a>
    </div>

    <div class="container mx-auto mt-8 p-4 bg-white rounded shadow-md max-w-md">
        <h2 class="text-2xl font-bold text-center mb-4">Регистрация</h2>

        <form id="registerForm" method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Выбор роли: клиент или продавец -->
            <div class="flex space-x-4">
                <label class="flex items-center">
                    <input type="radio" name="user_status" value="0" {{ old('user_status') == 0 ? 'checked' : '' }} class="mr-2"> Клиент
                </label>
                <label class="flex items-center">
                    <input type="radio" name="user_status" value="1" {{ old('user_status') == 1 ? 'checked' : '' }} class="mr-2"> Продавец
                </label>
            </div>

            <!-- Поле для имени пользователя -->
            <input type="text" name="username" id="usernameInput" placeholder="Название компании" value="{{ old('username') }}" required class="w-full p-3 border rounded-md">

            <!-- Поле для email -->
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required class="w-full p-3 border rounded-md">

            <div class="password-container relative">
                <!-- Поле для пароля -->
                <input type="password" name="password" id="passwordInput" placeholder="Пароль" required class="w-full p-3 border rounded-md">
                <span class="toggle-password absolute right-2 top-1/2 transform -translate-y-1/2 cursor-pointer" onclick="togglePasswordVisibility('passwordInput', 'confirmPasswordInput', this)">
                    <img src="images/close_password.png" alt="Показать" class="password-icon w-5 h-5">
                </span>
            </div>

            <!-- Поле для подтверждения пароля -->
            <input type="password" name="password_confirmation" id="confirmPasswordInput" placeholder="Повторите пароль" required class="w-full p-3 border rounded-md">

            <!-- Поле для телефона -->
            <input type="text" id="phoneInput" name="phone" placeholder="Телефон (8 888 888 88 88)" value="{{ old('phone') }}" required maxlength="15" class="w-full p-3 border rounded-md" oninput="formatPhoneNumber(this)">

            <!-- Поле для города с автозаполнением -->
            <input type="text" id="cityInput" name="city" placeholder="Введите город" value="{{ old('city') }}" required autocomplete="off" class="w-full p-3 border rounded-md">
            <div id="citySuggestions" class="suggest-view max-h-52 overflow-y-auto bg-white border rounded-md hidden"></div>

            <!-- Поле для адреса с автозаполнением -->
            <div id="addressFieldContainer">
                <input type="text" id="addressInput" name="address_line" placeholder="Введите адрес" value="{{ old('address_line') }}" class="w-full p-3 border rounded-md">
            </div>

            <!-- Чекбокс для согласия с офертой -->
            <div class="flex items-center">
                <input type="checkbox" id="agree" name="agree" required class="mr-2" {{ old('agree') ? 'checked' : '' }}>
                <label for="agree" class="text-sm">
                    Я прочитал и согласен с <a href="{{ url('/oferta') }}" target="_blank" class="text-blue-500 hover:underline">офертой</a>
                </label>
            </div>
            <p id="agreementError" class="text-red-500 hidden">Подтвердите, что ознакомлены и согласны с офертой.</p>

            <!-- Обработка ошибок валидации -->
            @if ($errors->any())
                <div class="error text-red-500">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <button type="submit" class="w-full p-3 bg-blue-500 text-white rounded-md hover:bg-blue-600">Зарегистрироваться</button>
        </form>

        <p class="mb-16 mt-4 text-center">После регистрации вам будет отправлено письмо для подтверждения почты.</p>
    </div>

    <script>
        // Функция для изменения placeholder в зависимости от выбранной роли
        function updatePlaceholder() {
            const usernameInput = document.getElementById('usernameInput');
            const clientRadio = document.querySelector('input[name="user_status"][value="0"]');
            const sellerRadio = document.querySelector('input[name="user_status"][value="1"]');

            if (clientRadio.checked) {
                usernameInput.placeholder = 'Ваше имя';
            } else if (sellerRadio.checked) {
                usernameInput.placeholder = 'Название компании';
            }
        }

        // Функция для отображения/скрытия поля адреса
        function toggleAddressField() {
            const addressFieldContainer = document.getElementById('addressFieldContainer');
            const addressInput = document.getElementById('addressInput');
            const clientRadio = document.querySelector('input[name="user_status"][value="0"]');

            if (clientRadio.checked) {
                addressFieldContainer.style.visibility = 'hidden'; // Скрываем поле адреса
                addressFieldContainer.style.height = '0'; // Убираем высоту контейнера
                addressInput.removeAttribute('required'); // Убираем атрибут required
            } else {
                addressFieldContainer.style.visibility = 'visible'; // Показываем поле адреса
                addressFieldContainer.style.height = 'auto'; // Восстанавливаем высоту контейнера
                addressInput.setAttribute('required', 'required'); // Добавляем атрибут required
            }
        }

        // Функция для форматирования номера телефона
        function formatPhoneNumber(input) {
            let phoneNumber = input.value.replace(/\D/g, ''); // Убираем все нецифровые символы
            phoneNumber = phoneNumber.substring(0, 11); // Ограничиваем длину до 11 цифр

            if (phoneNumber.length > 0) {
                phoneNumber = '8 ' + phoneNumber.substring(1).replace(/(\d{3})(\d{3})(\d{2})(\d{2})/, '$1 $2 $3 $4');
            }

            input.value = phoneNumber;
        }

        // Добавляем обработчик события change на радиокнопки
        document.querySelectorAll('input[name="user_status"]').forEach(radio => {
            radio.addEventListener('change', () => {
                updatePlaceholder();
                toggleAddressField();
            });
        });

        // Инициализация при загрузке страницы
        updatePlaceholder();
        toggleAddressField();
    </script>
</body>
</html>