<!DOCTYPE html>
<html lang="ru">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $advert->product_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=9fbfa4df-7869-44a3-ae8e-0ebc49545ea9" type="text/javascript"></script>
    <link rel="shortcut icon" href="{{asset('images/Group 438.png')}}" type="image/x-icon">
    <style>
        /* Выделение строки таблицы при наведении */
        tbody tr:hover {
            background-color: #f0f8ff; /* Светло-серый цвет для выделения */
            transition: background-color 0.3s ease; /* Плавное изменение цвета */
        }

        .favorite-btn i.fas {
    color: red; /* Красный цвет для избранного */
}

        @media (max-width: 767px) {
            .fixed-buttons {
                position: fixed;
                left: 0;
                width: 100%;
                background-color: white;
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
                padding: 1rem;
                display: flex;
                justify-content: space-between;
            }
        }



        /* Стили для пагинации */
#pagination-container {
    display: flex; /* Используем flexbox для выравнивания */
    justify-content: center; /* Центрируем кнопки */
    margin: 20px 0; /* Отступы сверху и снизу */
}

#pagination-container button {
    padding: 5px 10px; /* Уменьшаем внутренние отступы */
    margin: 0 5px; /* Отступы между кнопками */
    border: 1px solid #007bff; /* Граница кнопок */
    border-radius: 10px; /* Скругляем углы */
    text-decoration: none; /* Убираем подчеркивание */
    color: #007bff; /* Цвет текста */
    background-color: white; /* Цвет фона */
    font-size: 14px; /* Уменьшаем размер шрифта */
    cursor: pointer; /* Курсор в виде указателя */
}

#pagination-container button:hover {
    background-color: #007bff; /* Цвет фона при наведении */
    color: white; /* Цвет текста при наведении */
}

/* Стили для текущей страницы */
#pagination-container button.active {
    background-color: #007bff; /* Цвет фона для активной страницы */
    color: white; /* Цвет текста для активной страницы */
    border: 1px solid #007bff; /* Граница активной страницы (можно убрать, если не нужна) */
}
    </style>
