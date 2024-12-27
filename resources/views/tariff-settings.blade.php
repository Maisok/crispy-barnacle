<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="{{asset('images/Group 438.png')}}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <title>Настройки тарифа</title>
</head>
<style>
    body {
    font-family: 'Nunito', sans-serif;
}
</style>
<body class="flex justify-center flex-col">
@include('components.header-seller')
@extends('layouts.app')

<div class="container mx-auto p-4 mt-40 mb-20">
    <h1 class="text-3xl font-bold text-center mb-6">Выберите количество товаров:</h1>
    
    @if(!$hasTariff)
    <div class="mt-6 text-center">
        <p class="text-lg text-gray-700">Вам доступен пробный тариф "99999 объявлений на 14 дней!"</p>
         <form action="{{ route('create.trial.tariff') }}" method="POST">
        @csrf
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">Попробовать</button>
    </form>
    </div>
    @else
    <div class="slider-container">
        <label for="ad-count-manual" class="block text-lg font-bold mb-2">Количество товаров:</label>
        <input type="number" id="ad-count-manual" min="100" max="100000" step="100" value="100" class="w-1/4 p-2 border rounded-md">
        <input type="range" id="ad-count" name="ad-count" min="100" max="100000" step="100" value="100" class="w-3/4">
        <span id="ad-count-value" class="inline-block ml-2 font-bold"></span>
    </div>

    <div class="price-container mt-6">
        <p class="mb-2">Стоимость размещения в день: <span id="daily-cost" class="text-green-600">₽75.00</span></p>
        <p class="mb-2">Стоимость размещения в день одного товара: <span id="daily-cost-per-item" class="text-green-600">₽0.75</span></p>
        <p class="mb-4">Стоимость размещения в месяц: <span id="monthly-cost" class="text-green-600">₽2250.00</span></p>
        <form action="{{ route('save.tariff') }}" method="POST" id="tariff-form">
            @csrf
            <input type="hidden" id="ad-count-hidden" name="ad-count" value="100">
            <button type="submit" id="save-button" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Сохранить</button>
        </form>
    </div>
    @endif
</div>

<!-- Вывод сообщения с подтверждением -->
@if(session('warning'))
<div class="fixed bottom-0 left-0 w-full bg-yellow-100 text-yellow-700 p-4 text-center" id="confirmation-message">
    {{ session('warning') }}
    <div class="mt-4">
        <button id="confirm-yes" class="bg-green-500 text-white px-4 py-2 rounded-md mr-2">Да</button>
        <button id="confirm-no" class="bg-red-500 text-white px-4 py-2 rounded-md">Нет</button>
    </div>
</div>
@endif

<h3 class="text-xl font-bold text-center mt-6">Нужно разместить больше 100.000 товаров?</h3>
<h3 class="text-xl font-bold text-center">Напишите нам и мы подготовим для Вас персональное предложение</h3>

<script>
    const slider = document.getElementById('ad-count');
    const manualInput = document.getElementById('ad-count-manual');
    const dailyCost = document.getElementById('daily-cost');
    const dailyCostPerItem = document.getElementById('daily-cost-per-item');
    const monthlyCost = document.getElementById('monthly-cost');
    const adCountHidden = document.getElementById('ad-count-hidden');

    slider.oninput = function() {
        manualInput.value = this.value;
        adCountHidden.value = this.value;
        updatePrices(this.value);
    }

    manualInput.oninput = function() {
        slider.value = this.value;
        adCountHidden.value = this.value;
        updatePrices(this.value);
    }

    function updatePrices(adCount) {
        // Пример расчета цен в рублях
        const basePricePerDay = 0.75; // Базовая стоимость за одно объявление в день в рублях
        const discountFactor = 1 - Math.min(0.5, (adCount - 100) / 100000); // Дисконтный фактор

        const dailyCostValue = adCount * basePricePerDay * discountFactor;
        const dailyCostPerItemValue = basePricePerDay * discountFactor;
        const monthlyCostValue = dailyCostValue * 30;

        dailyCost.textContent = `₽${dailyCostValue.toFixed(2)}`;
        dailyCostPerItem.textContent = `₽${dailyCostPerItemValue.toFixed(2)}`;
        monthlyCost.textContent = `₽${monthlyCostValue.toFixed(2)}`;
    }

    // Обработка подтверждения
    document.getElementById('confirm-yes')?.addEventListener('click', function() {
        const form = document.getElementById('tariff-form');
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'confirm';
        hiddenInput.value = 'true';
        form.appendChild(hiddenInput);
        form.submit();
    });

    document.getElementById('confirm-no')?.addEventListener('click', function() {
        document.getElementById('confirmation-message').remove();
    });
</script>

</body>
</html>