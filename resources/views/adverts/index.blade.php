<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все товары</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="shortcut icon" href="{{asset('images/Group 438.png')}}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
</head>
<style>
#modifications {
    display: flex;
    flex-direction: column; /* Текст будет выводиться в один элемент в строку */
    overflow-y: auto; 
    height: 10rem;/* Вертикальная прокрутка, если текст не помещается */
}
.blockadvert{
    border: 0.2px solid #ccc;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 15px;
}

body {
    font-family: 'Nunito', sans-serif;
}
</style>
<body class=" flex flex-col items-center">

@include('components.header-seller')   
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js"></script> 

<!-- Рекламный баннер -->
<div class="w-full md:w-[90%] mx-auto mt-10 hidden md:block">
    <img src="{{ asset('images/banner.png') }}" alt="Реклама" class="banner w-full rounded-2xl hidden md:block">
</div>

<div class="w-full md:w-[90%] flex flex-col items-start">
    <div class="w-full md:w-[85%]">
        <h2 class="text-2xl font-bold mt-8 mb-4 text-center md:text-left">Поиск запчастей:</h2>
        @include('components.search-form')  
    </div>
</div>

<div id="search-filters-wrapper"  class="w-full md:w-[90%] mt-4 flex flex-col items-start">
    <div id="search-filters-container" class="p-4 hidden w-full md:w-[75%]"></div>
</div>

