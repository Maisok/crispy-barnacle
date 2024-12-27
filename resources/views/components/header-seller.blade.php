<style>
    /* Скрываем стандартную стрелку у select */
    select#city {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-color: transparent;
        padding: 0.5rem 2rem 0.5rem 2.5rem; /* Увеличиваем отступ слева */
        border-radius: 0.25rem;
        cursor: pointer;
        position: relative;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor' class='w-6 h-6'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'/%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 11a3 3 0 11-6 0 3 3 0 016 0z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: left 0.5rem center; /* Позиционируем значок слева */
        background-size: 1.5rem;
        font-weight: 600; /* Делаем текст жирным */
    }

    .avatar-link:hover + .avatar-popup,
    .avatar-popup:hover {
        display: block;
    }

    .wallet-popup {
        min-width: 150px;
        z-index: 10;
        right: 10px;
    }

    .wallet-link:hover + .wallet-popup,
    .wallet-popup:hover {
        display: block;
    }

    #city {
        border: none; /* Убирает границу */
        outline: none; /* Убирает внешнюю рамку при фокусе */
    }

    /* Скрываем блок .city-selector на экранах меньше 1000px */
    @media (max-width: 999px) {
        .city-selector {
            display: none !important;
        }
    }

    /* Показываем блок .city-selector на экранах 1000px и более */
    @media (min-width: 1000px) {
        .city-selector {
            display: grid !important;
            grid-template-columns: 18% 64% 18%;
            gap: 1rem;
        }
    }

    /* Показываем кнопку меню на экранах меньше 1000px */
    @media (max-width: 999px) {
        .menu-button-container {
            display: flex !important;
        }
    }

    /* Скрываем кнопку меню на экранах 1000px и более */
    @media (min-width: 1000px) {
        .menu-button-container {
            display: none !important;
        }
    }

    /* Показываем блок навигации внизу экрана на экранах меньше 1000px */
    @media (max-width: 999px) {
        .nav-bar {
            display: flex !important;
        }
    }

    /* Скрываем блок навигации внизу экрана на экранах 1000px и более */
    @media (min-width: 1000px) {
        .nav-bar {
            display: none !important;
        }
    }

    /* Стили для активной ссылки */
    .header a.active {
        color:  rgba(59, 130, 246, 1);
        text-decoration: underline;
        text-underline-offset: 4px;
        text-decoration-color: text-blue-500;
    }
    
    .unread-count {
    display: none; /* Скрываем по умолчанию */
    margin-left: 5px;
    background-color: red;
    color: white;
    border-radius: 100%;
    font-size: 12px;
    padding: 2px 6px;
}

.unread-dot {
    display: none; /* Скрываем по умолчанию */
    position: absolute;
    top: -2px; /* Смещаем точку вверх */
    right: -2px; /* Смещаем точку вправо */
    width: 8px;
    height: 8px;
    background-color: red;
    border-radius: 50%;
}
</style>

<script src="https://cdn.tailwindcss.com"></script>
<script src="{{ asset('js/header.js') }}" defer></script>
<script>
    const baseUrl = '{{ url()->current() }}'; // Передаем текущий URL в JavaScript

    document.addEventListener('DOMContentLoaded', function() {

        const currentUrl = window.location.href.split('?')[0]; // Убираем параметры из URL
    const links = document.querySelectorAll('.header a:not(.ignore-active)'); // Исключаем ссылки с классом ignore-active

    links.forEach(link => {
        if (link.href.split('?')[0] === currentUrl) { // Сравниваем без параметров
            link.classList.add('active');
        }
    });



        const menuButton = document.getElementById('menu-button');
        const menu = document.getElementById('menu');
        const overlay = document.getElementById('overlay');

        menuButton.addEventListener('click', function() {
            menu.classList.toggle('hidden');
            overlay.classList.toggle('hidden');
        });

        overlay.addEventListener('click', function() {
            menu.classList.add('hidden');
            overlay.classList.add('hidden');
        });

       
    });
</script>

