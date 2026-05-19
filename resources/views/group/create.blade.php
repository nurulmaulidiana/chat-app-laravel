<x-app-layout>

    <div class="p-6">

        <h2>Create Group</h2>

        @if(session('success'))
            <p>{{ session('success') }}</p>
        @endif

        <form method="POST" action="/group/store">

            @csrf

            <div>
                <input type="text"
                       name="name"
                       placeholder="Nama Group">
            </div>

            <br>

            <h4>Pilih Member:</h4>

            @foreach($users as $user)

                <div>
                    <input type="checkbox"
                           name="users[]"
                           value="{{ $user->id }}">

                    {{ $user->name }}
                </div>

            @endforeach

            <br>

            <button type="submit">
                Create Group
            </button>

        </form>

    </div>

</x-app-layout>