<div class="w-full md:w-[90%]">
    <div class="flex flex-col w-full sm:flex-row sm:justify-start sm:w-full mt-8">
    @if($adverts->isEmpty())
        <p class="text-center text-lg mt-8">Нет доступных объявлений.</p>
    @else
        @php
            // Фильтруем коллекцию, исключая товар с id 1111
            $filteredAdverts = $adverts->reject(function($advert) {
                return $advert->id == 1111;
            });
        @endphp

        <!-- Для телефонов -->
        <div class="grid grid-cols-2 sm:grid-cols-2 gap-4 sm:hidden">
            @foreach($filteredAdverts as $advert)
            <div class="bg-white rounded-lg shadow p-4" onclick="location.href='{{ route('adverts.show', $advert->id) }}'">
                <div class="relative">
                    @if ($advert->main_photo_url)
                        <img src="{{ $advert->main_photo_url }}" alt="{{ $advert->product_name }} - Главное фото" class="w-full h-48 object-cover rounded-lg">
                    @else
                        <img src="{{ asset('images/dontfoto.jpg') }}" alt="Фото отсутствует" class="w-full h-48 object-cover rounded-lg">
                    @endif
                    <span class="absolute top-2 right-2 bg-[#FFE6C1] text-black text-xs font-normal px-2 py-1 rounded">
                        В наличии
                    </span>
                </div>
                <div class="mt-4">
                    <div class="text-lg font-bold">
                        {{ $advert->product_name }}
                    </div>
                    <div class="text-xl text-black font-semibold">
                        {{ $advert->price }} ₽
                    </div>
                    <div class="flex flex-wrap text-gray-500 text-sm mt-2">
                        <i class="fas fa-car mr-2"></i>
                        <span>{{ $advert->brand }}</span>
                        <span class="mx-1">|</span>
                        <span>{{ $advert->model }}</span>
                        <span class="mx-1">|</span>
                        <span>{{ $advert->year }}</span>
                    </div>
                    <div class="text-red-500 font-semibold mt-2">
                        {{ $advert->user->userAddress->city ?? 'Не указан' }}
                    </div>
                    <div class="text-gray-500 text-sm">
                        сегодня в 12:00
                    </div>
                </div>
            </div>
            @endforeach
        </div>

       <!-- Для больших и средних экранов -->
       <div class="hidden sm:flex w-full flex-col items-start justify-center mr-20">
        @foreach($filteredAdverts as $advert)
        <div class="blockadvert bg-white rounded-lg shadow-md flex max-w-5xl w-full mt-8 cursor-pointer transition-colors duration-300 hover:bg-[#f0f8ff]" onclick="location.href='{{ route('adverts.show', $advert->id) }}'" tabindex="0" role="button">
            <!-- Вывод главного фото -->
            <div class="w-1/4 flex-shrink-0">
                <div class="w-[220px] h-[175px] bg-gray-200 rounded-lg overflow-hidden">
                    @if ($advert->main_photo_url)
                        <img src="{{ $advert->main_photo_url }}" alt="{{ $advert->product_name }} - Главное фото" class="w-full h-full object-cover">
                    @else
                        <img src="{{ asset('images/dontfoto.jpg') }}" alt="Фото отсутствует" class="w-full h-full object-cover">
                    @endif
                </div>
            </div>
        
            <div class="flex flex-col justify-between w-3/4 lg:ml-10 sm:ml-20">
                <div class="flex justify-between items-start">
                    <div class="pt-4">
                        <h2 class="text-xl font-semibold">{{ $advert->product_name }}</h2>
                        @if($advert->number)
                        <p class="beg bg-gray-200 mt-4 px-3 py-1 w-24 text-sm rounded-lg text-center">{{ $advert->number }}</p>
                    @endif
                    </div>
                    <div class="text-right pr-4 pt-4">
                        <p class="text-xl font-semibold">{{ $advert->price }} ₽</p>
                        <p class="text-red-500">{{ $advert->user->userAddress->city ?? 'Не указан' }}</p>
                    </div>
                </div>
                <div class="flex space-x-3 pb-4 w-full justify-start">
                    @if($advert->brand)
                    <span class="bg-[#FFE6C1] text-black text-sm font-semibold px-2.5 py-0.5 rounded">{{ $advert->brand }}</span>
                @endif
                
                @if($advert->model)
                    <span class="bg-[#FFE6C1] text-black text-sm font-semibold px-2.5 py-0.5 rounded">{{ $advert->model }}</span>
                @endif
                
                @if($advert->body)
                    <span class="bg-[#FFE6C1] text-black text-sm font-semibold px-2.5 py-0.5 rounded">{{ $advert->body }}</span>
                @endif
                
                @if($advert->engine)
                    <span class="bg-[#FFE6C1] text-black text-sm font-semibold px-2.5 py-0.5 rounded">{{ $advert->engine }}</span>
                @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
        <div id="filters-container" class="blockadvert p-4 filters bg-white mt-8 w-1/3 h-96 rounded-lg shadow-md hidden xl:block">
            <!-- Блок "Модификации" -->
            <div id="modifications-container" class="modification mt-4 h-1/2">
                <label class="font-medium">Модификации:</label>
                <div id="modifications" class="flex flex-col overflow-y-auto" style="display: none;">
                    <!-- Здесь будут отображаться модификации -->
                </div>
                <div id="modifications-placeholder" class="text-gray-500 mt-2 hidden">
                    Для отображения модификаций выберите параметры автомобиля
                </div>
            </div>
        
            <!-- Блок "Дополнительно" -->
            <div id="additional-container" class="modification mt-4 h-1/2">
                <label class="font-medium">Дополнительно:</label>
                <div id="additional-options" class="flex flex-col mt-2">
                    <!-- Чекбоксы по умолчанию -->
                    <label class="flex items-center space-x-2 mb-2">
                        <input type="checkbox" class="form-checkbox text-blue-500">
                        <span class="text-gray-700">Есть установка</span>
                    </label>
                    <label class="flex items-center space-x-2 mb-2">
                        <input type="checkbox" class="form-checkbox text-blue-500">
                        <span class="text-gray-700">Есть эвакуатор</span>
                    </label>
                    <label class="flex items-center space-x-2 mb-2">
                        <input type="checkbox" class="form-checkbox text-blue-500">
                        <span class="text-gray-700">Есть рассрочка/кредит</span>
                    </label>
                    <label class="flex items-center space-x-2 mb-2">
                        <input type="checkbox" class="form-checkbox text-blue-500">
                        <span class="text-gray-700">Есть доставка</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

        <div class="h-24">
            @include('components.indexpagination', ['adverts' => $adverts])
        </div>
    @endif
