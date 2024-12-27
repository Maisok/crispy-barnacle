<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Мои объявления</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="shortcut icon" href="{{asset('images/Group 438.png')}}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <style>
        /* Ограничиваем ширину текста и добавляем многоточие */
        .ellipsis {
            white-space: nowrap; /* Запрещаем перенос текста на новую строку */
            overflow: hidden; /* Скрываем текст, который не помещается */
            text-overflow: ellipsis; /* Добавляем многоточие */
            max-width: 30ch; /* Ограничиваем ширину до 30 символов */
        }

        body {
            font-family: 'Nunito', sans-serif;
        }

        /* Модальное окно */
        .modal {
            display: none; /* Скрываем модальное окно по умолчанию */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            position: relative;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-open {
            overflow: hidden; /* Блокируем скролл страницы */
        }

        #modalMainImg {
            width: 100%;
            height: 256px; /* Фиксированная высота */
            object-fit: contain; /* Вставка по размеру с полями */
            border-radius: 0.5rem;
        }

        #mainImgPlaceholder {
            display: none; /* Скрываем заполнитель по умолчанию */
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #f3f4f6;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 1rem;
        }

        /* Стили для таблицы */
        .table {
            font-size: 1rem; /* Увеличиваем размер шрифта */
            width: 100%;
            border-collapse: collapse;
            line-height: 1.2; /* Уменьшаем межстрочный интервал */
        }

        .table th, .table td {
            padding: 0.5rem !important; /* Увеличиваем вертикальные отступы */
            border: 1px solid #e2e8f0;
            height: 2.5rem !important; /* Фиксированная высота ячейки */
            vertical-align: middle; /* Выравниваем текст по центру по вертикали */
        }

        .table th {
            background-color: #f7fafc;
            font-weight: 600;
            text-align: left;
        }

        .table tbody tr {
            height: 1.2rem !important; /* Фиксированная высота строки */
        }

        .table tbody tr:hover {
            background-color: #edf2f7;
        }

        /* Убираем стрелку у выпадающего списка */
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }
    </style>
