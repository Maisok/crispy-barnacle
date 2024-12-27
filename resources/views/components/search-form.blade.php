<style>
    #main-form {
        border: 0.5px solid #ccc;
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.3);
    }

    /* Стили для выпадающих списков */
    #brand-dropdown,
    #model-dropdown,
    #year-dropdown {
        display: none;
        position: absolute;
        top: 100%; /* Позиционируем под инпутом */
        left: 0;
        background-color: #ffffff;
        width: 100%;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        max-height: 150px;
        overflow-y: auto;
        z-index: 10;
    }

    #brand-dropdown div,
    #model-dropdown div,
    #year-dropdown div {
        padding: 10px 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 14px;
        color: #4a5568;
    }

    #brand-dropdown div:hover,
    #model-dropdown div:hover,
    #year-dropdown div:hover {
        background-color: #f3f4f6;
    }

    #brand-dropdown.show,
    #model-dropdown.show,
    #year-dropdown.show {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>

<div class="ad-list">
    <form id="main-form" action="{{ route('adverts.search') }}" method="GET" class="flex flex-wrap gap-4 items-center" data-brands-url="{{ route('get.brands') }}">
        <input type="hidden" name="city" value="{{ request()->get('city') }}">

        <!-- Search Input -->
        <input
            type="text"
            name="search_query"
            id="search_query"
            placeholder="Введите название или номер детали"
            value="{{ request()->get('search_query') }}"
            class="w-full md:w-auto flex-grow px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-500 text-gray-500 md:py-1 md:text-sm"
        />

        <!-- Brand Input with Autocomplete -->
        <div class="relative w-full md:w-auto flex items-center">
            <input
                type="text"
                id="brand-input"
                name="brand_input"
                placeholder="Введите марку"
                class="w-full md:w-auto flex-grow px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-500 text-gray-500 md:py-1 md:text-sm"
            />
            <input type="hidden" id="brand" name="brand" value="{{ request()->get('brand') }}">
            <button type="button" id="brand-dropdown-button" class="absolute right-0 px-2 py-2 text-gray-500 focus:outline-none">
                <i class="fas fa-chevron-down"></i>
            </button>
            <div id="brand-dropdown" class="hidden"></div>
        </div>

        <!-- Model Input with Autocomplete -->
        <div class="relative w-full md:w-auto flex items-center">
            <input
                type="text"
                id="model-input"
                name="model_input"
                placeholder="Введите модель"
                class="w-full md:w-auto flex-grow px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-500 text-gray-500 md:py-1 md:text-sm"
            />
            <input type="hidden" id="model" name="model" value="{{ request()->get('model') }}">
            <button type="button" id="model-dropdown-button" class="absolute right-0 px-2 py-2 text-gray-500 focus:outline-none">
                <i class="fas fa-chevron-down"></i>
            </button>
            <div id="model-dropdown" class="hidden"></div>
        </div>

        <!-- Year Select -->
        <div class="relative w-full md:w-auto flex items-center">
            <input
                type="text"
                id="year-input"
                name="year_input"
                placeholder="Выберите год выпуска"
                class="w-full md:w-auto flex-grow px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-blue-500 text-gray-500 md:py-1 md:text-sm"
                readonly
            />
            <input type="hidden" id="year" name="year" value="{{ request()->get('year') }}">
            <button type="button" id="year-dropdown-button" class="absolute right-0 px-2 py-2 text-gray-500 focus:outline-none">
                <i class="fas fa-chevron-down"></i>
            </button>
            <div id="year-dropdown" class="hidden"></div>
        </div>

        <!-- Show Button -->
        <button
            type="button"
            id="show-button"
            class="w-full md:w-auto px-4 py-2 bg-blue-500 text-white font-semibold rounded focus:outline-none focus:ring focus:ring-blue-500 md:py-1 md:text-sm"
        >
            Показать
        </button>
    </form>

    
</div>

