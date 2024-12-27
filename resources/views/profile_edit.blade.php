<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование профиля</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{asset('images/Group 438.png')}}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
</head>
<style>
    body {
    font-family: 'Nunito', sans-serif;
}
</style>
<body >
    @include('components.header-seller')   

    <div class="container mx-auto mt-20 p-4">
        <div class="flex flex-col md:flex-row justify-between">
            <div class="w-full md:w-2/3">
                <h2 class="text-sm text-gray-500 mb-4">Профиль / Редактировать профиль</h2>
                <div class="mb-8">
                    <h3 class="text-xl font-bold mb-4">О компании</h3>
                    <form action="{{ route('profile.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- Поля для "О компании" -->
                        <div class="mb-4">
                            <label class="block text-gray-700">Название магазина</label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full border border-gray-300 p-2 rounded" placeholder="Под этим названием покупатели будут видеть ваши товары">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">E-Mail</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border border-gray-300 p-2 rounded">
                        </div>
                        <div class="mb-4 flex items-center">
                            <div class="w-full">
                                <label class="block text-gray-700">Телефон</label>
                                <input type="tel" name="phone" id="phoneInput" value="{{ old('phone', $user->phone) }}" class="w-full border border-gray-300 p-2 rounded" placeholder="8 888 888 88 88" oninput="formatPhoneNumber(this)">
                            </div>
                            <div class="flex items-center ml-2 text-blue-500 cursor-pointer">
                                <i class="fas fa-plus-circle mr-2"></i>
                                <span>Добавить дополнительный телефон</span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Адрес</label>
                            <input type="text" name="address" value="{{ old('address', $user->address) }}" class="w-full border border-gray-300 p-2 rounded">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">URL изображения профиля</label>
                            <input type="url" name="avatar_url" value="{{ old('avatar_url', $user->avatar_url) }}" class="w-full border border-gray-300 p-2 rounded">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">URL логотипа</label>
                            <input type="url" name="logo_url" value="{{ old('logo_url', $user->logo_url) }}" class="w-full border border-gray-300 p-2 rounded">
                        </div>

                        <!-- Поля для "Юридическая информация" -->
                        <h3 class="text-xl font-bold mt-8 mb-4">Юридическая информация</h3>
                        <div class="mb-4">
                            <label class="block text-gray-700">Название организации</label>
                            <input type="text" name="organization_name" value="{{ old('organization_name', $user->organization_name) }}" class="w-full border border-gray-300 p-2 rounded">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Юридический адрес</label>
                            <input type="text" name="legal_address" value="{{ old('legal_address', $user->legal_address) }}" class="w-full border border-gray-300 p-2 rounded">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">ИНН</label>
                            <input type="text" name="inn" value="{{ old('inn', $user->inn) }}" class="w-full border border-gray-300 p-2 rounded">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">КПП</label>
                            <input type="text" name="kpp" value="{{ old('kpp', $user->kpp) }}" class="w-full border border-gray-300 p-2 rounded">
                        </div>

                        <!-- Кнопка отправки формы -->
                        <div class="mt-8">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="w-full md:w-1/3 mt-8 md:mt-0 md:ml-8">
                <div class="bg-gray-100 p-4 rounded mb-8" style="padding: 1.5rem;">
                    <button id="changePasswordButton" class="flex items-center text-gray-700">
                        <i class="fas fa-lock mr-2"></i>
                        <span>Сменить пароль</span>
                    </button>
                </div>
                <div class="text-gray-500">
                    <p class="mb-2">Как выбрать название магазина</p>
                    <p>Как добавить дополнительный телефон</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для смены пароля -->
    <div id="passwordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Сменить пароль</h3>
                <form id="passwordForm" action="{{ route('profile.update', $user->id) }}" method="POST" class="mt-2">
                    @csrf
                     @method('PUT')
                    <div class="mb-4">
                        <label class="block text-gray-700">Новый пароль</label>
                        <input type="password" id="newPassword" class="w-full border border-gray-300 p-2 rounded">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Подтвердите пароль</label>
                        <input type="password" id="confirmPassword" class="w-full border border-gray-300 p-2 rounded">
                    </div>
                    <div class="mt-4">
                        <button type="submit" id="submitPassword" class="bg-blue-500 text-white px-4 py-2 rounded">Сохранить</button>
                        <button type="button" id="closeModal" class="ml-2 bg-gray-300 text-gray-700 px-4 py-2 rounded">Отмена</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Функция для форматирования номера телефона
        function formatPhoneNumber(input) {
            let phoneNumber = input.value.replace(/\D/g, ''); // Убираем все нецифровые символы
            phoneNumber = phoneNumber.substring(0, 11); // Ограничиваем длину до 11 цифр

            if (phoneNumber.length > 0) {
                phoneNumber = '8 ' + phoneNumber.substring(1).replace(/(\d{3})(\d{3})(\d{2})(\d{2})/, '$1 $2 $3 $4');
            }

            input.value = phoneNumber;
        }

        document.getElementById('changePasswordButton').addEventListener('click', function() {
            document.getElementById('passwordModal').classList.remove('hidden');
        });

        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('passwordModal').classList.add('hidden');
        });

        document.getElementById('submitPassword').addEventListener('click', function() {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword === confirmPassword) {
                // Здесь можно добавить логику для отправки нового пароля на сервер
                alert('Пароль успешно изменен');
                document.getElementById('passwordModal').classList.add('hidden');
            } else {
                alert('Пароли не совпадают');
            }
        });
    </script>
</body>
</html>