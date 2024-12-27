<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чат</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link rel="shortcut icon" href="{{asset('images/Group 438.png')}}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <style>
        .message-time-left {
            order: -1;
        }
        .message-time-right {
            order: 1;
        }
        /* Фиксированный блок с формой */
        .message-form {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: white;
            z-index: 0;
            padding: 16px;
            border-top: 1px solid #e2e8f0;
        }
        /* Добавляем отступ в контейнер с сообщениями */
        #chat-messages {
            padding-bottom: 180px; /* Отступ, равный высоте формы + навигационной панели */
        }
    </style>

<style>
    body {
    font-family: 'Nunito', sans-serif;
}
</style>
</head>
<body class="h-screen overflow-hidden">
    @include('components.header-seller')

    <div class="w-full h-full">
        <div class="flex w-full h-full flex-col md:flex-row pb-24">
            <!-- Боковая панель для списка чатов на больших экранах -->
            <div class="chat-list-container w-2/6  md:block hidden">
            @include('components.chat-list', ['userChats' => $userChats])
        </div>

            <div class=" flex flex-col w-full h-full border-l border-gray-300">
                @if($chat && $advert)
                    <!-- Ссылка на страницу с чатами (только для мобильных устройств) -->
                    <a href="{{ route('chats.index') }}" class="md:hidden flex items-center justify-start w-full p-4 bg-white text-blue-500 hover:underline text-sm">
                        <i class="fas fa-arrow-left mr-2"></i> Назад к чатам
                    </a>

                    <!-- Шапка с информацией о товаре -->
                    <div class="flex items-center border-b justify-between w-full p-4 bg-white">
                        <div class="flex items-center space-x-4">
                            <img alt="Product image" class="w-12 h-12 rounded-full" src="{{ $advert->main_photo_url ?: asset('images/no-image.jpg') }}" width="50" height="50"/>
                            <div>
                                <h2 class="text-xl font-bold">
                                    <a href="{{ route('advert.show', ['advert' => $advert->id]) }}" class="text-blue-500 hover:underline">
                                        {{ $advert->product_name }}
                                    </a>
                                </h2>
                                <p class="text-lg text-gray-500">
                                    {{ $advert->price }}₽
                                </p>
                            </div>
                        </div>
                        @if($messages->isNotEmpty() && $messages->last())
                        @else
                            <span class="text-lg text-gray-500">
                                Нет сообщений
                            </span>
                        @endif
                    </div>

                    <!-- Список сообщений -->
                    <div id="chat-messages" class="flex-1 p-4 w-full space-y-4 overflow-y-auto">
                        @foreach($messages as $message)
                            <div class="flex items-end space-x-4 @if($message->user_id === auth()->id()) justify-end @endif">
                                @if($message->user_id !== auth()->id())
                                    <img alt="User avatar" class="w-10 h-10 rounded-full" src="{{ $message->user->avatar_url ?: asset('images/noava.jpg') }}" width="50" height="50"/>
                                @endif
                                <div class="bg-gray-100 p-3 rounded-lg @if($message->user_id === auth()->id()) bg-green-100 @endif">
                                    <p>{{ $message->message }}</p>
                                </div>
                                <span class="text-sm text-gray-500 @if($message->user_id === auth()->id()) message-time-left @else message-time-right @endif">
                                    {{ $message->created_at->format('H:i') }}
                                </span>
                                @if($message->user_id === auth()->id())
                                    <img alt="User avatar" class="w-10 h-10 rounded-full" src="{{ $message->user->avatar_url ?: asset('images/noava.jpg') }}" width="50" height="50"/>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Форма для отправки сообщения -->
                    <div class="message-form p-4 border-t bg-white flex items-center space-x-4 pb-20 sm:pb-20 lg:pb-4">
                        <form action="{{ route('chat.send', ['chat' => $chat]) }}" method="POST" class="flex w-full">
                            @csrf
                            <input type="text" name="message" class="mr-2 flex-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Сообщение" required>
                            <button class="bg-blue-500 text-white px-4 py-2 rounded-lg" type="submit">
                                Отправить
                            </button>
                        </form>
                    </div>
                @else
                    <p class="text-gray-600 p-4">Выберите чат из списка.</p>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/ru.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Устанавливаем русскую локализацию для moment.js
            moment.locale('ru');

            // Обработчик отправки формы
            $('form').on('submit', function(e) {
                e.preventDefault(); // Предотвращаем стандартное поведение формы

                var messageInput = $(this).find('input[name="message"]');
                var message = messageInput.val();
                var chatId = '{{ $chat->id }}'; // Получаем ID чата

                $.ajax({
                    url: '/chat/' + chatId + '/send',
                    type: 'POST',
                    data: {
                        message: message,
                        _token: '{{ csrf_token() }}' // Добавляем CSRF-токен
                    },
                    success: function(response) {
                        // Добавляем новое сообщение в интерфейс
                        var formattedTime = moment(response.created_at).format('H:i');
                        $('#chat-messages').append('<div class="flex  items-end space-x-4">' +
                            '<span class="text-sm text-gray-500 message-time-left">' + formattedTime + '</span>' +
                            '<div class="bg-green-100 p-3 rounded-lg">' +
                                '<p>' + response.message + '</p>' +
                            '</div>' +
                            '<img alt="User avatar" class="w-10 h-10 rounded-full" src="{{ auth()->user()->avatar_url ?: asset('images/noava.jpg') }}" width="50" height="50"/>' +
                        '</div>');
                        messageInput.val(''); // Очищаем поле ввода
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText); // Обработка ошибок
                    }
                });
            });

            // Функция для периодической проверки новых сообщений
            function fetchMessages() {
                var chatId = '{{ $chat->id }}'; // Получаем ID чата

                $.ajax({
                    url: '/chat/' + chatId + '/messages', // Создайте этот маршрут и метод в контроллере
                    type: 'GET',
                    success: function(response) {
                        $('#chat-messages').empty(); // Очищаем старые сообщения
                        var lastDate = null;
                        response.messages.forEach(function(message) {
                            // Форматируем время с помощью moment.js
                            var formattedTime = moment(message.created_at).format('H:mm');
                            var messageDate = moment(message.created_at).format('YYYY-MM-DD');

                            // Добавляем дату, если это первое сообщение за день
                            if (lastDate !== messageDate) {
                                $('#chat-messages').append('<div class="message-date text-center text-gray-500 text-sm my-2">' + moment(message.created_at).format('DD.MM.YYYY') + '</div>');
                                lastDate = messageDate;
                            }

                            // Добавляем сообщение с аватаром и временем
                            var avatarUrl = message.user.avatar_url || "{{ asset('images/noava.jpg') }}";
                            var messageClass = message.user_id === {{ auth()->id() }} ? 'justify-end' : '';
                            var avatarPosition = message.user_id === {{ auth()->id() }} ? 'order-last' : 'order-first';
                            var timePosition = message.user_id === {{ auth()->id() }} ? 'message-time-left' : 'message-time-right';

                            $('#chat-messages').append('<div class="flex  items-end space-x-4 ' + messageClass + '">' +
                                '<img alt="User avatar" class="w-10 h-10 rounded-full ' + avatarPosition + '" src="' + avatarUrl + '" width="50" height="50"/>' +
                                '<div class="bg-gray-100 p-3 rounded-lg ' + (message.user_id === {{ auth()->id() }} ? 'bg-green-100' : '') + '">' +
                                    '<p>' + message.message + '</p>' +
                                '</div>' +
                                '<span class="text-sm text-gray-500 ' + timePosition + '">' + formattedTime + '</span>' +
                            '</div>');
                        });
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText); // Обработка ошибок
                    }
                });
            }

            // Запускаем функцию каждые 10 секунд
            setInterval(fetchMessages, 1000);
        });
    </script>
</body>
</html>