<div class="header bg-white text-center w-full z-10">
    <div class="logo float-left p-5">
        <a href="{{ \App\Helpers\UrlHelper::generateUrlWithCity('adverts.index', null, request()->get('city')) }}" class="text-2xl text-blue-500 ignore-active">
            @if(auth()->check() && auth()->user()->user_status == 1 && auth()->user()->logo_url)
                <img src="{{ auth()->user()->logo_url }}" alt="Логотип" class="logourl w-3/4 h-16 mt-[-3rem]">
            @else
                <span class="text-3xl font-semibold">Где</span><strong class="text-3xl text-black">Запчасть</strong><span class="text-lg">.</span><strong class="text-3xl text-black">рф</strong>
            @endif
        </a>
    </div>

    <!-- Кнопка меню только для мобильных устройств -->
    <div class="menu-button-container md:hidden flex items-center justify-end h-20">
        <button id="menu-button" class="text-black no-underline text-base mr-4">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <!-- Навигация для больших экранов -->
    <div class="city-selector">
        <!-- Выбор города -->
        <div class="p-5">
            <select id="city" name="city" onchange="updateCitySelection()" class="text-black bg-white border-0 rounded-md p-2 col-span-1">
                <option value="">Все города</option>
                <!-- Здесь будут добавлены города через JavaScript -->
            </select>
        </div>

        <!-- Блок с ссылками для авторизованного пользователя -->
        @if(auth()->check())
        <div class="col-span-1 flex items-center justify-center">
            <a href="{{ \App\Helpers\UrlHelper::generateUrlWithCity('user.show', auth()->user()->id, request()->get('city')) }}" class="text-black no-underline mx-2.5 text-base">Профиль</a>
                   <a href="{{ \App\Helpers\UrlHelper::generateUrlWithCity('chats.index', null, request()->get('city')) }}" class="btn btn-secondary text-black no-underline mx-2.5 text-base">
    Сообщения
    <span id="unread-count" class="unread-count"></span>
</a>


            @if(auth()->user()->user_status == 0)
                <a href="{{ \App\Helpers\UrlHelper::generateUrlWithCity('adverts.viewed', null, request()->get('city')) }}" class="text-black no-underline mx-2.5 text-base">Вы посмотрели</a>
                <a href="{{ \App\Helpers\UrlHelper::generateUrlWithCity('adverts.favorites', null, request()->get('city')) }}" class="text-black no-underline mx-2.5 text-base">Избранное</a>
            @else
                @if(auth()->user()->user_status != 2)
                    <a href="{{ \App\Helpers\UrlHelper::generateUrlWithCity('adverts.create', null, request()->get('city')) }}" class="text-black no-underline mx-2.5 text-base">Разместить товары</a>
                    <a href="{{ \App\Helpers\UrlHelper::generateUrlWithCity('adverts.my_adverts', null, request()->get('city')) }}" class="text-black no-underline mx-2.5 text-base">Мои товары</a>
                @endif
            @endif

            @if(auth()->user()->is_seller)
                <!-- Здесь можно добавить ссылки или элементы для продавца -->
            @endif
        </div>

        <!-- Блок с балансом и аватаркой -->
        <div class="col-span-1 justify-self-end mr-20 flex items-center justify-center h-full">
            <div class="flex items-center justify-center space-x-2">
                @if(auth()->user()->user_status == 1)
                <!-- Блок с балансом -->
                <div class="relative">
                    <a class="flex items-center justify-center wallet-link">
                        <img src="{{asset('images/Wallet.png')}}" class="w-6 h-6" alt="">
                    </a>
                    <div class="absolute top-full mt-2 bg-white border border-gray-300 rounded-md shadow-lg text-center hidden wallet-popup">
                        <a href="" class="text-sm flex items-center justify-center py-3 px-6">
                            <span>{{ auth()->user()->balance }} ₽</span>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Аватарка -->
                <div class="relative">
                    <img alt="User profile picture" class="rounded-full w-14 h-14 object-contain cursor-pointer avatar-link" src="{{ auth()->user()->avatar_url ? auth()->user()->avatar_url : asset('images/noava.jpg') }}" />

                    <!-- Выпадающее окошко -->
                    <div class="absolute top-full mt-2 right-0 bg-white border border-gray-300 rounded-md shadow-lg text-center hidden avatar-popup">
                        <form action="{{ route('logout') }}" method="POST" class="mb-0">
                            @csrf
                            <button type="submit" class="text-sm flex items-center justify-center py-3 px-6">
                                Выйти
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @else
        <!-- Кнопка "Войти" для незарегистрированных пользователей -->
        <div class="col-span-1 justify-self-end mr-20 flex items-center justify-center h-full">
            <a href="{{ route('login') }}" class="btn btn-primary text-black no-underline mx-2.5 text-base flex items-center justify-center">Войти</a>
            <a href="{{ \App\Helpers\UrlHelper::generateUrlWithCity('adverts.favorites', null, request()->get('city')) }}" class="text-black no-underline mx-2.5 text-base">Избранное</a>
            <a href="{{ \App\Helpers\UrlHelper::generateUrlWithCity('chats.index', null, request()->get('city')) }}" class="btn btn-secondary text-black no-underline mx-2.5 text-base">Сообщения</a>
        </div>
      
        @endif
    </div>
