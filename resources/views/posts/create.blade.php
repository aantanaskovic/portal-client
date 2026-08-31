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
                    <h2 class="font-bold text-lg">Create Post</h2>
                </div>

                <form action="{{ route('posts.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <x-label for="title" value="{{ __('Title') }}" />
                        <x-input id="title" class="block mt-1 w-full" type="text" name="title"
                            :value="old('title')" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="content" value="{{ __('Content') }}" />
                        <x-textarea id="content" class="block mt-1 w-full" type="text" name="content"
                            :value="old('content')" />
                        <x-input-error :messages="$errors->get('content')" class="mt-2" />
                    </div>

                    <x-button>
                        {{ __('Create') }}
                    </x-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
