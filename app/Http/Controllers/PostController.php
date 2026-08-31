<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorizeAbility(
            'post:read',
            'dashboard',
            'Forbidden. You don\'t have permission to access post list page.'
        );

        $queryParams = request()->query();

        $posts = new LengthAwarePaginator([], 0, 10);

        $errorMessage = null;

        try {
            $response = Http::withToken(request()->user()->api_token)
                ->acceptJson()
                ->timeout(5)
                ->get('http://portal-server.test/api/posts', $queryParams);

            if ($response->successful()) {
                $apiData = $response->json();

                // dd($apiData);

                $hydratedPosts = Post::hydrate($apiData['data']);

                $posts = new LengthAwarePaginator(
                    $hydratedPosts,
                    $apiData['meta']['total'],
                    $apiData['meta']['per_page'],
                    $apiData['meta']['current_page'],
                    [
                        'path' => request()->url(),
                        'query' => request()->query()
                    ]
                );
            } else {
                if ($response->status() === 401) {
                    $errorMessage = 'Unauthenticated. The access token is missing, expired, or invalid.';
                } elseif ($response->status() === 403) {
                    $errorMessage = 'Forbidden. You don\'t have permission to access this page (Invalid API token).';
                } else {
                    $errorMessage = 'There was an error during data loading. Please, try again later.';
                }

                Log::error('API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                $this->apiTokenAbilitiesCheck();
            }
        } catch (\Exception $e) {
            $errorMessage = 'System is unavailable at the moment. Please, try again later.';
            Log::critical('API Server Unavailable: ' . $e->getMessage());
        }

        return view('posts.index', compact('posts', 'errorMessage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeAbility(
            'post:create',
            'posts.index',
            'Forbidden. You don\'t have permission to access create post page.'
        );

        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorizeAbility(
            'post:create',
            'posts.index',
            'Forbidden. You don\'t have permission to create posts.'
        );

        $response = Http::withToken(request()->user()->api_token)
            ->acceptJson()
            ->timeout(5)
            ->post('http://portal-server.test/api/posts', $request->all());

        if ($response->status() === 403) {
            $this->apiTokenAbilitiesCheck();

            return back()
                ->with('error', 'Forbidden. You don\'t have permission to create posts (Invalid API token).')
                ->withInput();
        }

        if ($response->status() === 422) {
            return back()
                ->withErrors($response->json('errors'))
                ->withInput();
        }

        if ($response->successful()) {
            return redirect()->route('posts.index')
                ->with('success', 'Post has been created successfully!');
        }

        return back()->with('error', 'API server error occurred.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorizeAbility(
            'post:read',
            'dashboard',
            'Forbidden. You don\'t have permission to access post detail page.'
        );

        $response = Http::withToken(request()->user()->api_token)
            ->acceptJson()
            ->timeout(5)
            ->get('http://portal-server.test/api/posts/' . $id);

        if ($response->failed()) {
            if ($response->status() === 403) {
                $this->apiTokenAbilitiesCheck();
            }

            abort(404);
        }

        $postArray = $response->json('data');

        $post = Post::make($postArray);
        $post->exists = true;

        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorizeAbility(
            'post:update',
            'posts.index',
            'Forbidden. You don\'t have permission to access edit post page.'
        );

        $response = Http::withToken(request()->user()->api_token)
            ->acceptJson()
            ->timeout(5)
            ->get('http://portal-server.test/api/posts/' . $id);

        if ($response->failed()) {
            if ($response->status() === 403) {
                $this->apiTokenAbilitiesCheck();
            }

            abort(404);
        }

        $postArray = $response->json('data');

        $post = Post::make($postArray);
        $post->exists = true;

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorizeAbility(
            'post:update',
            'posts.index',
            'Forbidden. You don\'t have permission to update posts.'
        );

        $response = Http::withToken(request()->user()->api_token)
            ->acceptJson()
            ->timeout(5)
            ->patch('http://portal-server.test/api/posts/' . $id, $request->all());

        if ($response->status() === 403) {
            $this->apiTokenAbilitiesCheck();

            return back()
                ->with('error', 'Forbidden. You don\'t have permission to update posts (Invalid API token).')
                ->withInput();
        }

        if ($response->status() === 422) {
            return back()
                ->withErrors($response->json('errors'))
                ->withInput();
        }

        if ($response->successful()) {
            return redirect()->route('posts.index')
                ->with('success', 'Post has been updated successfully!');
        }

        return back()->with('error', 'API server error occurred.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorizeAbility(
            'post:delete',
            'posts.index',
            'Forbidden. You don\'t have permission to delete posts.'
        );

        $response = Http::withToken(request()->user()->api_token)
            ->acceptJson()
            ->timeout(5)
            ->delete('http://portal-server.test/api/posts/' . $id);

        if ($response->status() === 403) {
            $this->apiTokenAbilitiesCheck();

            return redirect()->route('posts.index')
                ->with('error', 'Forbidden. You don\'t have permission to delete posts (Invalid API token).');
        }

        if ($response->successful()) {
            return redirect()->route('posts.index')
                ->with('success', 'Post has been deleted successfully!');
        }

        return back()->with('error', 'Could not delete the post from API server.');
    }

    private function authorizeAbility(string $ability, string $redirectTo = 'posts.index', string $message = 'Forbidden. You don\'t have permission to perform this action.')
    {
        $abilities = request()->user()->api_token_abilities ?? [];

        if (!in_array($ability, $abilities)) {
            $redirect = redirect()->route($redirectTo)->with('error', $message);

            $this->apiTokenAbilitiesCheck();

            throw new HttpResponseException($redirect);
        }
    }

    private function apiTokenAbilitiesCheck()
    {
        try {
            $response = Http::withToken(request()->user()->api_token)
                ->acceptJson()
                ->timeout(5)
                ->get('http://portal-server.test/api/user/abilities');

            if ($response->successful()) {
                request()->user()->update([
                    'api_token_abilities' => $response->json('abilities', [])
                ]);
            } else {
                request()->user()->update([
                    'api_token_abilities' => []
                ]);

                Log::warning('API token abilities cleared due to server response', [
                    'status' => $response->status()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to sync API token abilities (Server offline): ' . $e->getMessage());
        }
    }
}
