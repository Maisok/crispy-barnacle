<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вы посмотрели</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="shortcut icon" href="{{ asset('images/Group 438.png') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
</head>
<style>
    .advert-block {
        border: 0.2px solid #ccc;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 15px;
        transition: background-color 0.3s ease;
    }

    .advert-block:hover {
        background-color: #f0f8ff;
    }

    body {
    font-family: 'Nunito', sans-serif;
}
</style>
<body class="font-sans flex flex-col items-center">

@include('components.header-seller')

<div class="w-full md:w-[90%] mx-auto mt-10">
    <h1 class="text-2xl font-bold mt-8 mb-4 text-center md:text-left">Вы посмотрели</h1>

    @if($adverts->isEmpty())
        <p class="text-center text-lg mt-8">Нет просмотренных товаров.</p>
    @else
        <!-- Блоки товаров -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($adverts->reverse() as $advert)
                <div class="advert-block bg-white rounded-lg shadow p-4 cursor-pointer" onclick="location.href='{{ route('adverts.show', $advert->id) }}'">
                    <div class="advert-details">
                        <div class="text-lg font-bold">
                            {{ $advert->product_name }}
                        </div>
                        <div class="text-xl text-black font-semibold mt-2">
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
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

</body>
</html>