</div>

<script>
// Функция для проверки, выбраны ли все значения
function checkAllValuesSelected() {
    const searchQuery = document.querySelector('input[name="search_query"]').value;
    const brand = document.getElementById('brand').value;
    const model = document.getElementById('model').value;
    const year = document.getElementById('year').value;

    // Проверяем, выбраны ли все значения
    if (searchQuery && brand && model && year) {
        // Если все значения выбраны, вызываем функцию
        toggleModificationsVisibility();
    } else {
        // Если не все значения выбраны, скрываем блок с модификациями
        const modificationsContainer = document.getElementById('modifications');
        modificationsContainer.style.display = 'none';
        updateModificationsPlaceholder();
    }
}

// Функция для обновления текста в зависимости от состояния блока с модификациями
function updateModificationsPlaceholder() {
    const modificationsContainer = document.getElementById('modifications');
    const placeholder = document.getElementById('modifications-placeholder');

    // Проверяем, скрыт ли блок с модификациями
    if (modificationsContainer.style.display === 'none') {
        // Если блок скрыт, показываем текст
        placeholder.classList.remove('hidden');
    } else {
        // Если блок видимый, скрываем текст
        placeholder.classList.add('hidden');
    }
}

// Функция для переключения видимости блока с модификациями
function toggleModificationsVisibility() {
    const modificationsContainer = document.getElementById('modifications');

    // Переключаем состояние блока
    if (modificationsContainer.style.display === 'none') {
        modificationsContainer.style.display = 'flex'; // Показываем блок
    } else {
        modificationsContainer.style.display = 'none'; // Скрываем блок
    }

    // Обновляем состояние текста
    updateModificationsPlaceholder();
}

// Добавляем обработчики событий на изменение значений полей
document.querySelector('input[name="search_query"]').addEventListener('input', checkAllValuesSelected);
document.getElementById('brand-input').addEventListener('input', checkAllValuesSelected);
document.getElementById('model-input').addEventListener('input', checkAllValuesSelected);
document.getElementById('year').addEventListener('change', checkAllValuesSelected);

// Вызываем функцию при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    checkAllValuesSelected();
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const filtersContainer = document.getElementById('filters-container');
        const searchFiltersContainer = document.getElementById('search-filters-container');
        const searchFiltersWrapper = document.getElementById('search-filters-wrapper');
    
        // Функция для проверки ширины экрана и перемещения блока фильтров
        function handleFiltersVisibility() {
            if (window.innerWidth < 1280) { // 2xl breakpoint в Tailwind
                // Скрываем основной блок фильтров
                filtersContainer.classList.add('hidden');
                // Показываем блок под поиском
                searchFiltersContainer.classList.remove('hidden');
                // Копируем содержимое фильтров в блок под поиском
                searchFiltersContainer.innerHTML = filtersContainer.innerHTML;
            } else {
                // Показываем основной блок фильтров
                filtersContainer.classList.remove('hidden');
                // Скрываем блок под поиском
                searchFiltersContainer.classList.add('hidden');
                // Очищаем содержимое блока под поиском
                searchFiltersContainer.innerHTML = '';
            }
    
            // Проверяем, пуст ли блок под поиском, и скрываем внешний блок, если он пуст
            if (searchFiltersContainer.innerHTML.trim() === '') {
                searchFiltersWrapper.classList.add('hidden');
            } else {
                searchFiltersWrapper.classList.remove('hidden');
            }
        }
    
        // Вызываем функцию при загрузке страницы
        handleFiltersVisibility();
    
        // Вызываем функцию при изменении размера окна
        window.addEventListener('resize', handleFiltersVisibility);
    });
    </script>
</body>
</html>