<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создать товар с помощью формы</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/search-form.js') }}" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
<body class=" flex flex-col justify-center items-center min-h-screen">
    @include('components.header-seller')
    <div class="flex flex-col items-center justify-center h-screen mb-52">
        <h1 class="text-2xl font-semibold mb-6">Как вы хотите добавить товары?</h1>
        <div class="flex justify-center space-x-4">
            <a href="#sel" class="bg-gray-200 text-gray-800 text-lg py-14 px-6 rounded-md">Создать товар с помощью формы</a>
            <a href="{{route('fromlist')}}#sel" class="bg-gray-200  text-lg text-gray-800  py-14 px-6  rounded-md">Загрузить товары из прайс-листа</a>        </div>
    </div>
    <!-- Основной контейнер с использованием Grid -->
    <div class="grid grid-cols-[1fr_2fr_1fr] gap-8 w-full max-w-6xl mb-20">
        
        <!-- Левая пустая колонка -->
        <div id="sel" class=" hidden md:block"></div>

        <!-- Центральная колонка (форма) -->
        <div id="create-form"  class="bg-white p-8 rounded-lg mt-20">
            <h1 class="text-center text-xl font-semibold mb-6">Создать товар с помощью формы</h1>

            @if ($errors->any())
                <div class="alert alert-danger bg-red-100 text-red-700 p-4 rounded-md mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success bg-green-100 text-green-700 p-4 rounded-md mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('adverts.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="art_number" class="block text-gray-700 font-medium mb-2">Артикул</label>
                    <input type="text" class="w-full px-3 py-2 border rounded-lg" id="art_number" name="art_number">
                </div>

                <div class="mb-4">
                    <label for="product_name" class="block text-gray-700 font-medium mb-2">Название товара <span class="text-red-500">*</span></label>
                    <input type="text" class="w-full px-3 py-2 border rounded-lg" id="product_name" name="product_name">
                </div>

                <div class="mb-4">
                    <label for="number" class="block text-gray-700 font-medium mb-2">Номер детали</label>
                    <input type="text" class="w-full px-3 py-2 border rounded-lg" id="number" name="number">
                </div>

                <div class="mb-4">
                    <label for="new_used" class="block text-gray-700 font-medium mb-2">Состояние</label>
                    <select class="w-full px-3 py-2 border rounded-lg" id="new_used" name="new_used">
                        <option value="new">Новый</option>
                        <option value="used">Б/У</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="brand" class="block text-gray-700 font-medium mb-2">Марка <span class="text-red-500">*</span></label>
                    <select id="brand" name="brand" data-url="{{ route('get.models') }}" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Выберите марку</option>
                        @foreach(App\Models\BaseAvto::distinct()->pluck('brand') as $brand)
                            <option value="{{ $brand }}" {{ request()->get('brand') == $brand ? 'selected' : '' }}>
                                {{ $brand }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="model" class="block text-gray-700 font-medium mb-2">Модель</label>
                    <select id="model" name="model" class="w-full px-3 py-2 border rounded-lg">
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

                <div class="mb-4">
                    <label for="year" class="block text-gray-700 font-medium mb-2">Год выпуска</label>
                    <select id="year" name="year" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Выберите год выпуска</option>
                        @for($i = 2000; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ request()->get('year') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="mb-4">
                    <label for="body" class="block text-gray-700 font-medium mb-2">Модель Кузова</label>
                    <input type="text" class="w-full px-3 py-2 border rounded-lg" id="body" name="body">
                </div>

                <div class="mb-4">
                    <label for="engine" class="block text-gray-700 font-medium mb-2">Модель Двигателя</label>
                    <input type="text" class="w-full px-3 py-2 border rounded-lg" id="engine" name="engine">
                </div>

                <div class="mb-4">
                    <label for="L_R" class="block text-gray-700 font-medium mb-2">Слева/Справа</label>
                    <select class="w-full px-3 py-2 border rounded-lg" id="L_R" name="L_R">
                        <option value="">Выберите расположение</option>
                        <option value="Слева">Слева (L)</option>
                        <option value="Справа">Справа (R)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="F_R" class="block text-gray-700 font-medium mb-2">Спереди/Сзади</label>
                    <select class="w-full px-3 py-2 border rounded-lg" id="F_R" name="F_R">
                        <option value="">Выберите расположение</option>
                        <option value="Спереди">Спереди (F)</option>
                        <option value="Сзади">Сзади (R)</option>
                    </select>        
                </div>

                <div class="mb-4">
                    <label for="U_D" class="block text-gray-700 font-medium mb-2">Сверху/Снизу</label>
                    <select class="w-full px-3 py-2 border rounded-lg" id="U_D" name="U_D">
                        <option value="">Выберите расположение</option>
                        <option value="Сверху">Сверху (U)</option>
                        <option value="Снизу">Снизу (D)</option>
                    </select>         
                </div>

                <div class="mb-4">
                    <label for="color" class="block text-gray-700 font-medium mb-2">Цвет</label>
                    <input type="text" class="w-full px-3 py-2 border rounded-lg" id="color" name="color">
                </div>

                <div class="mb-4">
                    <label for="applicability" class="block text-gray-700 font-medium mb-2">Применимость</label>
                    <input type="text" class="w-full px-3 py-2 border rounded-lg" id="applicability" name="applicability">
                </div>

                <div class="mb-4">
                    <label for="quantity" class="block text-gray-700 font-medium mb-2">Количество</label>
                    <input type="number" class="w-full px-3 py-2 border rounded-lg" id="quantity" name="quantity" min="1">
                </div>

                <div class="mb-4">
                    <label for="price" class="block text-gray-700 font-medium mb-2">Цена</label>
                    <div class="flex">
                        <input type="text" class="w-full px-3 py-2 border rounded-l-lg" id="price" name="price" min="0" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <span class="inline-flex items-center px-3 border-t border-b border-r border-gray-300 rounded-r-lg bg-gray-100">₽</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="availability" class="block text-gray-700 font-medium mb-2">Наличие</label>
                    <select class="w-full px-3 py-2 border rounded-lg" id="availability" name="availability">
                        <option value="1">В наличии</option>
                        <option value="0">Нет в наличии</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="main_photo_url" class="block text-gray-700 font-medium mb-2">Основное фото (URL)</label>
                    <input type="text" class="w-full px-3 py-2 border rounded-lg" id="main_photo_url" name="main_photo_url">
                </div>

                <div class="mb-4">
                    <label for="additional_photo_url_1" class="block text-gray-700 font-medium mb-2">Дополнительное фото 1 (URL)</label>
                    <input type="text" class="w-full px-3 py-2 border rounded-lg" id="additional_photo_url_1" name="additional_photo_url_1">
                </div>

                <div class="mb-4">
                    <label for="additional_photo_url_2" class="block text-gray-700 font-medium mb-2">Дополнительное фото 2 (URL)</label>
                    <input type="text" class="w-full px-3 py-2 border rounded-lg" id="additional_photo_url_2" name="additional_photo_url_2">
                </div>

                <div class="mb-4">
                    <label for="additional_photo_url_3" class="block text-gray-700 font-medium mb-2">Дополнительное фото 3 (URL)</label>
                    <input type="text" class="w-full px-3 py-2 border rounded-lg" id="additional_photo_url_3" name="additional_photo_url_3">
                </div>

                <div class="text-center">
                    <button type="submit" class="bg-blue-500 text-white font-medium py-2 px-4 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Добавить товар</button>
                </div>
            </form>
        </div>

        <!-- Правая колонка (ссылка на импорт товаров) -->
        <div class="bg-white p-4 rounded-lg shadow-md self-start">
            <ul class="text-sm text-gray-600">
                <li class="mb-2"><a href="{{route('fromlist')}}" class="block hover:underline">Импортировать товары</a></li>
            </ul>
        </div>
    </div>

    <script>
        function scrollToForm() {
            document.getElementById('create-product-form').scrollIntoView({ behavior: 'smooth' });
        }

        function scrollToForm2() {
            document.getElementById('import-product-form').scrollIntoView({ behavior: 'smooth' });
        }

        function showText(text) {
            document.getElementById('hoverText').textContent = text;
            document.getElementById('hoverText').style.display = 'block';
        }

        function hideText() {
            document.getElementById('hoverText').style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const convertButton = document.getElementById('convert-button');

            if (convertButton) {
                convertButton.addEventListener('click', function() {
                    const form = document.getElementById('convert-form');
                    const formData = new FormData(form);

                    fetch('{{ route('convert.price.list') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(columns => {
                        const columnsContainer = document.getElementById('columns-container');
                        columnsContainer.innerHTML = '';

                        const columnNamesDiv = document.createElement('div');
                        columnNamesDiv.className = 'col-span-1';

                        const h2Columns = document.createElement('h2');
                        h2Columns.textContent = 'Найденные столбцы в вашем файле';
                        h2Columns.className = 'text-xl font-bold mb-4'; 
                        columnsContainer.appendChild(h2Columns);

                        const h2Data = document.createElement('h2');
                        h2Data.textContent = 'Данные которые содержит столбец';
                        h2Data.className = 'text-xl font-bold mb-4'; 
                        columnsContainer.appendChild(h2Data);

                        const selectDiv = document.createElement('div');
                        selectDiv.className = 'col-span-1';

                        columns.forEach((column, index) => {
                            const labelDiv = document.createElement('div');
                            labelDiv.className = 'border border-gray-300 h-10 mb-4';

                            const label = document.createElement('label');
                            label.className = 'block text-gray-700';
                            label.textContent = column;

                            const select = document.createElement('select');
                            select.className = 'form-control border h-10 w-full';
                            select.name = column;

                            const options = ['Выберите поле', 'Артикул', 'Название товара', 'Состояние', 'Марка', 'Модель', 'Кузов', 'Номер запчасти', 'Номер двигателя', 'Год', 'Расположение Л_П', 'Расположение Сп_Сз', 'Расположение Св_Сн', 'Цвет', 'Расположение Л_П', 'Описание', 'Количество', 'Цена', 'Доступность', 'Время доставки', 'Главное фото', 'Фото1', 'Фото2', 'Фото3', 'Доступность' ];
                            options.forEach(option => {
                                const optionElement = document.createElement('option');
                                optionElement.value = option;
                                optionElement.textContent = option;
                                select.appendChild(optionElement);
                            });

                            labelDiv.appendChild(label);
                            columnNamesDiv.appendChild(labelDiv);

                            const selectDivWrapper = document.createElement('div');
                            selectDivWrapper.className = 'mb-4';
                            selectDivWrapper.appendChild(select);

                            selectDiv.appendChild(selectDivWrapper);
                        });

                        columnsContainer.appendChild(columnNamesDiv);
                        columnsContainer.appendChild(selectDiv);

                        document.getElementById('columns-form').style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                });
            } else {
                console.error('Element with id "convert-button" not found');
            }
        });
    </script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Проверяем, есть ли в URL якорь #create-form
        if (window.location.hash === '#create-form') {
            // Плавный скролл к элементу с id="create-form"
            document.getElementById('create-form').scrollIntoView({ behavior: 'smooth' });
        }
    });
</script>
</body>
</html>