<!-- Import jQuery and Other JavaScript Libraries -->
<script src="{{ asset('js/search-form.js') }}" defer></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script>
$(document).ready(function() {
    // Настройка автодополнения для поля поиска запчастей
    $('#search_query').autocomplete({
        source: function(request, response) {
            var term = request.term.trim();
            $.ajax({
                url: '{{ route('get.parts') }}',
                type: 'GET',
                data: { term: term },
                success: function(data) {
                    if (term === "") {
                        response(data); // Показываем весь список, если поле пустое
                    } else {
                        response($.ui.autocomplete.filter(data, term)); // Фильтруем список по введенному значению
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
                }
            });
        }
    });


    // Настройка автодополнения для модели
    $('#model-input').autocomplete({
        source: function(request, response) {
            var brand = $('#brand').val();
            var modelTerm = request.term.trim();
            $.ajax({
                url: '{{ route('get.models') }}',
                type: 'GET',
                data: { term: modelTerm, brand: brand },
                success: function(data) {
                    if (modelTerm === "") {
                        response(data); // Показываем весь список, если поле пустое
                    } else {
                        response($.ui.autocomplete.filter(data, modelTerm)); // Фильтруем список по введенному значению
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
                }
            });
        },
        select: function(event, ui) {
            $('#model').val(ui.item.value); // Устанавливаем значение в скрытое поле
        }
    });

    // Обработчик для кнопки "Показать"
    $('#show-button').on('click', function() {
        var formData = $('#main-form').serialize();
        window.location.href = '{{ route('adverts.search') }}?' + formData;
    });

    function updateModels(brand) {
        $('#model-input').val(''); // Очищаем поле модели
        $('#model').val(''); // Очищаем скрытое поле модели
        $('#model-input').autocomplete("search", ""); // Сбрасываем автодополнение для модели
    }

    // Обработчик для изменения марки
    $('#brand-input').on('change', function() {
        var brand = $(this).val();
        $('#brand').val(brand); // Устанавливаем значение в скрытое поле
        updateModels(brand);
    });

    // Обработчик для изменения модели
    $('#model-input').on('input', function() {
        var model = $(this).val();
        $('#model').val(model); // Устанавливаем значение в скрытое поле
        $('#model-input').autocomplete("search", model); // Обновляем автодополнение для модели
    });

    // Обработчик для кнопки выпадающего списка марок
    $('#brand-dropdown-button').on('click', function() {
    if ($('#brand-dropdown').hasClass('hidden')) {
        $('#brand-dropdown').removeClass('hidden').addClass('show');
        $.ajax({
            url: '{{ route('get.brands') }}',
            type: 'GET',
            success: function(data) {
                $('#brand-dropdown').empty();

                // Фильтруем марки с объявлениями (advert_count > 0)
                const brandsWithAds = data.filter(brand => brand.advert_count > 0);
                // Остальные марки (advert_count === 0)
                const brandsWithoutAds = data.filter(brand => brand.advert_count === 0);

                // Сортируем марки с объявлениями по количеству объявлений (по убыванию)
                brandsWithAds.sort((a, b) => b.advert_count - a.advert_count);

                // Добавляем марки с объявлениями (жирным шрифтом)
                brandsWithAds.forEach(brand => {
                    $('#brand-dropdown').append('<div class="px-3 py-2 cursor-pointer hover:bg-gray-100 font-bold" data-value="' + brand.brand + '">' + brand.brand + ' <span class="text-black-500">(' + brand.advert_count + ')</span></div>');
                });

                // Если есть марки без объявлений, добавляем разделитель и сортируем их по алфавиту
                if (brandsWithoutAds.length > 0) {
                    $('#brand-dropdown').append('<div class="border-t border-gray-200 my-1"></div>'); // Горизонтальная линия
                    brandsWithoutAds.sort((a, b) => a.brand.localeCompare(b.brand)); // Сортировка по алфавиту
                    brandsWithoutAds.forEach(brand => {
                        $('#brand-dropdown').append('<div class="px-3 py-2 cursor-pointer hover:bg-gray-100" data-value="' + brand.brand + '">' + brand.brand + ' <span class="text-gray-500">(' + brand.advert_count + ')</span></div>');
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", status, error);
            }
        });
    } else {
        $('#brand-dropdown').removeClass('show').addClass('hidden');
    }
});

    // Обработчик для выбора марки из выпадающего списка
    $('#brand-dropdown').on('click', 'div', function() {
        var brand = $(this).data('value');
        $('#brand-input').val(brand);
        $('#brand').val(brand);
        $('#brand-dropdown').removeClass('show').addClass('hidden');
        updateModels(brand);
    });

    // Обработчик для кнопки выпадающего списка моделей
    $('#model-dropdown-button').on('click', function() {
    if ($('#model-dropdown').hasClass('hidden')) {
        $('#model-dropdown').removeClass('hidden').addClass('show');
        var brand = $('#brand').val();
        if (brand) {
            $.ajax({
                url: '{{ route('get.models') }}',
                type: 'GET',
                data: { brand: brand },
                success: function(data) {
                    $('#model-dropdown').empty();

                    // Фильтруем модели с объявлениями (advert_count > 0)
                    const modelsWithAds = data.filter(model => model.advert_count > 0);
                    // Остальные модели (advert_count === 0)
                    const modelsWithoutAds = data.filter(model => model.advert_count === 0);

                    // Сортируем модели с объявлениями по количеству объявлений (по убыванию)
                    modelsWithAds.sort((a, b) => b.advert_count - a.advert_count);

                    // Добавляем модели с объявлениями (жирным шрифтом)
                    modelsWithAds.forEach(model => {
                        $('#model-dropdown').append('<div class="px-3 py-2 cursor-pointer hover:bg-gray-100 font-bold" data-value="' + model.model + '">' + model.model + ' <span class="text-gray-500">(' + model.advert_count + ')</span></div>');
                    });

                    // Если есть модели без объявлений, добавляем разделитель и сортируем их по алфавиту
                    if (modelsWithoutAds.length > 0) {
                        $('#model-dropdown').append('<div class="border-t border-gray-200 my-1"></div>'); // Горизонтальная линия
                        modelsWithoutAds.sort((a, b) => a.model.localeCompare(b.model)); // Сортировка по алфавиту
                        modelsWithoutAds.forEach(model => {
                            $('#model-dropdown').append('<div class="px-3 py-2 cursor-pointer hover:bg-gray-100" data-value="' + model.model + '">' + model.model + ' <span class="text-gray-500">(' + model.advert_count + ')</span></div>');
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
                }
            });
        }
    } else {
        $('#model-dropdown').removeClass('show').addClass('hidden');
    }
});
    // Обработчик для выбора модели из выпадающего списка
    $('#model-dropdown').on('click', 'div', function() {
        var model = $(this).data('value');
        $('#model-input').val(model);
        $('#model').val(model);
        $('#model-dropdown').removeClass('show').addClass('hidden');
    });

    // Обработчик для кнопки выпадающего списка годов
    $('#year-dropdown-button').on('click', function() {
        if ($('#year-dropdown').hasClass('hidden')) {
            $('#year-dropdown').removeClass('hidden').addClass('show');

            // Получаем выбранную марку и модель
            var brand = $('#brand').val();
            var model = $('#model').val();

            // Проверяем, что марка и модель выбраны
            if (brand && model) {
                // Отправляем AJAX-запрос для получения списка годов
                $.ajax({
                    url: '{{ route('get.years') }}', // Маршрут для получения годов
                    type: 'GET',
                    data: { brand: brand, model: model }, // Передаем марку и модель
                    success: function(data) {
                        // Очищаем список годов
                        $('#year-dropdown').empty();

                        // Добавляем годы в выпадающий список
                        $.each(data, function(index, year) {
                            $('#year-dropdown').append('<div class="px-3 py-2 cursor-pointer hover:bg-gray-100" data-value="' + year + '">' + year + '</div>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: ", status, error);
                    }
                });
            } else {
                // Если марка или модель не выбраны, показываем сообщение
                $('#year-dropdown').empty().append('<div class="px-3 py-2">Выберите марку и модель</div>');
            }
        } else {
            // Скрываем выпадающий список
            $('#year-dropdown').removeClass('show').addClass('hidden');
        }
    });

    // Обработчик для выбора года из выпадающего списка
    $('#year-dropdown').on('click', 'div', function() {
        var year = $(this).data('value');
        $('#year-input').val(year); // Устанавливаем значение в поле ввода
        $('#year').val(year); // Устанавливаем значение в скрытое поле
        $('#year-dropdown').removeClass('show').addClass('hidden'); // Скрываем список

        // После выбора года, запрашиваем модификации
        updateModifications();
    });

    // Функция для обновления модификаций
    function updateModifications() {
        var brand = $('#brand').val();
        var model = $('#model').val();
        var year = $('#year').val();

        // Скрываем placeholder и показываем блок модификаций
        $('#modifications-placeholder').hide();
        $('#modifications').show();

        if (brand && model && year) {
            $.ajax({
                url: '/get-modifications', // Создайте маршрут для получения модификаций
                type: 'GET',
                data: { brand: brand, model: model, year: year },
                success: function(data) {
                    // Очищаем блок модификаций
                    $('#modifications').empty();

                    // Добавляем модификации в блок
                    if (data.length > 0) {
                        $.each(data, function(index, modification) {
                            $('#modifications').append('<label class="flex items-center space-x-2 mb-2"><input type="checkbox" class="modification-checkbox" value="' + modification.id_modification + '" checked><span class="text-gray-700">' + modification.modification + '</span></label>');
                        });
                    } else {
                        // Если модификаций нет, показываем сообщение
                        $('#modifications').append('<div class="text-gray-500">Нет доступных модификаций</div>');
                    }

                    // Сохранение состояния чекбоксов в куки при изменении
                    $('.modification-checkbox').change(function() {
                        saveSelectedModifications();
                    });

                    // Сохранение состояния в куки по умолчанию
                    saveSelectedModifications();
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
                }
            });
        } else {
            // Если параметры не выбраны, скрываем блок модификаций и показываем placeholder
            $('#modifications').hide();
            $('#modifications-placeholder').show();
        }
    }

    // Функция для сохранения выбранных модификаций в куки
    function saveSelectedModifications() {
        var selectedModifications = [];
        $('.modification-checkbox:checked').each(function() {
            var modificationId = $(this).val();
            var modificationText = $(this).parent().text().trim(); // Получаем текст модификации
            selectedModifications.push({
                id_modification: modificationId,
                modification: modificationText
            });
        });
        Cookies.set('selectedModifications', JSON.stringify(selectedModifications), { expires: 7 }); // Сохраняем на 7 дней
    }
});
</script>