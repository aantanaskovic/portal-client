@props(['post'])

<div class="py-6 space-y-2">
    <h2 class="font-semibold text-lg"><a href="{{ route('posts.show', $post->id) }}">{{ $post->title }}</a></h2>
    <div class="text-sm italic">Author: {{ $post->user->name }}</div>
    <p>{{ $post->content }}</p>
    <div class="flex space-x-2">
        <a href="{{ route('posts.edit', $post->id) }}"
            class="inline-flex items-center px-4 py-2 bg-green-800 dark:bg-green-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-green-800 uppercase tracking-widest hover:bg-green-700 dark:hover:bg-white focus:bg-green-700 dark:focus:bg-white active:bg-green-900 dark:active:bg-green-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-green-800 transition ease-in-out duration-150">
            Edit</a>
        <form action="{{ route('posts.destroy', $post->id) }}" method="POST">
            @csrf
            @method('DELETE')

            <x-primary-button
                class="inline-flex items-center px-4 py-2 bg-red-800 dark:bg-red-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-red-800 uppercase tracking-widest hover:bg-red-700 dark:hover:bg-white focus:bg-red-700 dark:focus:bg-white active:bg-red-900 dark:active:bg-red-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-red-800 transition ease-in-out duration-150">
                Delete
            </x-primary-button>
        </form>
    </div>
</div>
