<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{asset('images/Group 438.png')}}" type="image/x-icon">
    <title>Платеж успешно завершен</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <!-- Подключение Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<style>
    body {
    font-family: 'Nunito', sans-serif;
}
</style>
<body class="bg-gray-100 flex flex-col min-h-screen">
    @include('components.header-seller')
    <div class="mt-20 mb-20 flex items-center justify-center w-full">
        <div class="bg-white p-8 rounded-lg shadow-md text-center max-w-md w-full ">
            <!-- Зеленая галочка в кружке -->
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 rounded-full bg-green-500 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
    
            <!-- Заголовок -->
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Платеж успешно завершен</h1>
    
            <!-- Информация о платеже -->
            <p class="text-gray-700 mb-2">Сумма пополнения: <span class="font-bold">{{ $amount }} руб.</span></p>
            <p class="text-gray-700 mb-6">Ваш баланс был успешно пополнен.</p>
    
            <!-- Кнопка для перехода на страницу профиля -->
            <a href="{{ route('user.show', auth()->user()->id,) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Перейти в профиль
            </a>
        </div>
    </div>
   
</body>
</html>