</div>

<div id="menu" class="hidden fixed top-[70px] left-0 w-full h-full bg-white z-30 flex flex-col justify-center items-center">
    <ul class="space-y-4 text-center">
        <li><a href="{{route('about')}}" class="block text-lg text-gray-700 hover:text-gray-900">О проекте</a></li>
        <li><a href="{{route('oferta')}}" class="block text-lg text-gray-700 hover:text-gray-900">Оферта</a></li>
        <li><a href="{{route('franchise.index')}}" class="block text-lg text-gray-700 hover:text-gray-900">Франшиза</a></li>
        <li><a href="{{route('help.index')}}" class="block text-lg text-gray-700 hover:text-gray-900">Справка</a></li>

        @if(auth()->check())
        <li class="block text-lg text-gray-700 hover:text-gray-900">
            <form action="{{ route('logout') }}" method="POST" style="display:inline;" class="mb-0">
                @csrf
                <button type="submit">Выйти</button>
            </form>
        </li>
        @endif
    </ul>
</div>

<!-- Навигация для мобильных устройств -->
<div class="fixed bottom-0 left-0 w-full bg-white shadow-lg p-2 z-10 flex justify-around items-center md:hidden overflow-x-hidden border border-t-gray-300 nav-bar">
    <a href="{{ \App\Helpers\UrlHelper::generateUrlWithCity('adverts.index', null, request()->get('city')) }}" class="text-black no-underline text-sm flex flex-col items-center flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        Поиск
    </a>
    @if(auth()->check())
    <a href="{{ \App\Helpers\UrlHelper::generateUrlWithCity('adverts.favorites', null, request()->get('city')) }}" class="text-black no-underline text-sm flex flex-col items-center flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
        </svg>
        Избранное
    </a>
    <a href="{{ \App\Helpers\UrlHelper::generateUrlWithCity('chats.index', null, request()->get('city')) }}" class="text-black no-underline text-sm flex flex-col items-center flex-shrink-0">
        <div class="relative"> <!-- Обертка для иконки и точки -->
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
            </svg>
            <div id="unread-dot" class="unread-dot"></div> <!-- Красная точка -->
        </div>
        Сообщения
    </a>

    <a href="{{ \App\Helpers\UrlHelper::generateUrlWithCity('user.show', auth()->user()->id, request()->get('city')) }}" class="text-black no-underline text-sm flex flex-col items-center flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        Профиль
    </a>
    @else
    <a href="{{route('login')}}" class="text-black no-underline text-sm flex flex-col items-center flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        Войти
    </a>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const avatarLink = document.querySelector('.avatar-link');
        const avatarPopup = document.querySelector('.avatar-popup');

        let isHovered = false; // Переменная для отслеживания состояния наведения

        // Обработчик наведения на аватарку
        avatarLink.addEventListener('mouseenter', function() {
            isHovered = true;
            avatarPopup.classList.remove('hidden');
        });

        // Обработчик ухода курсора с аватарки
        avatarLink.addEventListener('mouseleave', function() {
            setTimeout(() => {
                if (!isHovered) {
                    avatarPopup.classList.add('hidden');
                }
            }, 100); // Задержка, чтобы дать время на переход на выпадающее окно
        });

        // Обработчик наведения на выпадающее окно
        avatarPopup.addEventListener('mouseenter', function() {
            isHovered = true;
        });

        // Обработчик ухода курсора с выпадающего окна
        avatarPopup.addEventListener('mouseleave', function() {
            isHovered = false;
            avatarPopup.classList.add('hidden');
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(auth()->check())
        // Функция для получения количества непрочитанных сообщений
        function getUnreadCount() {
            return fetch('/messages/unread-count', {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // Токен CSRF
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Ошибка сети');
                }
                return response.json();
            });
        }

        // Функция для обновления красной точки
        function updateUnreadCount() {
            getUnreadCount()
                .then(data => {
                    const unreadCount = data.unread_count;
                    const unreadDotElement = document.getElementById('unread-dot');

                    // Обновляем красную точку
                    if (unreadDotElement) {
                        if (unreadCount > 0) {
                            unreadDotElement.style.display = 'block'; // Показываем точку
                        } else {
                            unreadDotElement.style.display = 'none'; // Скрываем точку, если сообщений нет
                        }
                    }
                })
                .catch(error => {
                    console.error('Ошибка при получении количества непрочитанных сообщений:', error);
                });
        }

        // Обновляем точку каждые 3 секунды
        setInterval(updateUnreadCount, 3000);

        // Первоначальное обновление точки
        updateUnreadCount();
    @endif
});
</script>