</head>
<body class="text-gray-800">
    @include('components.header-seller')

    <!-- путь -->
    <div class="container_path px-4 py-2 text-gray-600 font-medium">
        <a href="{{ route('adverts.index') }}" class=" hover:underline">Главная</a> /
        <a href="javascript:history.back()" class=" hover:underline">Поиск</a> /
        <a href="{{ route('adverts.show', $advert->id) }}" class=" hover:underline">{{ $advert->product_name }}</a>
    </div>

    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-semibold mb-4">{{ $advert->product_name }}</h1>
        <div class="flex flex-col lg:flex-row">
            <div class="lg:w-2/3">
                <div class="bg-gray-100 p-4 rounded-lg mb-4">
                    <img id="main-photo" alt="Изображение товара" class="w-full h-auto object-cover" src="{{ $advert->main_photo_url }}"/>
                </div>
                <div class="flex space-x-2 overflow-x-auto">
                    @if ($advert->additional_photo_url_1)
                        <img alt="Миниатюра 1" class="w-32 h-32 bg-gray-100 p-2 rounded-lg object-cover cursor-pointer" src="{{ $advert->additional_photo_url_1 }}" onclick="swapImage(this)"/>
                    @endif
                    @if ($advert->additional_photo_url_2)
                        <img alt="Миниатюра 2" class="w-32 h-32 bg-gray-100 p-2 rounded-lg object-cover cursor-pointer" src="{{ $advert->additional_photo_url_2 }}" onclick="swapImage(this)"/>
                    @endif
                    @if ($advert->additional_photo_url_3)
                        <img alt="Миниатюра 3" class="w-32 h-32 bg-gray-100 p-2 rounded-lg object-cover cursor-pointer" src="{{ $advert->additional_photo_url_3 }}" onclick="swapImage(this)"/>
                    @endif
                </div>
            </div>
            <div class="lg:w-1/3 lg:pl-8 mt-4 lg:mt-0">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-3xl font-bold">{{ $advert->price }} ₽</p>
                            <p class="text-gray-500 text-base">сегодня в 12:00</p>
                        </div>
                        <button class="text-gray-500 hover:text-gray-700 favorite-btn" data-advert-id="{{ $advert->id }}">
                            <i class="{{ $isFavorite ? 'fas' : 'far' }} fa-heart text-2xl"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <p class="text-red-500 font-semibold text-lg">{{ $advert->user->userAddress->city ?? 'Не указан' }}</p>
                        <div class="flex items-center mt-1">
                            <i class="fas fa-truck text-yellow-400 text-lg"></i>
                            <p class="text-yellow-400 text-base ml-1">Есть доставка</p>
                        </div>
                        <a class="text-blue-500 text-base mt-1 block" href="#">Показать условия доставки</a>
                    </div>
                    <div class="mt-4 flex justify-between items-start">
                        <div>
                            <p class="font-semibold text-lg">{{ $advert->user->username }}</p>
                            <p class="text-gray-500 text-base">{{ $advert->user->userAddress->address_line ?? 'Не указан' }}</p>
                            <a class="text-blue-500 text-base mt-1 block" href="#">показать на карте</a>
                        </div>
                        <div class="flex justify-center">
                            <img alt="Логотип {{ $advert->user->username }}" class="rounded-full w-24 h-24 object-cover" src=""/>
                        </div>
                    </div>
                    <div class="mt-6 hidden md:block">
                        <button id="show-phone-btn-desktop" class="w-full bg-blue-500 text-white py-2 rounded-lg text-lg">Показать телефон</button>
                        <button class="w-full bg-green-500 text-white py-2 rounded-lg text-lg mt-2">Написать продавцу</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-md mt-8">
            <h1 class="text-xl font-semibold mb-4">
                Продавец
            </h1>
            <div class="flex items-center">
                <img alt="Логотип {{ $advert->user->username }}" class="w-24 h-24 rounded-full mr-4 object-cover" src=""/>
                <div>
                    <h2 class="text-lg font-semibold">
                        {{ $advert->user->username }}
                    </h2>
                    <p class="text-base text-gray-600">
                        {{ $advert->user->userAddress->address_line ?? 'Не указан' }}
                    </p>
                    <div class="flex flex-col items-start mt-1">
                        <span id="phone-number" class="text-base font-semibold text-yellow-600 bg-yellow-100 px-2 py-1 rounded">
                            {{ $advert->user->UserPhoneNumber->number_1 }}
                        </span>
                        <a class="text-base text-blue-500 " href="#">
                            показать на карте
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-8">
            <h2 class="text-2xl font-semibold mb-4">Характеристики</h2>
            <div class="grid grid-cols-2 gap-2 text-base w-2/3">
                <div>Артикул</div>
                <div>{{ $advert->article_number ?? 'Не указан' }}</div>
                <div>Номер запчасти</div>
                <div>{{ $advert->part_number ?? 'Не указан' }}</div>
                <div>Марка</div>
                <div>{{ $advert->brand }}</div>
                <div>Модель</div>
                <div>{{ $advert->model }}</div>
                <div>Кузов</div>
                <div>{{ $advert->body ?? 'Не указан' }}</div>
                <div>Двигатель</div>
                <div>{{ $advert->engine ?? 'Не указан' }}</div>
                <div>Год выпуска</div>
                <div>{{ $advert->year }}</div>
                <div>Состояние</div>
                <div>{{ $advert->condition ?? 'Не указан' }}</div>
            </div>
        </div>
        <div class="mt-8">
            <h2 class="text-2xl font-semibold mb-4">Описание</h2>
            <p class="text-base">{{ $advert->body }}</p>
        </div>
        <div class="mt-8">
            <h2 class="text-2xl font-semibold mb-4">Может подойти</h2>
            <div class="bg-yellow-100 p-4 rounded-lg mb-4 text-base">
                Совместимость не гарантирована. Данные сформированы автоматически и могут содержать ошибки. Уточните применимость к вашему авто у продавца.
            </div>
            <div class="mb-4">
                <div class="flex items-center space-x-2">
                    <span class="font-bold text-lg">Марка</span>
                    <div id="brand-filters" class="flex items-center space-x-2"></div>
                </div>
                <div class="flex items-center space-x-2 mt-2">
                    <span class="font-bold text-lg">Модель</span>

                    <div id="model-filters" class="flex items-center space-x-2"></div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-base text-left" id="adverts-table">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2 whitespace-nowrap">Марка</th>
                            <th class="p-2 whitespace-nowrap">Модель</th>
                            <th class="p-2 whitespace-nowrap">Поколение</th>
                            <th class="p-2 whitespace-nowrap">Период выпуска</th>
                            <th class="p-2 whitespace-nowrap">Модификация</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Данные будут загружены динамически -->
                    </tbody>
                </table>
            </div>
            <div id="pagination-container" class="mt-4 flex justify-center space-x-2">
                <!-- Пагинация будет загружена динамически -->
            </div>
        </div>
        

        <div class="mt-8">
            <h2 class="text-2xl font-semibold mb-4">Доставка и оплата</h2>
            <p class="text-base mb-4">Гарантия и условия возврата</p>
            <p class="text-base mb-4">Гарантия есть!</p>
            <p class="text-base mb-4">**** ВНИМАНИЕ! ****</p>
            <p class="text-base mb-4">Цена указана за наличный расчет или переводом на карту!</p>
            <p class="text-base mb-4">Возможна оплата по терминалу и по счету. (+10% к стоимости).</p>
            <p class="text-base mb-4">Цена и наличие может быть неактуальными!</p>
            <p class="text-base mb-4">Перед тем как ехать, всю информацию уточняйте пожалуйста в переписке или по телефону!</p>
            <p class="text-base mb-4">ПРОСЬБА !!! до приезда в магазин !!! оповестить нас по телефону о Вашем намерении забрать в магазине товар!</p>
            <p class="text-base mb-4">Звоните нам или пишите на WhatsApp, подберем запчасти под Ваш бюджет!</p>
            <div class="text-base mb-4">
                <p class="font-semibold">Доставка и оплата</p>
                <p>Самовывоз — <a class="text-blue-500 hover:underline" href="#">Иркутск, ул.Лермонтова 321\2</a></p>
                <p>Доставка по городу Иркутск — 300 р.</p>
                <p>До транспортной компании в Иркутске — 300 р.</p>
                <p>При заказе от 50 000 р. доставка курьером в Иркутске и до транспортной компании бесплатна</p>
                <p>Самовывоз ул. Баррикад 82 ТЦ "Gold Car", пав. 5</p>
            </div>
            <div class="flex items-center p-4">
                <img alt="Логотип {{ $advert->user->username }}" class="w-24 h-24 rounded-full object-cover" src=""/>
                <div class="ml-4">
                    <div class="text-lg font-semibold">
                        {{ $advert->user->username }}
                    </div>
                    <div class="text-gray-500 text-base">
                        {{ $advert->user->userAddress->address_line ?? 'Не указан' }}
                    </div>
                </div>
                <div class="ml-auto flex space-x-4 hidden md:flex">
                    <button id="show-phone-btn-desktop2" class="bg-blue-500 text-white px-4 py-2 rounded text-lg">
                        Показать телефон
                    </button>
                    <button class="bg-green-500 text-white px-4 py-2 rounded text-lg">
                        Написать продавцу
                    </button>
                </div>
            </div>
        </div>
        <div class="mt-8">
            <div id="map" class="w-full h-96 mt-4 mb-12"></div>
        </div>
    </div>

    <!-- Фиксированные кнопки на мобильных устройствах -->
    <div class="fixed-buttons bottom-14 md:hidden flex space-x-2">
        <button id="show-phone-btn-mobile" class="w-1/2 bg-blue-500 text-white py-2 rounded-lg text-sm">Показать телефон</button>
        <button class="w-1/2 bg-green-500 text-white py-2 rounded-lg text-sm">Написать продавцу</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const showPhoneBtnDesktop = document.getElementById('show-phone-btn-desktop');
            const showPhoneBtnDesktop2 = document.getElementById('show-phone-btn-desktop2');
            const showPhoneBtnMobile = document.getElementById('show-phone-btn-mobile');

            if (showPhoneBtnDesktop) {
                showPhoneBtnDesktop.addEventListener('click', function() {
                    showPhoneBtnDesktop.textContent = "{{ $advert->user->UserPhoneNumber->number_1 }}";
                });
            }

            if (showPhoneBtnDesktop2) {
                showPhoneBtnDesktop2.addEventListener('click', function() {
                    showPhoneBtnDesktop2.textContent = "{{ $advert->user->UserPhoneNumber->number_1 }}";
                });
            }

            if (showPhoneBtnMobile) {
                showPhoneBtnMobile.addEventListener('click', function() {
                    showPhoneBtnMobile.textContent = "{{ $advert->user->UserPhoneNumber->number_1 }}";
                });
            }
        });

        ymaps.ready(init);

        function init() {
            var myMap = new ymaps.Map('map', {
                center: [52.753994, 104.622093],
                zoom: 9, 
                controls: ['zoomControl']
            });

            // Данные для геокодирования
            var address = "{{ $advert->user->userAddress->address_line ?? 'Не указан' }}";
            var prod_name = "{{ $advert->product_name }}";
            var image_url = "{{ $advert->main_photo_url }}";
            var advert_id = "{{ $advert->id }}";

            // URL изображения по умолчанию
            var defaultImageUrl = "{{ asset('images/dontfoto.jpg') }}";

            // Функция для геокодирования и добавления метки на карту
            function geocodeAndAddToMap(address, prod_name, image_url, advert_id) {
                if (!address || address === "Не указан") {
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
                    myMap.setCenter(coords, 15, {
                        checkZoomRange: true
                    });
                });
            }

            // Выполняем геокодирование и добавление метки для адреса
            geocodeAndAddToMap(address, prod_name, image_url, advert_id);
        }

        function swapImage(thumbnail) {
            const mainPhoto = document.getElementById('main-photo');
            const mainPhotoSrc = mainPhoto.src;
            const thumbnailSrc = thumbnail.src;

            mainPhoto.src = thumbnailSrc;
            thumbnail.src = mainPhotoSrc;
        }

        document.querySelectorAll('td.truncate').forEach(function(cell) {
            cell.addEventListener('click', function() {
                const fullText = cell.getAttribute('data-fulltext');
                if (cell.textContent !== fullText) {
                    cell.textContent = fullText;
                } else {
                    cell.textContent = cell.textContent.slice(0, 20) + '...';
                }
            });
        });


