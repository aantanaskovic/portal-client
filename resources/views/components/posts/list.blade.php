@props(['posts'])

<div class="space-y-4">
    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach ($posts as $post)
            <x-posts.list-item :post="$post" />
        @endforeach
    </div>

    <div>{{ $posts->links() }}</div>
</div>
