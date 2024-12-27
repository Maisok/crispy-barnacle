<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки конвертера</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="shortcut icon" href="{{asset('images/Group 438.png')}}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

    <style>
        .icon-text-hover:hover i,
        .icon-text-hover:hover p {
            color: #0077FF;
        }
    </style>

<style>
    body {
    font-family: 'Nunito', sans-serif;
}
</style>
</head>
<body>
@include('components.header-seller')

<div class="mx-auto max-w-5xl flex flex-col items-center justify-center p-4 mt-28 mb-20">
    <h4 class="text-xl font-bold mb-4">Настройки конвертера</h4>


    <h4 class="text-xl font-bold mb-4 self-start">Соответствие столбцов</h4>
    <form action="{{ route('converter_set.reset') }}" method="POST" class="self-start" onsubmit="return confirmReset();">
        @csrf
        <button type="submit" class="btn btn-primary bg-gray-600 text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Сбросить выбор столбцов</button>
    </form> 

    @if(session('success'))
        <div class="alert alert-success bg-green-100 text-green-700 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h5 class="text-lg font-bold mb-4">Выберите марки автомобилей которые есть в Вашем прайс-листе</h5>

    <form action="{{ route('converter_set.update') }}" method="POST" class="space-y-10 flex flex-col">
        @csrf
        @method('PUT')

        <div class=" grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 ">
            @foreach ([
                'acura',
                'alfa_romeo',
                'asia',
                'aston_martin',
                'audi',
                'bentley',
                'bmw',
                'byd',
                'cadillac',
                'changan',
                'chevrolet',
                'citroen',
                'daewoo',
                'daihatsu',
                'datsun',
                'fiat',
                'ford',
                'gaz',
                'geely',
                'haval',
                'honda',
                'hyundai',
                'infiniti',
                'isuzu',
                'jaguar',
                'jeep',
                'kia',
                'lada',
                'land_rover',
                'mazda',
                'mercedes_benz',
                'mitsubishi',
                'nissan',
                'opel',
                'peugeot',
                'peugeot_lnonum',
                'porsche',
                'renault',
                'skoda',
                'ssangyong',
                'subaru', 
                'suzuki', 
                'toyota', 
                'uaz', 
                'volkswagen', 
                'volvo', 
                'zaz'
            ] as $brand)
                <div class="flex items-center space-x-2">
                    <input type="hidden" name="{{ $brand }}" value="0">
                    <input class="form-check-input" type="checkbox" name="{{ $brand }}" id="{{ $brand }}" value="1" {{ isset($converterSet) && $converterSet->$brand ? 'checked' : '' }}>
                    <label class="form-check-label" for="{{ $brand }}">{{ ucfirst(str_replace('_', ' ', $brand)) }}</label>
                </div>
            @endforeach
        </div>

      

        <h5 class="text-lg font-bold mb-4">Параметры файла</h5>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      
            <div class="form-group">
                <label for="del_duplicate" class="block text-sm font-medium text-gray-700">Удалить дубликаты</label>
                <input type="text" class="form-control mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" name="del_duplicate" id="del_duplicate" value="{{ old('del_duplicate', $converterSet->del_duplicate ?? '') }}">
            </div>

        </div>

        <button type="submit" class="self-center btn btn-primary bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Сохранить настройки</button>
    </form>
</div>
<script>
    function confirmReset() {
        // Показываем диалоговое окно подтверждения
        const confirmResult = confirm("Вы уверены, что хотите сбросить все настройки? Это действие нельзя отменить.");
        
        // Если пользователь нажал "Отмена", возвращаем false, чтобы отменить отправку формы
        if (!confirmResult) {
            return false;
        }

        // Если пользователь подтвердил, форма отправится
        return true;
    }
</script>
</body>
</html>