</head>
<body class="bg-gray-100">

    <script src="{{ asset('js/my_adverts.js') }}" defer></script>
    <link rel="shortcut icon" href="{{asset('images/Group 438.png')}}" type="image/x-icon">
    @include('components.header-seller')

    <div class="grid grid-cols-10 gap-2 items-center w-full">
        <!-- Форма поиска (50% ширины) -->
        <form method="GET" action="{{ route('adverts.my_adverts') }}" class="border-r border-gray-300 w-full h-10 p-1 pl-4 search-form flex items-center justify-start gap-1 col-span-5">
            <!-- Поле ввода текста (50% ширины) -->
            <input type="text" name="search" class="h-full searchInput p-1 border rounded-md w-2/5 text-sm" placeholder="Поиск по наименованию или номеру" value="{{ request()->input('search') }}">
            
            <!-- Выпадающий список (оставшееся пространство) -->
            <select name="brand" class="brandFilter h-full p-1 border w-3/10 rounded-md text-sm">
                <option value="">Все марки</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand }}" {{ request()->get('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                @endforeach
            </select>
            
            <!-- Кнопка "Поиск" (оставшееся пространство) -->
            <button type="submit" class=" btn-search p-2 text-xs bg-blue-500 text-white rounded-md">Поиск</button>
            
            <!-- Кнопка "Сбросить" (оставшееся пространство) -->
            <a href="{{ route('adverts.my_adverts') }}" class="text-center  btn-reset p-2 text-xs bg-gray-300 text-black rounded-md">Сбросить</a>
        </form>
    
        <!-- Блок с кнопками "Удалить несколько" и "Удалить выбранные" (30% ширины) -->
        <div class="border-r border-gray-300 w-full h-10 gap-1 col-span-3 flex items-center justify-center">
            <button id="deleteMultipleBtn" class="w-1/4 text-xs btn-delete-multiple p-2 bg-gray-500 text-white rounded-md w-full">Выбрать</button>
            <button id="deleteSelectedBtn" class="w-2/5 text-xs btn-delete-selected p-2 bg-gray-300 text-gray-500 rounded-md opacity-100 cursor-not-allowed w-full whitespace-nowrap" disabled>
                Удалить выбранные
            </button>
        </div>
    
        <!-- Выпадающий список статусов (20% ширины) -->
        <div class="col-span-2 flex items-center justify-center p-1">
            <span class="mr-2 text-xs ">Отображать:</span>
            <select id="statusFilter" class="appearance-none rounded p-2 text-sm font-bold bg-gray-100 focus:outline-none focus:ring-0">
                <option value="">Все статусы</option>
                <option value="active">Активные</option>
                <option value="inactive">Неактивные</option>
                <option value="sold">Проданные</option>
                <option value="archived">Архивные</option>
            </select>
        </div>
    </div>
    

        <!-- Таблица объявлений -->
        @if ($adverts->isEmpty())
            <p>У вас нет активных объявлений.</p>
        @else
        <div class="overflow-x-auto w-full px-4">
            <table class="table w-full border-collapse">
                <thead>
                    <tr>
                        <th class="bg-gray-200 border p-0">Артикул</th>
                        <th class="bg-gray-200 border p-0">Наименование</th>
                        <th class="bg-gray-200 border p-0">Состояние</th>
                        <th class="bg-gray-200 border p-0">Марка</th>
                        <th class="bg-gray-200 border p-0">Модель</th>
                        <th class="bg-gray-200 border p-0">Кузов</th>
                        <th class="bg-gray-200 border p-0">Номер</th>
                        <th class="bg-gray-200 border p-0">Двигатель</th>
                        <th class="bg-gray-200 border p-0">Год</th>
                        <th class="bg-gray-200 border p-0">L/R</th>
                        <th class="bg-gray-200 border p-0">F/R</th>
                        <th class="bg-gray-200 border p-0">U/D</th>
                        <th class="bg-gray-200 border p-0">Цена</th>
                        <th class="bg-gray-200 border p-0">Цвет</th>
                        <th class="bg-gray-200 border p-0">Применимость/Описание</th>
                        <th class="bg-gray-200 border p-0">Количество</th>
                        <th class="bg-gray-200 border p-0">Наличие</th>
                        <th class="bg-gray-200 border p-0">Действия</th> <!-- Новый столбец для кнопок -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($adverts as $advert)
                    <tr data-id-info="{{ $advert->id }}"
                        data-art-number="{{ $advert->art_number }}"
                        data-product-name="{{ $advert->product_name }}"
                        data-brand-info="{{ $advert->brand }}"
                        data-model-info="{{ $advert->model }}"
                        data-body-info="{{ $advert->body }}"
                        data-number-info="{{ $advert->number }}"
                        data-engine-info="{{ $advert->engine }}"
                        data-main-photo-url="{{ $advert->main_photo_url }}"
                        data-additional-photo-url-1="{{ $advert->additional_photo_url_1 }}"
                        data-additional-photo-url-2="{{ $advert->additional_photo_url_2 }}"
                        data-additional-photo-url-3="{{ $advert->additional_photo_url_3 }}"
                        data-price-info="{{ $advert->price }}">
                        <td class="border p-0 ellipsis">{{ $advert->art_number }}</td>
                        <td class="border p-0 ellipsis" title="{{ $advert->product_name }}">{{ $advert->product_name }}</td>
                        <td class="border p-0 ellipsis">{{ $advert->new_used }}</td>
                        <td class="border p-0 ellipsis">{{ $advert->brand }}</td>
                        <td class="border p-0 ellipsis">{{ $advert->model }}</td>
                        <td class="border p-0 ellipsis">{{ $advert->body }}</td>
                        <td class="border p-0 ellipsis">{{ $advert->number }}</td>
                        <td class="border p-0 ellipsis">{{ $advert->engine }}</td>
                        <td class="border p-0 ellipsis">{{ $advert->year }}</td>
                        <td class="border p-0 ellipsis">{{ $advert->L_R }}</td>
                        <td class="border p-0 ellipsis">{{ $advert->F_R }}</td>
                        <td class="border p-0 ellipsis">{{ $advert->U_D }}</td>
                        <td class="border p-0 ellipsis">{{ $advert->price }}</td>
                        <td class="border p-0 ellipsis">{{ $advert->color }}</td>
                        <td class="border p-0 ellipsis" title="{{ $advert->applicability }}">{{ $advert->applicability }}</td>
                        <td class="border p-0 ellipsis">{{ $advert->quantity }}</td>
                        <td class="border p-0 ellipsis">{{ $advert->availability }}</td>
                        <td class="border p-0">
                            <button class="btn btn-primary edit-btn p-1 bg-blue-500 text-white rounded-md mr-2" data-id="{{ $advert->id }}">
                                <i class="fas fa-pencil-alt"></i>
                            </button>

                            <form action="{{ route('adverts.destroy', $advert->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger p-1 bg-red-500 text-white rounded-md" onclick="return confirm('Вы уверены?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Подключение пагинации -->
        @include('components.indexpagination', ['adverts' => $adverts])
        @endif
    </div>

    <!-- Модальное окно для редактирования -->
    <div id="editModal" class="modal fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="modal-content bg-white p-4 rounded-lg w-full md:w-3/4 lg:w-1/2">
            <span class="close text-gray-500 text-2xl font-bold float-right cursor-pointer">&times;</span>
            <h2 class="text-xl font-bold mb-4">Редактировать объявление</h2>
            <form id="editForm" action="{{ route('adverts.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editAdvertId" name="id">
                <input type="hidden" id="old_art_number" name="old_art_number">
                <input type="hidden" id="old_product_name" name="old_product_name">
                <input type="hidden" id="old_number" name="old_number">
                <input type="hidden" id="old_new_used" name="old_new_used">
                <input type="hidden" id="old_brand" name="old_brand">
                <input type="hidden" id="old_model" name="old_model">
                <input type="hidden" id="old_year" name="old_year">
                <input type="hidden" id="old_body" name="old_body">
                <input type="hidden" id="old_engine" name="old_engine">
                <input type="hidden" id="old_L_R" name="old_L_R">
                <input type="hidden" id="old_F_R" name="old_F_R">
                <input type="hidden" id="old_U_D" name="old_U_D">
                <input type="hidden" id="old_color" name="old_color">
                <input type="hidden" id="old_applicability" name="old_applicability">
                <input type="hidden" id="old_quantity" name="old_quantity">
                <input type="hidden" id="old_price" name="old_price">
                <input type="hidden" id="old_availability" name="old_availability">
                <input type="hidden" id="old_main_photo_url" name="old_main_photo_url">
                <input type="hidden" id="old_additional_photo_url_1" name="old_additional_photo_url_1">
                <input type="hidden" id="old_additional_photo_url_2" name="old_additional_photo_url_2">
                <input type="hidden" id="old_additional_photo_url_3" name="old_additional_photo_url_3">

                <div class="form-group mb-4">
                    <label for="art_number" class="block text-gray-700">Артикул</label>
                    <input type="text" class="form-control p-0 border rounded-md w-full" id="art_number" name="art_number">
                </div>

                <div class="form-group mb-4">
                    <label for="product_name" class="block text-gray-700">Название товара</label>
                    <input type="text" class="form-control p-0 border rounded-md w-full" id="product_name" name="product_name">
                </div>

                <div class="form-group mb-4">
                    <label for="number" class="block text-gray-700">Номер детали</label>
                    <input type="text" class="form-control p-0 border rounded-md w-full" id="number" name="number">
                </div>

                <div class="form-group mb-4">
                    <label for="new_used" class="block text-gray-700">Состояние</label>
                    <select class="form-control p-0 border rounded-md w-full" id="new_used" name="new_used">
                        <option value="new">Новый</option>
                        <option value="used">Б/У</option>
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label for="brand" class="block text-gray-700">Марка</label>
                    <select id="brand" name="brand" data-url="{{ route('get.models') }}" class="form-control p-0 border rounded-md w-full">
                        <option value="">Выберите марку</option>
                        @foreach(App\Models\BaseAvto::distinct()->pluck('brand') as $brand)
                            <option value="{{ $brand }}" {{ request()->get('brand') == $brand ? 'selected' : '' }}>
                                {{ $brand }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label for="model" class="block text-gray-700">Модель</label>
                    <select id="model" name="model" class="form-control p-0 border rounded-md w-full">
                        <option value="">Выберите модель</option>
                        @if(request()->get('brand')) 
                            @foreach(App\Models\BaseAvto::where('brand', request()->get('brand'))->distinct()->pluck('model') as $model)
                                <option value="{{ $model }}" {{ request()->get('model') == $model ? 'selected' : '' }}>
                                    {{ $model }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label for="year" class="block text-gray-700">Год выпуска</label>
                    <select id="year" name="year" class="form-control p-0 border rounded-md w-full">
                        <option value="">Выберите год выпуска</option>
                        @for($i = 2000; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ request()->get('year') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label for="body" class="block text-gray-700">Модель Кузова</label>
                    <input type="text" class="form-control p-0 border rounded-md w-full" id="body" name="body">
                </div>

                <div class="form-group mb-4">
                    <label for="engine" class="block text-gray-700">Модель Двигателя</label>
                    <input type="text" class="form-control p-0 border rounded-md w-full" id="engine" name="engine">
                </div>

                <div class="form-group mb-4">
                    <label for="L_R" class="block text-gray-700">Слева/Справа</label>
                    <select class="form-control p-0 border rounded-md w-full" id="L_R" name="L_R">
                        <option value="">Выберите расположение</option>
                        <option value="Слева">Слева (L)</option>
                        <option value="Справа">Справа (R)</option>
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label for="F_R" class="block text-gray-700">Спереди/Сзади</label>
                    <select class="form-control p-0 border rounded-md w-full" id="F_R" name="F_R">
                        <option value="">Выберите расположение</option>
                        <option value="Спереди">Спереди (F)</option>
                        <option value="Сзади">Сзади (R)</option>
                    </select>        
                </div>

                <div class="form-group mb-4">
                    <label for="U_D" class="block text-gray-700">Сверху/Снизу</label>
                    <select class="form-control p-0 border rounded-md w-full" id="U_D" name="U_D">
                        <option value="">Выберите расположение</option>
                        <option value="Сверху">Сверху (U)</option>
                        <option value="Снизу">Снизу (D)</option>
                    </select>         
                </div>

                <div class="form-group mb-4">
                    <label for="color" class="block text-gray-700">Цвет</label>
                    <input type="text" class="form-control p-0 border rounded-md w-full" id="color" name="color">
                </div>

                <div class="form-group mb-4">
                    <label for="applicability" class="block text-gray-700">Применимость</label>
                    <input type="text" class="form-control p-0 border rounded-md w-full" id="applicability" name="applicability">
                </div>

                <div class="form-group mb-4">
                    <label for="quantity" class="block text-gray-700">Количество</label>
                    <input type="number" class="form-control p-0 border rounded-md w-full" id="quantity" name="quantity" min="1">
                </div>

                <div class="form-group mb-4">
                    <label for="price" class="block text-gray-700">Цена</label>
                    <input type="text" class="form-control p-0 border rounded-md w-full" id="price" name="price" min="0" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>

                <div class="form-group mb-4">
                    <label for="availability" class="block text-gray-700">Наличие</label>
                    <select class="form-control p-0 border rounded-md w-full" id="availability" name="availability">
                        <option value="1">В наличии</option>
                        <option value="0">Нет в наличии</option>
                    </select>
                </div>

                <!-- Добавление полей для URL фотографий -->
                <div class="form-group mb-4">
                    <label for="main_photo_url" class="block text-gray-700">Основное фото (URL)</label>
                    <input type="text" class="form-control p-0 border rounded-md w-full" id="main_photo_url" name="main_photo_url">
                </div>

                <div class="form-group mb-4">
                    <label for="additional_photo_url_1" class="block text-gray-700">Дополнительное фото 1 (URL)</label>
                    <input type="text" class="form-control p-0 border rounded-md w-full" id="additional_photo_url_1" name="additional_photo_url_1">
                </div>

                <div class="form-group mb-4">
                    <label for="additional_photo_url_2" class="block text-gray-700">Дополнительное фото 2 (URL)</label>
                    <input type="text" class="form-control p-0 border rounded-md w-full" id="additional_photo_url_2" name="additional_photo_url_2">
                </div>

                <div class="form-group mb-4">
                    <label for="additional_photo_url_3" class="block text-gray-700">Дополнительное фото 3 (URL)</label>
                    <input type="text" class="form-control p-0 border rounded-md w-full" id="additional_photo_url_3" name="additional_photo_url_3">
                </div>

                <button type="submit" class="btn btn-primary p-0 bg-blue-500 text-white rounded-md">Сохранить</button>
            </form>
        </div>
    </div>

    <!-- Модальное окно для просмотра -->
    <div id="viewModal" class="modal fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="relative bg-white p-4 rounded-lg shadow-lg max-w-4xl w-full">
            <button class="absolute top-0 right-2 text-gray-500 hover:text-gray-700 close">
                <i class="fas fa-times text-2xl"></i>
            </button>
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/2">
                    <div class="relative">
                        <img id="modalMainImg" src="" alt="Основное фото" class="w-full h-64 object-contain rounded-lg cursor-pointer">
                    </div>
                </div>
                <div class="md:w-1/2 md:pl-4 mt-4 md:mt-0">
                    <p><strong>id товара:</strong> <span id="modalId"></span></p>
                    <p><strong>Наименование:</strong> <span id="modalProductName"></span></p>
                    <p><strong>Марка:</strong> <span id="modalBrand"></span></p>
                    <p><strong>Модель:</strong> <span id="modalModel"></span></p>
                    <p><strong>Кузов:</strong> <span id="modalBody"></span></p>
                    <p><strong>Двигатель:</strong> <span id="modalEngine"></span></p>
                    <p><strong>Номер:</strong> <span id="modalNumber"></span></p>
                    <p><strong>Цена:</strong> <span id="modalPrice"></span></p>
                </div>
            </div>
            <div class="flex mt-4 space-x-2" id="additionalImagesContainer"></div>
            <div class="flex justify-end mt-4">
                <button id="addToCartBtn" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Добавить в корзину</button>
                <p id="cartNotification" class="text-sm mt-2"></p>
            </div>
        </div>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', () => {
    const deleteMultipleBtn = document.getElementById('deleteMultipleBtn');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    const table = document.querySelector('.table');
    const thead = table.querySelector('thead');
    const tbody = table.querySelector('tbody');
    let isMultipleDeleteMode = false;

    // Обработчик клика на кнопку "Выбрать"
    deleteMultipleBtn.addEventListener('click', () => {
        if (!isMultipleDeleteMode) {
            console.log('Режим удаления нескольких товаров активирован');
            // Активируем режим выбора нескольких товаров
            isMultipleDeleteMode = true;

            // Активируем кнопку "Удалить выбранные"
            deleteSelectedBtn.disabled = false;
            deleteSelectedBtn.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
            deleteSelectedBtn.classList.add('bg-red-500', 'text-white');

            // Добавляем столбец с чекбоксами
            const checkAllCheckbox = document.createElement('input');
            checkAllCheckbox.type = 'checkbox';
            checkAllCheckbox.id = 'checkAll';
            checkAllCheckbox.addEventListener('change', (event) => {
                const checkboxes = document.querySelectorAll('.delete-checkbox');
                checkboxes.forEach(checkbox => checkbox.checked = event.target.checked);
            });

            const th = document.createElement('th');
            th.classList.add('bg-gray-200', 'border', 'p-0');
            th.appendChild(checkAllCheckbox);
            thead.querySelector('tr').insertBefore(th, thead.querySelector('tr').firstChild);

            tbody.querySelectorAll('tr').forEach(row => {
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.classList.add('delete-checkbox');
                checkbox.dataset.id = row.dataset.idInfo;

                const td = document.createElement('td');
                td.classList.add('border', 'p-0');
                td.appendChild(checkbox);
                row.insertBefore(td, row.firstChild);
            });
        } else {
            console.log('Режим удаления нескольких товаров деактивирован');
            // Деактивируем режим выбора нескольких товаров
            isMultipleDeleteMode = false;

            // Деактивируем кнопку "Удалить выбранные"
            deleteSelectedBtn.disabled = true;
            deleteSelectedBtn.classList.remove('bg-red-500', 'text-white');
            deleteSelectedBtn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');

            // Удаляем столбец с чекбоксами
            thead.querySelector('tr').removeChild(thead.querySelector('tr').firstChild);
            tbody.querySelectorAll('tr').forEach(row => {
                row.removeChild(row.firstChild);
            });
        }
    });

    // Функция для удаления выбранных объявлений
    function deleteSelectedAdverts() {
        const checkboxes = document.querySelectorAll('.delete-checkbox:checked');
        const ids = Array.from(checkboxes).map(checkbox => checkbox.dataset.id);

        if (ids.length > 0) {
            if (confirm('Вы уверены, что хотите удалить выбранные объявления?')) {
                console.log('Отправка запроса на удаление выбранных объявлений');
                // Отправляем запрос на удаление
                fetch('/adverts/delete-multiple', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ids })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Объявления успешно удалены');
                        location.reload();
                    } else {
                        console.error('Ошибка при удалении объявлений');
                        alert('Ошибка при удалении объявлений');
                    }
                });
            }
        } else {
            alert('Выберите хотя бы одно объявление для удаления');
        }
    }

    // Привязываем функцию удаления к кнопке "Удалить выбранные"
    deleteSelectedBtn.addEventListener('click', deleteSelectedAdverts);

    // Обработчик клика на строку
    tbody.querySelectorAll('tr').forEach(row => {
        row.addEventListener('click', (event) => {
            // Проверяем, что клик был на строке, а не на интерактивных элементах внутри строки
            if (!event.target.closest('.delete-checkbox') && !event.target.closest('.btn-danger') && !event.target.closest('.btn-primary')) {
                // Если режим удаления нескольких товаров не активен, открываем модальное окно
                if (!isMultipleDeleteMode) {
                    console.log('Открытие модального окна для строки с ID:', row.dataset.idInfo);
                    const modal = document.getElementById('viewModal');
                    modal.style.display = 'flex';
                    document.body.classList.add('modal-open');

                    // Заполняем данные в модальном окне
                    document.getElementById('modalId').textContent = row.dataset.idInfo;
                    document.getElementById('modalProductName').textContent = row.dataset.productName;
                    document.getElementById('modalBrand').textContent = row.dataset.brandInfo;
                    document.getElementById('modalModel').textContent = row.dataset.modelInfo;
                    document.getElementById('modalBody').textContent = row.dataset.bodyInfo;
                    document.getElementById('modalEngine').textContent = row.dataset.engineInfo;
                    document.getElementById('modalNumber').textContent = row.dataset.numberInfo;
                    document.getElementById('modalPrice').textContent = row.dataset.priceInfo;
                    document.getElementById('modalMainImg').src = row.dataset.mainPhotoUrl;

                    // Дополнительные фото
                    const additionalImagesContainer = document.getElementById('additionalImagesContainer');
                    additionalImagesContainer.innerHTML = ''; // Очищаем контейнер
                    for (let i = 1; i <= 3; i++) {
                        const photoUrl = row.dataset[`additionalPhotoUrl${i}`];
                        if (photoUrl) {
                            const img = document.createElement('img');
                            img.src = photoUrl;
                            img.classList.add('w-16', 'h-16', 'object-contain', 'rounded-lg', 'mr-2');
                            additionalImagesContainer.appendChild(img);
                        }
                    }
                } else {
                    console.log('Режим удаления нескольких товаров активен, модальное окно не открывается');
                }
            }
        });
    });

    // Закрытие модальных окон
    document.querySelectorAll('.close').forEach(button => {
        button.addEventListener('click', () => {
            console.log('Закрытие модального окна');
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => modal.style.display = 'none');
            document.body.classList.remove('modal-open');
        });
    });

    // Закрытие модальных окон при клике вне их
    window.addEventListener('click', (event) => {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                console.log('Закрытие модального окна при клике вне его');
                modal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        });
    });
});
    </script>
</body>
</html>