<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                {{-- Search Form --}}
                <div class="px-6 py-4 mb-5 md:w-1/2 xl:w-1/3">
                    @if (request('search'))
                        <h2 class="pb-3 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                            Search result for "{{ request('search') }}"
                        </h2>
                    @endif

                    <form method="GET" action="{{ route('user.index') }}" class="flex items-center gap-4">
                        <div>
                            <x-text-input id="search" name="search" type="text" class="w-50"
                                          placeholder="Search by name or email" value="{{ request('search') }}" autofocus />
                        </div>
                        <div>
                            <x-primary-button type="submit">
                                {{ __('Search') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>

                {{-- Alert Messages --}}
                <div class="px-6 text-xl text-gray-900 dark:text-gray-100">
                    @if (session('success'))
                        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                            <p class="pb-3 text-sm text-green-600 dark:text-green-400">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if (session('danger'))
                        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 5000)">
                            <p class="pb-3 text-sm text-red-600 dark:text-red-400">{{ session('danger') }}</p>
                        </div>
                    @endif
                </div>

                {{-- User Table --}}
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Id</th>
                                <th scope="col" class="px-6 py-3">Nama</th>
                                <th scope="col" class="hidden px-6 py-3 md:block">Email</th>
                                <th scope="col" class="px-6 py-3">Todo</th>
                                <th scope="col" class="px-6 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr class="odd:bg-white odd:dark:bg-gray-800 even:bg-gray-50 even:dark:bg-gray-700">
                                    <td class="px-6 py-4 font-medium whitespace-nowrap dark:text-white">
                                        {{ $user->id }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $user->name }}
                                    </td>
                                    <td class="hidden px-6 py-4 md:block">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p>
                                            {{ $user->todos->count() }}
                                            <span>
                                                <span class="text-green-600 dark:text-green-400">
                                                    ({{ $user->todos->where('is_done', true)->count() }}
                                                </span> /
                                                <span class="text-blue-600 dark:text-blue-400">
                                                    {{ $user->todos->where('is_done', false)->count() }} )
                                                </span>
                                            </span>
                                        </p>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{-- Admin toggle button --}}
                                        <div class="flex space-x-3" id="user-actions-{{ $user->id }}">
                                            @if ($user->is_Admin)
                                                <form action="{{ route('user.removeadmin', $user) }}" method="Post"
                                                      data-user-id="{{ $user->id }}" data-action="removeadmin">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="admin-toggle-button text-blue-600 dark:text-blue-400 whitespace-nowrap">
                                                        Remove Admin
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('user.makeadmin', $user) }}" method="Post"
                                                      data-user-id="{{ $user->id }}" data-action="makeadmin">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="admin-toggle-button text-red-600 dark:text-red-400 whitespace-nowrap">
                                                        Make Admin
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('user.destroy', $user) }}" method="Post">
                                                @csrf
                                                @method('delete')
                                                <button type="submit"
                                                        class="text-red-600 dark:text-red-400 whitespace-nowrap">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="odd:bg-white odd:dark:bg-gray-800 even:bg-gray-50 even:dark:bg-gray-700">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-5">
                    {{ $users->links() }}
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                attachEventListeners();
            });

            function attachEventListeners() {
                const adminToggleButtons = document.querySelectorAll('.admin-toggle-button');

                adminToggleButtons.forEach(button => {
                    button.addEventListener('click', function(event) {
                        event.preventDefault();
                        const form = this.closest('form');
                        const userId = form.dataset.userId;
                        const action = form.dataset.action;
                        const formData = new FormData(form);

                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const actionsDiv = document.getElementById(`user-actions-${userId}`);
                                actionsDiv.innerHTML = '';

                                // Update the button based on the current action
                                if (action === 'makeadmin') {
                                    // Show Remove Admin button
                                    actionsDiv.innerHTML = `
                                        <form action="{{ route('user.removeadmin', '') }}/${userId}" method="Post"
                                              data-user-id="${userId}" data-action="removeadmin"
                                              class="flex space-x-3">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="admin-toggle-button text-blue-600 dark:text-blue-400 whitespace-nowrap">
                                                Remove Admin
                                            </button>
                                        </form>
                                    `;
                                } else {
                                    // Show Make Admin button
                                    actionsDiv.innerHTML = `
                                        <form action="{{ route('user.makeadmin', '') }}/${userId}" method="Post"
                                              data-user-id="${userId}" data-action="makeadmin"
                                              class="flex space-x-3">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="admin-toggle-button text-red-600 dark:text-red-400 whitespace-nowrap">
                                                Make Admin
                                            </button>
                                        </form>
                                    `;
                                }

                                // Add delete button
                                actionsDiv.innerHTML += `
                                    <form action="{{ route('user.destroy', '') }}/${userId}" method="Post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 dark:text-red-400 whitespace-nowrap">
                                            Delete
                                        </button>
                                    </form>
                                `;

                                // Re-attach event listeners
                                attachEventListeners();

                                // Show success message
                                if (data.message) {
                                    // You can implement a toast notification here
                                    console.log(data.message);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                    });
                });
            }
        </script>
    @endpush
</x-app-layout>