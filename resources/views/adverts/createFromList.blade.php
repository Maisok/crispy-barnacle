<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Загрузить товары</title>
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
<body>
    @include('components.header-seller')
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js"></script>
    
    <div class="container mx-auto p-4 mt-20 mb-20">
        <div class="flex flex-col items-center justify-center h-screen">
            <h1 class="text-2xl font-semibold mb-6">Как вы хотите добавить товары?</h1>
            <div class="flex justify-center space-x-4">
                <a href="{{route('adverts.create')}}#create-form" class="bg-gray-200 text-lg text-gray-800  py-12 px-5  rounded-md">Создать товар с помощью формы</a>                <a href="#sel" class="bg-gray-200 text-gray-800  py-12 px-5 text-lg rounded-md">Загрузить товары из прайс-листа</a>
            </div>
        </div>
    
        <div class="mt-16">
            <h2 id="sel" class="text-xl font-semibold text-center mb-6">Выберите способ загрузки товаров из прайс-листа</h2>
            <div class="space-y-8">
                <div class="border p-4 rounded-md">
                    <div class="flex items-center mb-4">
                        <div class="bg-gray-200 text-gray-800 rounded-full h-8 w-8 flex items-center justify-center mr-4">1</div>
                        <h3 class="text-lg font-semibold">Прямая загрузка товаров из прайс-листа на сайт</h3>
                    </div>
                    <div class="bg-orange-100 p-4 rounded-md mb-4 flex justify-between items-center">
                        <p>Выберите этот способ загрузки товаров, если ваш прайс-лист соответствует <a href="#" class="text-blue-600">принятому формату</a>.</p>
                        <i class="fas fa-times text-gray-500"></i>
                    </div>
                    <div class="flex items-center mb-4">
                        <label class="mr-4">Выберите файл:</label>
                        <input type="file" class="border rounded-md p-2">
                    </div>
                    <div class="flex justify-between items-center mb-4 md:flex-row flex-col">
                        <button class="bg-blue-600 text-white py-2 px-4 rounded-md md:w-auto w-full mb-4 md:mb-0">Добавить товары на сайт</button>
                        <div class="text-right w-full md:w-auto">
                            <p><a href="#" class="text-blue-600">Требования к файлу для прямого импорта</a></p>
                            <p><a href="#" class="text-blue-600">Инструкция по загрузке товаров из файла</a></p>
                            <p><a href="#" class="text-blue-600">Видеоинструкция</a></p>
                        </div>
                    </div>
                </div>
    
                <div class="text-center text-gray-500 text-lg font-semibold">ИЛИ</div>
    
                <div class="border p-4 rounded-md">
                    <div class="flex items-center mb-4">
                        <div class="bg-gray-200 text-gray-800 rounded-full h-8 w-8 flex items-center justify-center mr-4">2</div>
                        <h3 class="text-lg font-semibold">Конвертировать прайс-лист и загрузить товары на сайт</h3>
                    </div>
                    <div class="bg-orange-100 p-4 rounded-md mb-4 flex justify-between items-center">
                        <p>Если Ваш прайс-лист отличается от <a href="#" class="text-blue-600">принятого формата для прямого импорта</a> выберите этот способ. Конвертация позволяет загрузить товары на сайт из прайс-листа любого вида.</p>
                        <i class="fas fa-times text-gray-500"></i>
                    </div>
                  <form id="convert-form" action="" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group mb-4">
           
            <label for="file" class="block text-gray-700">Выберите файл для конвертации</label>
         
            <input type="file" name="file" id="file" required class="form-control border p-2 w-full">
        </div>

        <button type="button" id="convert-button" class="btn btn-primary bg-blue-500 text-white px-4 py-2 rounded-md">Открыть файл</button>
    </form>
<!-- Форма для выбора столбцов -->
<div id="columns-form" class="container mx-auto p-4 mt-20" style="display: none;">
    <form id="import-columns-form" action="{{ route('cars.import') }}" method="POST">
        @csrf
        <div class="grid grid-cols-2 gap-4" id="columns-container">   
           
            <h2>Найденные столбцы в вашем файле</h2>
            <h2>Данные которые содержит столбец</h2>
    </div>
    <button type="submit" class="btn btn-primary bg-blue-500 text-white px-4 py-2 rounded-md">Конвертировать файл</button>
    </form>
