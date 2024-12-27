<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Избранное</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="shortcut icon" href="{{ asset('images/Group 438.png') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
</head>
<style>
    .blockadvert {
        border: 0.2px solid #ccc;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 15px;
    }


    body {
    font-family: 'Nunito', sans-serif;
}

</style>
<body class="font-sans flex flex-col items-center">

@include('components.header-seller')

<div class="w-full md:w-[90%] mx-auto mt-10">
    <h2 class="text-2xl font-bold mt-8 mb-4 text-center md:text-left">Избранное</h2>

    @if($favorites->isEmpty())
        <p class="text-center text-lg mt-8">Нет товаров в избранном.</p>
    @else
        <!-- Для телефонов -->
        <div class="grid grid-cols-2 sm:grid-cols-2 gap-4 sm:hidden">
            @foreach($favorites as $favorite)
                @php
                    $advert = $favorite->advert;
                @endphp
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
            @foreach($favorites as $favorite)
                @php
                    $advert = $favorite->advert;
                @endphp
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
                                <p class="beg bg-gray-20 px-3 py-1 w-24 text-sm rounded-lg text-center">{{ $advert->number }}</p>
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
    @endif
</div>

</body>
</html>