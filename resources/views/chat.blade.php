<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ChatApp
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">

                <div id="chat-box" class="h-64 overflow-y-auto mb-6 border border-gray-100 p-4 rounded-lg bg-gray-50">

                    @forelse($messages as $message)
                        <div class="mb-3">
                            
                            {{-- pesan --}}
                            <span class="text-gray-700">
                                {{ $message->message }}
                            </span>

                            {{-- jam kecil --}}
                            <div class="text-xs text-gray-400">
                                {{ $message->created_at->format('H:i') }}
                            </div>

                        </div>
                    @empty
                        <p class="text-center text-gray-400 mt-10">
                            Belum ada pesan...
                        </p>
                    @endforelse

                </div>

                <form action="/chat" method="POST">
                    @csrf

                    <div style="display:flex; gap:10px; align-items:center;">
                        <input type="text"
                               name="message"
                               autocomplete="off"
                               placeholder="Tulis pesan..."
                               required
                               class="rounded-full border border-gray-300 p-3"
                               style="flex:1; outline:none; box-shadow:none;">

                        <button type="submit"
                                style="background-color:#f1d275; color:white; padding:10px 25px; border-radius:50px; font-weight:bold; border:none;">
                            Kirim
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>