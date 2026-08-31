<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Posts') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="flex items-center justify-between">
                    <h2 class="font-bold text-lg">All Posts</h2>
                    <a href="{{ route('posts.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 transition ease-in-out duration-150">Add
                        Post</a>
                </div>
                @if ($errorMessage)
                    <div class="mt-4 p-4 bg-red-100 text-red-500">
                        ⚠️ {{ $errorMessage }}
                    </div>
                @else
                    <x-posts.list :posts="$posts" />
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
