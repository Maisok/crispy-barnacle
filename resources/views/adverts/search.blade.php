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
    <style>
        /* Добавляем стили для отображения карты на весь экран */
        #map.full-screen {
            position: fixed;
            top: 64px; /* Высота шапки */
            left: 0;
            width: 100%;
            height: calc(100% - 64px); /* Высота карты без учета шапки */
            z-index: 1000;
        }

        .aspect-square {
    aspect-ratio: 1 / 1;
}

body {
    font-family: 'Nunito', sans-serif;
}

        .blockadvert{
        border: 0.2px solid #ccc;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
         border-radius: 15px;
}

        /* Стили для кнопок */
        .buttons-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            background-color: white;
            padding: 16px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1001;
        }

        /* Стили для отображения меню на весь экран */
        #fullScreenMenu, #filterMenu {
            position: fixed;
            top: 64px; /* Высота шапки */
            left: 0;
            width: 100%;
            height: calc(100% - 64px); /* Высота меню без учета шапки */
            background-color: white;
            z-index: 1000;
            overflow-y: auto;
            display: none; /* Скрываем меню по умолчанию */
        }

        #fullScreenMenu.active, #filterMenu.active {
            display: block; /* Показываем меню, когда оно активно */
        }
    </style>
</head>
<body class="flex flex-col items-center ">
    <!-- Шапка -->
    @include('components.header-seller')   

    <!-- Карта -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js"></script>

    <div id="map" class="w-full h-64 md:h-96 hidden sm:block"></div>

    <!-- Поисковая форма -->
    <div class="w-full mt-20 md:w-3/4 mt-10 mx-auto hidden sm:block">
        @include('components.search-form')  
    </div>

    <div class=" blockadvert filters bg-white mt-4 w-full hidden md:w-3/4 p-4 rounded-lg shadow-md sm:block md:block 2xl:hidden">
        <form method="GET" action="{{ route('adverts.search') }}">
            <h4 class="text-xl font-semibold mb-4">Фильтры по двигателю:</h4>
            @foreach($engines as $engine)
                <div>
                    <input type="checkbox" name="engines[]" value="{{ $engine }}" id="engine-{{ $engine }}"
                        {{ in_array($engine, request('engines', [])) || !request()->has('engines') ? 'checked' : '' }}
                        class="mr-2">
                    <label for="engine-{{ $engine }}" class="text-lg">{{ !empty($engine) ? ucfirst($engine) : 'Не указан' }}</label>
                </div>
            @endforeach

            <!-- Сохраняем другие параметры запроса -->
            <input type="hidden" name="search_query" value="{{ request('search_query') }}">
            <input type="hidden" name="brand" value="{{ request('brand') }}">
            <input type="hidden" name="model" value="{{ request('model') }}">
            <input type="hidden" name="year" value="{{ request('year') }}">

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg mt-4">Применить фильтры</button>
        </form>
    </div>

    <!-- Основной контент -->
    <div class="flex flex-col w-full sm:flex-row sm:justify-start sm:w-full md:w-3/4 mt-8">
        <!-- Результаты поиска -->
        <div class="w-full flex justify-center items-center space-x-4 mt-14 mb-4 sm:hidden px-4 hidden-on-map">
            <button id="sortButton" class="flex items-center justify-center px-4 py-2 bg-gray-700 text-white rounded-md w-1/2">
                <i class="fas fa-sort mr-2"></i>
                Сортировка 
            </button>
            <button id="filterButton" class="flex items-center justify-center px-4 py-2 bg-gray-700 text-white rounded-md w-1/2">
                <i class="fas fa-filter mr-2"></i>
                Фильтры
            </button>
        </div>
        
        <div id="mapsButton" class="w-full flex justify-center items-center space-x-4 mb-4 sm:hidden px-4">
            <button id="listButton" class="flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg w-1/2">
                <i class="fas fa-th-large mr-2"></i>
                Списком
            </button>
            <button id="mapButton" class="text-xs flex items-center justify-center pl-2 py-3 bg-white text-gray-600 border rounded-lg w-1/2">
                <i class="fas fa-map mr-2"></i>
                Показать на карте
            </button>
        </div>

        <div id="listView" class="w-full">
            @if($allAdverts->count())
                <!-- Для телефонов -->
                <div id="phoneListView" class="grid grid-cols-2 gap-4 w-full sm:hidden">
                    @foreach($allAdverts as $advert)
                    <div class="bg-white rounded-lg shadow p-4 mt-8 cursor-pointer transition-colors duration-300 hover:bg-blue-100" onclick="location.href='{{ route('adverts.show', $advert->id) }}'" tabindex="0" role="button">
                        <div class="relative">
                            @if ($advert->main_photo_url)
                                <img src="{{ $advert->main_photo_url }}" alt="{{ $advert->product_name }} - Главное фото" class="w-full h-48 object-cover rounded-lg">
                            @else
                                <img src="{{ asset('images/dontfoto.jpg') }}" alt="Фото отсутствует" class="w-full h-48 object-cover rounded-lg">
                            @endif
                            <span class="absolute top-2 right-2 bg-yellow-200 text-yellow-800 text-xs font-semibold px-2 py-1 rounded">
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
                            <div class="flex flex-wrap items-center text-gray-500 text-sm mt-2">
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

                <div class="hidden sm:flex w-full flex-col items-start justify-center 
                md:flex w-full flex-col items-start justify-center 
                lg:flex w-full flex-col items-start justify-center 
                xl:flex w-full flex-col items-start justify-center 
                2xl:flex w-full flex-col items-start justify-center">
    @foreach($allAdverts as $advert)
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
                <!-- Подключение пагинации -->
            @else
                <p class="text-center text-lg mt-8">Нет результатов для отображения.</p>
            @endif
        </div>

        <!-- Фильтры по параметру engine для больших экранов -->
        <div class=" filters bg-white mt-4 ml-auto hidden 2xl:block">
            <form method="GET" class="blockadvert p-2 rounded-lg shadow-md " action="{{ route('adverts.search') }}">
                <h4 class="text-xl text-center font-semibold mb-4">Фильтры по двигателю:</h4> <!-- Укажите правильный маршрут для обработки формы -->
                @foreach($engines as $engine)
                    <div>
                        <input type="checkbox" name="engines[]" value="{{ $engine }}" id="engine-{{ $engine }}"
                            {{ in_array($engine, request('engines', [])) || !request()->has('engines') ? 'checked' : '' }}
                            class="mr-2">
                        <label for="engine-{{ $engine }}" class="text-lg">{{ !empty($engine) ? ucfirst($engine) : 'Не указан' }}</label>
                    </div>
                @endforeach

                <!-- Сохраняем другие параметры запроса -->
                <input type="hidden" name="search_query" value="{{ request('search_query') }}">
                <input type="hidden" name="brand" value="{{ request('brand') }}">
                <input type="hidden" name="model" value="{{ request('model') }}">
                <input type="hidden" name="year" value="{{ request('year') }}">

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg mt-4">Применить фильтры</button>
            </form>
        </div>
    </div>

    <div id="pagination" class="mt-8 mb-14">
        @include('components.pagination', ['allAdverts' => $allAdverts])
    </div>

    <div id="fullScreenMenu" class="hidden w-full">
        <div class="menu-content w-full">
            <button id="closeMenuButton" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 close-button w-10 h-10 flex items-center justify-center">
                <i class="fas fa-times text-xl"></i>
            </button>
            <div class="bg-white p-4 rounded-lg shadow-lg w-full">
                <h2 class="text-lg font-semibold mb-4">Фильтры</h2>
                
                <div class="mb-4">
                    <h3 class="font-medium mb-2">Состояние детали</h3>
                    <div class="flex items-center mb-2">
                        <input type="radio" id="new" name="condition" class="mr-2">
                        <label for="new">Новая</label>
                    </div>
                    <div class="flex items-center mb-2">
                        <input type="radio" id="used" name="condition" class="mr-2">
                        <label for="used">Б/У деталь</label>
                    </div>
                    <div class="flex items-center mb-2">
                        <input type="radio" id="unspecified" name="condition" class="mr-2">
                        <label for="unspecified">Не указано</label>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="font-medium mb-2">Цена</h3>
                    <div class="flex space-x-2">
                        <input type="text" placeholder="Цена от" class="border rounded p-2 w-full">
                        <input type="text" placeholder="до" class="border rounded p-2 w-full">
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="font-medium mb-2">Фото</h3>
                    <div class="flex items-center mb-2">
                        <input type="checkbox" id="photo" class="mr-2">
                        <label for="photo">Только с фото</label>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="font-medium mb-2">Доставка</h3>
                    <div class="flex items-center mb-2">
                        <input type="radio" id="pickup" name="delivery" class="mr-2">
                        <label for="pickup">С самовывозом</label>
                    </div>
                    <div class="flex items-center mb-2">
                        <input type="radio" id="delivery" name="delivery" class="mr-2">
                        <label for="delivery">С доставкой</label>
                    </div>
                </div>

                <div class="mb-4">
                    <h3 class="font-medium mb-2">Модель кузова</h3>
                    <div class="flex items-center mb-2">
                        <input type="checkbox" id="bodyModel1" class="mr-2">
                        <label for="bodyModel1">Тут список доступных кузовов</label>
                    </div>
                    <div class="flex items-center mb-2">
                        <input type="checkbox" id="bodyModel2" class="mr-2" checked>
                        <label for="bodyModel2">gx90</label>
                    </div>
                    <a href="#" class="text-blue-500">Показать все</a>
                </div>

                <div class="mb-4">
                    <h3 class="font-medium mb-2">Модель двигателя</h3>
                    <div class="flex items-center mb-2">
                        <input type="checkbox" id="engineModel1" class="mr-2" checked>
                        <label for="engineModel1">Тут список доступных двигателей</label>
                    </div>
                    <a href="#" class="text-blue-500">Показать все</a>
                </div>

                <div class="mb-4">
                    <h3 class="font-medium mb-2">OEM номер</h3>
                    <div class="flex items-center mb-2">
                        <input type="checkbox" id="oemNumber1" class="mr-2">
                        <label for="oemNumber1">Тут список доступных номеров детали</label>
                    </div>
                    <a href="#" class="text-blue-500">Показать все</a>
                </div>

                <div class="mb-4">
                    <h3 class="font-medium mb-2">Перед/Зад</h3>
                    <div class="flex items-center mb-2">
                        <input type="checkbox" id="frontBack1" class="mr-2" checked>
                        <label for="frontBack1">Тут список доступных расположений</label>
                    </div>
                    <a href="#" class="text-blue-500">Показать все</a>
                </div>

                <div class="mb-4">
                    <h3 class="font-medium mb-2">Слева/Справа</h3>
                    <div class="flex items-center mb-2">
                        <input type="checkbox" id="leftRight1" class="mr-2" checked>
                        <label for="leftRight1">Тут список доступных расположений</label>
                    </div>
                    <a href="#" class="text-blue-500">Показать все</a>
                </div>

                <div class="mb-4">
                    <h3 class="font-medium mb-2">Верх/Низ</h3>
                    <div class="flex items-center mb-2">
                        <input type="checkbox" id="topBottom1" class="mr-2" checked>
                        <label for="topBottom1">Тут список доступных расположений</label>
                    </div>
                    <a href="#" class="text-blue-500">Показать все</a>
                </div>

                <div class="flex space-x-2">
                    <button class="bg-blue-500 text-white py-2 px-4 rounded">Сохранить</button>
                    <button class="border border-blue-500 text-blue-500 py-2 px-4 rounded">Сбросить</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Меню фильтров на весь экран -->
    <div id="filterMenu" class="hidden">
        <div class="menu-content">
            <button id="closeFilterMenuButton" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 close-button w-10 h-10 flex items-center justify-center">
                <i class="fas fa-times text-xl"></i>
            </button>
            <div class="bg-white p-4 rounded-lg shadow-lg w-full">
                <div class="text-center space-y-8">
                    <p class="text-black text-lg">Сначала недавно добавленные</p>
                    <p class="text-black text-lg">Сначала давно добавленные</p>
                    <p class="text-black text-lg">Сначала дешёвые</p>
                    <p class="text-black text-lg">Сначала дорогие</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Подключение Yandex Maps -->
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=9fbfa4df-7869-44a3-ae8e-0ebc49545ea9" type="text/javascript"></script>
    <script>
        let mapInitialized = false;
        let myMap;

        ymaps.ready(function() {
            myMap = new ymaps.Map('map', {
                center: [52.753994, 104.622093],
                zoom: 9, 
                controls: ['zoomControl']
            });

            // Отключаем взаимодействие с картой
            myMap.behaviors.disable('drag');
            myMap.behaviors.disable('scrollZoom');

            // Массив адресов для геокодирования
            var addresses = @json($addresses);
            var prod_name = @json($prod_name);
            var image_prod = @json($image_prod);
            var advert_ids = @json($advert_ids);

            // URL изображения по умолчанию
            var defaultImageUrl = "{{ asset('images/dontfoto.jpg') }}";

            // Функция для геокодирования и добавления меток на карту
            function geocodeAndAddToMap(address, prod_name, image_url, advert_id) {
                if (address == "Не указан") {
                    return; // Пропускаем добавление метки, если адрес отсутствует
                }

                ymaps.geocode(address, {
                    results: 1
                }).then(function (res) {
                    var firstGeoObject = res.geoObjects.get(0),
                        coords = firstGeoObject.geometry.getCoordinates(),
                        bounds = firstGeoObject.properties.get('boundedBy');

                    // Проверяем, существует ли URL изображения
                    var imageUrl = image_url ? image_url : defaultImageUrl;

                    // Создаем метку с пользовательским контентом
                    var placemark = new ymaps.Placemark(coords, {
                        balloonContent: address + '<br><a href="{{ route('adverts.show', '') }}/' + advert_id + '">' + prod_name + '</a><br><img src="' + imageUrl + '" alt="Фото отсутствует" width="100">', // Пользовательский контент в баллуне с изображением и ссылкой
                        hintContent: prod_name // Пользовательский контент в подсказке
                    }, {
                        preset: 'islands#darkBlueDotIconWithCaption'
                    });

                    myMap.geoObjects.add(placemark);

                    // Центрируем карту на последней добавленной метке
                    myMap.setCenter(coords, 10, {
                        checkZoomRange: true
                    });
                });
            }

            // Выполняем геокодирование и добавление меток для каждого адреса
            addresses.forEach(function (address, index) {
                geocodeAndAddToMap(address, prod_name[index], image_prod[index], advert_ids[index]);
            });

            // Обработчик клика на карту
            document.getElementById('map').addEventListener('click', function() {
                if (!mapInitialized) {
                    mapInitialized = true;
                    // Включаем взаимодействие с картой
                    myMap.behaviors.enable('drag');
                    myMap.behaviors.enable('scrollZoom');
                }
            });

            // Обработчик ухода курсора с карты
            document.getElementById('map').addEventListener('mouseleave', function() {
                if (mapInitialized) {
                    // Отключаем взаимодействие с картой
                    myMap.behaviors.disable('drag');
                    myMap.behaviors.disable('scrollZoom');
                    mapInitialized = false;
                }
            });
        });

        const listButton = document.getElementById('listButton');
        const mapButton = document.getElementById('mapButton');
        const hiddenOnMapBlock = document.querySelector('.hidden-on-map');

        // JavaScript для переключения отображения
        document.getElementById('listButton').addEventListener('click', function() {
            document.getElementById('phoneListView').classList.remove('hidden');
            document.getElementById('map').classList.remove('full-screen');
            document.getElementById('map').classList.add('hidden');
            document.getElementById('fullScreenMenu').classList.remove('active');
            document.getElementById('filterMenu').classList.remove('active');
            document.getElementById('pagination').classList.remove('hidden');
            document.getElementById('listButton').classList.add('bg-blue-600', 'text-white');
            document.getElementById('listButton').classList.remove('bg-white', 'text-gray-600', 'border');
            document.getElementById('mapButton').classList.remove('bg-blue-600', 'text-white');
            document.getElementById('mapButton').classList.add('bg-white', 'text-gray-600', 'border');
            hiddenOnMapBlock.classList.remove('hidden');

            // Убираем mt-14 при нажатии на кнопку "Списком"
            document.getElementById('mapsButton').classList.remove('mt-14');
        });

        document.getElementById('mapButton').addEventListener('click', function() {
            document.getElementById('phoneListView').classList.add('hidden');
            document.getElementById('map').classList.remove('hidden');
            document.getElementById('map').classList.add('full-screen');
            document.getElementById('fullScreenMenu').classList.remove('active');
            document.getElementById('filterMenu').classList.remove('active');
            document.getElementById('pagination').classList.add('hidden');
            document.getElementById('mapButton').classList.add('bg-blue-600', 'text-white');
            document.getElementById('mapButton').classList.remove('bg-white', 'text-gray-600', 'border');
            document.getElementById('listButton').classList.remove('bg-blue-600', 'text-white');
            document.getElementById('listButton').classList.add('bg-white', 'text-gray-600', 'border');
            hiddenOnMapBlock.classList.add('hidden');
            // Добавляем mt-14 при нажатии на кнопку "Показать на карте"
            document.getElementById('mapsButton').classList.add('mt-14');
        });

        document.getElementById('sortButton').addEventListener('click', function() {
            document.getElementById('phoneListView').classList.add('hidden');
            document.getElementById('map').classList.remove('full-screen');
            document.getElementById('map').classList.add('hidden');
            document.getElementById('fullScreenMenu').classList.toggle('active');
            document.getElementById('filterMenu').classList.remove('active');
            document.getElementById('pagination').classList.add('hidden');
        
            document.getElementById('listButton').classList.remove('bg-blue-600', 'text-white');
            document.getElementById('listButton').classList.add('bg-white', 'text-gray-600', 'border');
            document.getElementById('mapButton').classList.remove('bg-blue-600', 'text-white');
            document.getElementById('mapButton').classList.add('bg-white', 'text-gray-600', 'border');
        });

        document.getElementById('filterButton').addEventListener('click', function() {
            document.getElementById('phoneListView').classList.add('hidden');
            document.getElementById('map').classList.remove('full-screen');
            document.getElementById('map').classList.add('hidden');
            document.getElementById('fullScreenMenu').classList.remove('active');
            document.getElementById('filterMenu').classList.toggle('active');
            document.getElementById('pagination').classList.add('hidden');
            document.getElementById('listButton').classList.remove('bg-blue-600', 'text-white');
            document.getElementById('listButton').classList.add('bg-white', 'text-gray-600', 'border');
            document.getElementById('mapButton').classList.remove('bg-blue-600', 'text-white');
            document.getElementById('mapButton').classList.add('bg-white', 'text-gray-600', 'border');
        });

        // JavaScript для закрытия меню
        document.getElementById('closeMenuButton').addEventListener('click', function() {
            document.getElementById('fullScreenMenu').classList.remove('active');
            document.getElementById('phoneListView').classList.remove('hidden');
            document.getElementById('map').classList.remove('full-screen');
            document.getElementById('map').classList.add('hidden');
            document.getElementById('pagination').classList.remove('hidden');
            document.getElementById('listButton').classList.add('bg-blue-600', 'text-white');
            document.getElementById('listButton').classList.remove('bg-white', 'text-gray-600', 'border');
            document.getElementById('mapButton').classList.remove('bg-blue-600', 'text-white');
            document.getElementById('mapButton').classList.add('bg-white', 'text-gray-600', 'border');
        });

        document.getElementById('closeFilterMenuButton').addEventListener('click', function() {
            document.getElementById('filterMenu').classList.remove('active');
            document.getElementById('phoneListView').classList.remove('hidden');
            document.getElementById('map').classList.remove('full-screen');
            document.getElementById('map').classList.add('hidden');
            document.getElementById('pagination').classList.remove('hidden');
            document.getElementById('listButton').classList.add('bg-blue-600', 'text-white');
            document.getElementById('listButton').classList.remove('bg-white', 'text-gray-600', 'border');
            document.getElementById('mapButton').classList.remove('bg-blue-600', 'text-white');
            document.getElementById('mapButton').classList.add('bg-white', 'text-gray-600', 'border');
        });
    </script>
</body>
</html>