</div>


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

                    <div class="flex justify-between items-center mb-4 md:flex-row flex-col">
                     
                        <div class="text-right w-full md:w-auto">
                            <p><a href="#" class="text-blue-600">О конвертере прайс-листов</a></p>
                            <p><a href="#" class="text-blue-600">Первая конвертация файла</a></p>
                            <p><a href="#" class="text-blue-600">Видеоинструкция</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/search-form.js') }}" defer></script>
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

                        const options = {
                            'Выберите поле': 'none',
                            'Артикул': 'art_number',
                            'Название товара': 'product_name',
                            'Состояние': 'new_used',
                            'Марка': 'brand',
                            'Модель': 'model',
                            'Кузов': 'body',
                            'Номер запчасти': 'number',
                            'Номер двигателя': 'engine',
                            'Год': 'year',
                            'Расположение Л_П': 'L_R',
                            'Расположение Сп_Сз': 'F_R',
                            'Расположение Св_Сн': 'U_D',
                            'Цвет': 'color',
                            'Применимость': 'applicability',
                            'Количество': 'quantity',
                            'Цена': 'price',
                            'Доступность': 'availability',
                            'Время доставки': 'delivery_time',
                            'Главное фото': 'main_photo_url',
                            'Фото1': 'additional_photo_url_1',
                            'Фото2': 'additional_photo_url_2',
                            'Фото3': 'additional_photo_url_3'
                        };
                        for (let key in options){
                            const optionElement = document.createElement('option');
                            optionElement.textContent = key;
                            optionElement.setAttribute("value", options[key])
                            select.appendChild(optionElement);
                        }
                        
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

        // Обработчик события submit для формы импорта
        const importColumnsForm = document.getElementById('import-columns-form');
        if (importColumnsForm) {
            importColumnsForm.addEventListener('submit', function(event) {
                event.preventDefault();
                
                const html_columns = importColumnsForm.querySelectorAll('.col-span-1');
                const column_names = html_columns[0];
                const column_name_options = html_columns[1];

                let columns_dict = {};
                for (let i = 0; i < column_names.childElementCount; i++) {
                    const name_from_file = column_names.children[i].textContent;
                    const name_from_db = column_name_options.children[i].firstChild.value;
                    columns_dict[name_from_db] = name_from_file;
                }

              // Получаем настройки пользователя
                fetch("{{ route('get.settings') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(settings => {
                    console.log("settings", settings)
                  const userId = {{ auth()->id() }}; // Используем ID авторизованного пользователя
                    const columns_str = JSON.stringify(columns_dict);
                    const skipRows = 0;
                    const csv_encoding = "auto";
                   const csv_delimiter = ",";
                    const addSheetNameToProductName = true;
                    const extractDataFromProductName = true;
                    const skip_empty_price_rows = true;
                    const deactivate_old_ad = true;
                  const split_symbols = [",", "/", " ", ".", "(", ")", "\\", '"'];
                    const selected_brands = JSON.stringify(settings.settings); // Массив выбранных брендов

                    console.log("log", userId, columns_str)


                    const formData = new FormData();
                    formData.append("file", document.getElementById('file').files[0]);

                    const url = new URL('http://45.153.190.28:8002/upload');
                    url.searchParams.append("user_id", userId);
                    url.searchParams.append("columns", columns_str);
                    url.searchParams.append("skip_rows", skipRows);
                    url.searchParams.append("csv_encoding", csv_encoding);
                    url.searchParams.append("csv_delimiter", csv_delimiter);
                    url.searchParams.append("add_sheet_name_to_product_name", addSheetNameToProductName);
                    url.searchParams.append("extract_data_from_product_name", extractDataFromProductName);
                    url.searchParams.append("skip_empty_price_rows", skip_empty_price_rows);
                    url.searchParams.append("deactivate_old_ad", deactivate_old_ad);
                    url.searchParams.append("selected_brands", selected_brands); // Добавляем выбранные бренды
                    url.searchParams.append("split_symbols", split_symbols);

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                    })
                    .then((response) => {
                        console.log("status", response.status);
                        return response.json();
                    })
                    .then((result) => {
                        console.log("Response", result);
                        alert("Добавлено в очередь на обработку. Ваша позиция в очереди: "+result["queue_position"]);
                        task_id = result["task_id"]
                        setTimeout(()=>{       
                            fetch("http://45.153.190.28:8002/get_status?task_id="+task_id, {
                                method: 'GET'
                            })
                            .then(response => response.json())
                            .then(data => {
                                alert("Статус вашего запроса: "+data["status"])
                                console.log(data)
                            })
                        }, 1000)
                    })
                    .catch((error) => console.error(error));
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        } else {
            console.error('Element with id "import-columns-form" not found');
        }
    });


    document.addEventListener('DOMContentLoaded', function() {
        // Проверяем, есть ли в URL якорь #sel
        if (window.location.hash === '#sel') {
            // Плавный скролл к элементу с id="sel"
            document.getElementById('sel').scrollIntoView({ behavior: 'smooth' });
        }
    });

</script>