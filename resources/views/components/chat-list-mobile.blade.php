<div class="chat-list-mobile max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-4">Сообщения</h1>
    <div class="space-y-4">
        @foreach($userChats as $userChat)
            <a href="{{ route('chat.show', ['chat' => $userChat->id]) }}" class="block">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <img alt="User avatar" class="w-10 h-10 rounded-full" src="{{ ($userChat->user1_id == auth()->id() ? $userChat->user2->avatar_url : $userChat->user1->avatar_url) ?: asset('images/noava.jpg') }}" width="40" height="40"/>
                        <div>
                            <div class="font-bold">
                                {{ $userChat->user1_id == auth()->id() ? $userChat->user2->username : $userChat->user1->username }}
                            </div>
                            <div class="text-gray-500">
                                {{ Str::limit($userChat->last_message->message ?? 'Нет сообщений', 20, '...') }}
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-gray-500">
                            {{ $userChat->last_message ? $userChat->last_message->created_at->format('H:i') : '' }}
                        </div>
                        @if($userChat->unread_count > 0)
                            <div class="text-blue-500">
                                <i class="fas fa-check-double"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <hr/>
            </a>
        @endforeach
    </div>
</div>