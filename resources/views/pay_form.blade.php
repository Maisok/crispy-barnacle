<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кошелёк</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="shortcut icon" href="{{asset('images/Group 438.png')}}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <style>
        .hidden {
            display: none;
        }
    </style>

<style>
    body {
    font-family: 'Nunito', sans-serif;
}
</style>
</head>
<body class="bg-white text-gray-800">
    @include('components.header-seller')
    <div class="container mx-auto p-4 mt-28">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="text-gray-600 md:block hidden">
        <a href="#" class="hover:underline">Профиль</a> / <span>Кошелёк</span>
    </div>
    <div class="space-y-2 text-right text-sm text-gray-600">
        <a href="#" class="block hover:underline">Как пополнить кошелёк</a>
        <a href="#" class="block hover:underline">Как происходят списания</a>
        <a href="#" class="block hover:underline">Возврат средств</a>
        <a href="#" class="block hover:underline">Часто задаваемые вопросы</a>
    </div>
</div>
        <div class="text-center">
            <h1 class="text-2xl font-bold mb-2">Кошелёк</h1>
            <p class="text-lg mb-4">Баланс <span class="font-bold">1,000 ₽</span></p>
            <div class="flex justify-center space-x-4 mb-6">
                <a href="#" id="top-up-link" class="text-black font-bold border-b-2 border-black">Пополнение кошелка</a>
                <a href="#" id="history-link" class="text-gray-600">История операций</a>
            </div>
            <div id="content-section" class="mb-14">
                <form action="{{ route('pay') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="amount" class="block text-gray-600 mb-2">Введите сумму</label>
                        <div class="inline-flex items-center border border-gray-300 rounded-md px-3 py-2">
                            <input type="number" name="amount" id="amount" required value="1500" class="w-20 text-center outline-none">
                            <span class="ml-2">₽</span>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4">Выберите способ пополнения</p>
                    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-3">
                        <!-- Первая кнопка -->
                        <button class="flex items-center justify-center p-4 bg-gray-100 rounded-md shadow-sm hover:bg-gray-200">
                            <i class="fas fa-credit-card text-2xl mr-2"></i>
                            <span>Банковская карта</span>
                        </button>
                    
                        <!-- Вторая кнопка -->
                        <div class="relative">
                            <button class="flex items-center justify-center p-4 bg-gray-100 rounded-md shadow-sm opacity-50 cursor-not-allowed" disabled>
                                <i class="fas fa-file-invoice text-2xl mr-2"></i>
                                <span>Сформировать счёт</span>
                            </button>
                            <div class="absolute inset-0 flex items-center justify-center bg-gray-50 bg-opacity-70 rounded-md">
                                <span class="text-gray-700 text-lg font-medium">Временно недоступно</span>
                            </div>
                        </div>
                    
                        <!-- Третья кнопка -->
                        <div class="relative">
                            <button class="flex items-center justify-center p-4 bg-gray-100 rounded-md shadow-sm opacity-50 cursor-not-allowed" disabled>
                                <i class="fas fa-university text-2xl mr-2"></i>
                                <span>СБП</span>
                            </button>
                            <div class="absolute inset-0 flex items-center justify-center bg-gray-50 bg-opacity-70 rounded-md">
                                <span class="text-gray-700 text-lg font-medium">Временно недоступно</span>
                            </div>
                        </div>
                    
                        <!-- Четвёртая кнопка -->
                        <div class="relative">
                            <button class="flex items-center justify-center p-4 bg-gray-100 rounded-md shadow-sm opacity-50 cursor-not-allowed" disabled>
                                <i class="fas fa-credit-card text-2xl mr-2"></i>
                                <span>T-Pay</span>
                            </button>
                            <div class="absolute inset-0 flex items-center justify-center bg-gray-50 bg-opacity-70 rounded-md">
                                <span class="text-gray-700 text-lg font-medium">Временно недоступно</span>
                            </div>
                        </div>
                    
                        <!-- Пятая кнопка -->
                        <div class="relative">
                            <button class="flex items-center justify-center p-4 bg-gray-100 rounded-md shadow-sm opacity-50 cursor-not-allowed" disabled>
                                <i class="fas fa-credit-card text-2xl mr-2"></i>
                                <span>Мир-Pay</span>
                            </button>
                            <div class="absolute inset-0 flex items-center justify-center bg-gray-50 bg-opacity-70 rounded-md">
                                <span class="text-gray-700 text-lg font-medium">Временно недоступно</span>
                            </div>
                        </div>
                    
                        <!-- Шестая кнопка -->
                        <div class="relative">
                            <button class="flex items-center justify-center p-4 bg-gray-100 rounded-md shadow-sm opacity-50 cursor-not-allowed" disabled>
                                <i class="fas fa-credit-card text-2xl mr-2"></i>
                                <span>Sber-Pay</span>
                            </button>
                            <div class="absolute inset-0 flex items-center justify-center bg-gray-50 bg-opacity-70 rounded-md">
                                <span class="text-gray-700 text-lg font-medium">Временно недоступно</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Секция с историей операций -->
            <div id="history-section" class="hidden mt-4 mb-14">
                <div class="container mx-auto p-4">
                    <div class="flex justify-center">
                        <div>
                            <div class="mt-4 flex space-x-4">
                                <button id="all-operations" class="text-blue-500 font-semibold">Все операции</button>
                                <button id="replenishment" class="text-gray-500">Пополнение</button>
                                <button id="withdrawal" class="text-gray-500">Списание</button>
                            </div>
                            <div id="history-items" class="mt-4 space-y-4">
                                <!-- Операции будут добавляться динамически -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('history-link').addEventListener('click', function(event) {
            event.preventDefault();
            var contentSection = document.getElementById('content-section');
            var historySection = document.getElementById('history-section');
            var topUpLink = document.getElementById('top-up-link');
            var historyLink = document.getElementById('history-link');

            if (historySection.classList.contains('hidden')) {
                contentSection.classList.add('hidden');
                historySection.classList.remove('hidden');
                topUpLink.classList.remove('text-black', 'font-bold', 'border-b-2', 'border-black');
                topUpLink.classList.add('text-gray-600');
                historyLink.classList.remove('text-gray-600');
                historyLink.classList.add('text-black', 'font-bold', 'border-b-2', 'border-black');
            } else {
                contentSection.classList.remove('hidden');
                historySection.classList.add('hidden');
                topUpLink.classList.add('text-black', 'font-bold', 'border-b-2', 'border-black');
                topUpLink.classList.remove('text-gray-600');
                historyLink.classList.add('text-gray-600');
                historyLink.classList.remove('text-black', 'font-bold', 'border-b-2', 'border-black');
            }
        });

        document.getElementById('top-up-link').addEventListener('click', function(event) {
            event.preventDefault();
            var contentSection = document.getElementById('content-section');
            var historySection = document.getElementById('history-section');
            var topUpLink = document.getElementById('top-up-link');
            var historyLink = document.getElementById('history-link');

            if (contentSection.classList.contains('hidden')) {
                contentSection.classList.remove('hidden');
                historySection.classList.add('hidden');
                topUpLink.classList.add('text-black', 'font-bold', 'border-b-2', 'border-black');
                topUpLink.classList.remove('text-gray-600');
                historyLink.classList.add('text-gray-600');
                historyLink.classList.remove('text-black', 'font-bold', 'border-b-2', 'border-black');
            } else {
                contentSection.classList.add('hidden');
                historySection.classList.remove('hidden');
                topUpLink.classList.remove('text-black', 'font-bold', 'border-b-2', 'border-black');
                topUpLink.classList.add('text-gray-600');
                historyLink.classList.remove('text-gray-600');
                historyLink.classList.add('text-black', 'font-bold', 'border-b-2', 'border-black');
            }
        });

        // Фильтрация операций
        const operations = [
            { type: 'replenishment', text: 'Пополнение кошелька', date: '02.12.2024', amount: '+ 1,500 ₽', color: 'text-green-500' },
            { type: 'withdrawal', text: 'Оплата тарифа', details: '1000 товаров × 0,75₽', date: '02.12.2024', amount: '- 750 ₽', color: 'text-red-500' },
            { type: 'withdrawal', text: 'Оплата тарифа', details: '1000 товаров × 0,75₽', date: '01.12.2024', amount: '- 750 ₽', color: 'text-red-500' }
        ];

        function renderOperations(filter) {
            const historyItems = document.getElementById('history-items');
            historyItems.innerHTML = '';

            operations.forEach(operation => {
                if (filter === 'all' || operation.type === filter) {
                    const item = document.createElement('div');
                    item.className = 'bg-gray-100 p-4 rounded-lg';
                    item.innerHTML = `
                        <p class="font-semibold">${operation.text}</p>
                        ${operation.details ? `<p class="text-gray-500 text-sm">${operation.details}</p>` : ''}
                        <p class="text-gray-500 text-sm">${operation.date}</p>
                        <p class="text-right font-semibold text-lg ${operation.color}">${operation.amount}</p>
                    `;
                    historyItems.appendChild(item);
                }
            });
        }

        // Функция для обновления стилей кнопок
        function updateButtonStyles(activeButton) {
            const buttons = document.querySelectorAll('#all-operations, #replenishment, #withdrawal');
            buttons.forEach(button => {
                if (button === activeButton) {
                    button.classList.add('text-blue-500', 'font-semibold');
                    button.classList.remove('text-gray-500'); // Удаляем text-gray-500
                } else {
                    button.classList.remove('text-blue-500', 'font-semibold');
                    button.classList.add('text-gray-500'); // Добавляем text-gray-500
                }
            });
        }

        // Обработчики событий для кнопок фильтрации
        document.getElementById('all-operations').addEventListener('click', () => {
            renderOperations('all');
            updateButtonStyles(document.getElementById('all-operations'));
        });

        document.getElementById('replenishment').addEventListener('click', () => {
            renderOperations('replenishment');
            updateButtonStyles(document.getElementById('replenishment'));
        });

        document.getElementById('withdrawal').addEventListener('click', () => {
            renderOperations('withdrawal');
            updateButtonStyles(document.getElementById('withdrawal'));
        });

        // Инициализация с отображением всех операций
        renderOperations('all');
        updateButtonStyles(document.getElementById('all-operations'));
    </script>
</body>
</html>