document.querySelectorAll('.favorite-btn').forEach(button => {
    button.addEventListener('click', function () {
        const advertId = this.getAttribute('data-advert-id');
        const icon = this.querySelector('i');

        // Определяем текущее состояние (добавлено или нет)
        const isFavorite = icon.classList.contains('fas');

        if (isFavorite) {
            // Удаляем из избранного
            fetch(`/favorites/remove/${advertId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                   
                }
            });
        } else {
            // Добавляем в избранное
            fetch(`/favorites/add/${advertId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                   
                }
            });
        }
    });
});
    </script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const advertId = {{ $advert->id }};
    const brandFiltersContainer = document.getElementById('brand-filters');
    const modelFiltersContainer = document.getElementById('model-filters');

    let allBrands = new Set(); // Все марки
    let allModels = new Set(); // Все модели

    // Функция для загрузки данных таблицы
    function loadTableData(advertId, page = 1, filterType = null, filterValue = null) {
        let url = `/advert/${advertId}/table-data?page=${page}`;
        if (filterType && filterValue) {
            url += `&filterType=${filterType}&filterValue=${filterValue}`;
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const tableBody = document.querySelector('#adverts-table tbody');
                const paginationContainer = document.querySelector('#pagination-container');

                // Очищаем таблицу и пагинацию
                tableBody.innerHTML = '';
                paginationContainer.innerHTML = '';

                // Заполняем таблицу данными
                data.adverts.forEach(car => {
                    const row = document.createElement('tr');
                    row.className = 'border-b';
                    row.innerHTML = `
                        <td class="p-2 truncate" data-fulltext="${car.brand}">${car.brand}</td>
                        <td class="p-2 truncate" data-fulltext="${car.model}">${car.model}</td>
                        <td class="p-2 truncate" data-fulltext="${car.generation}">${car.generation}</td>
                        <td class="p-2 truncate" data-fulltext="${car.year_from} - ${car.year_before}">${car.year_from} - ${car.year_before}</td>
                        <td class="p-2 truncate" data-fulltext="${car.modification}">${car.modification}</td>
                    `;
                    tableBody.appendChild(row);
                });

                // Собираем все марки и модели из данных таблицы
                if (page === 1) { // Собираем данные только при первой загрузке
                    data.adverts.forEach(car => {
                        allBrands.add(car.brand);
                        allModels.add(car.model);
                    });

                    // Обновляем фильтры один раз при загрузке страницы
                    updateFilters();
                }

                // Создаем пагинацию
                const pagination = data.pagination;
                if (pagination.last_page > 1) {
                    // Кнопка "Назад" (используем &laquo;)
                    if (pagination.current_page > 1) {
                        const prevButton = document.createElement('button');
                        prevButton.innerHTML = '&laquo;'; // Символ «
                        prevButton.className = 'px-4 py-2 rounded bg-blue-500 text-white'; // Стили из вашего примера
                        prevButton.addEventListener('click', () => loadTableData(advertId, pagination.current_page - 1, filterType, filterValue));
                        paginationContainer.appendChild(prevButton);
                    }

                    // Кнопки страниц
                    for (let i = 1; i <= pagination.last_page; i++) {
                        const pageButton = document.createElement('button');
                        pageButton.textContent = i;
                        pageButton.className = `px-4 py-2 rounded ${pagination.current_page === i ? 'bg-blue-500 text-white active' : 'bg-gray-200'}`;
                        pageButton.addEventListener('click', () => loadTableData(advertId, i, filterType, filterValue));
                        paginationContainer.appendChild(pageButton);
                    }

                    // Кнопка "Вперед" (используем &raquo;)
                    if (pagination.current_page < pagination.last_page) {
                        const nextButton = document.createElement('button');
                        nextButton.innerHTML = '&raquo;'; // Символ »
                        nextButton.className = 'px-4 py-2 rounded bg-blue-500 text-white'; // Стили из вашего примера
                        nextButton.addEventListener('click', () => loadTableData(advertId, pagination.current_page + 1, filterType, filterValue));
                        paginationContainer.appendChild(nextButton);
}
                }
            });
    }

    // Функция для обновления фильтров
    function updateFilters() {
        // Очищаем контейнеры фильтров
        brandFiltersContainer.innerHTML = '';
        modelFiltersContainer.innerHTML = '';

        // Добавляем "Показать все" для марок
        const showAllBrands = document.createElement('a');
        showAllBrands.href = '#';
        showAllBrands.className = 'text-blue-600 text-lg filter-link';
        showAllBrands.setAttribute('data-filter', 'brand');
        showAllBrands.setAttribute('data-value', 'all');
        showAllBrands.textContent = 'Показать все';
        brandFiltersContainer.appendChild(showAllBrands);

        // Добавляем марки в фильтры
        allBrands.forEach(brand => {
            const link = document.createElement('a');
            link.href = '#';
            link.className = 'text-blue-600 text-lg filter-link';
            link.setAttribute('data-filter', 'brand');
            link.setAttribute('data-value', brand);
            link.textContent = brand;
            brandFiltersContainer.appendChild(link);
        });

        // Добавляем "Показать все" для моделей
        const showAllModels = document.createElement('a');
        showAllModels.href = '#';
        showAllModels.className = 'text-blue-600 text-lg filter-link';
        showAllModels.setAttribute('data-filter', 'model');
        showAllModels.setAttribute('data-value', 'all');
        showAllModels.textContent = 'Показать все';
        modelFiltersContainer.appendChild(showAllModels);

        // Добавляем модели в фильтры
        allModels.forEach(model => {
            const link = document.createElement('a');
            link.href = '#';
            link.className = 'text-blue-600 text-lg filter-link';
            link.setAttribute('data-filter', 'model');
            link.setAttribute('data-value', model);
            link.textContent = model;
            modelFiltersContainer.appendChild(link);
        });

        // Добавляем обработчики событий для фильтров
        document.querySelectorAll('.filter-link').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const filterType = this.getAttribute('data-filter');
                const filterValue = this.getAttribute('data-value');

                // Сбрасываем фильтр, если выбрано "Показать все"
                if (filterValue === 'all') {
                    loadTableData(advertId);
                    return;
                }

                // Фильтруем строки таблицы
                loadTableData(advertId, 1, filterType, filterValue);
            });
        });
    }

    // Загружаем данные таблицы при загрузке страницы
    loadTableData(advertId);
});
</script>
</body>
</html>