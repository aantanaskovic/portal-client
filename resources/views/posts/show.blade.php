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
                    <h2 class="font-bold text-lg">Post Details</h2>
                </div>

                <div class="py-6 space-y-4">
                    <h3 class="font-medium text-lg">{{ $post->title }}</h3>
                    <p>{{ $post->content }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
