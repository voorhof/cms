<?php

namespace App\Http\Controllers\Cms;

use App\Http\Requests\Cms\StorePostRequest;
use App\Http\Requests\Cms\UpdatePostRequest;
use App\Models\Post;
use App\Services\Cms\Contracts\PostServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class CmsPostController extends BaseCmsController implements HasMiddleware
{
    /**
     * Create a new controller instance,
     * with the post service implementation.
     *
     * @return void
     */
    public function __construct(
        protected PostServiceInterface $postService
    ) {}

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,App\Models\Post', only: ['index']),
            new Middleware('can:view,post', only: ['show']),
            new Middleware('can:create,App\Models\Post', only: ['create', 'store']),
            new Middleware('can:update,post', only: ['edit', 'update']),
            new Middleware('can:publish,post', only: ['publish']),
            new Middleware('can:delete,post', only: ['destroy']),
            new Middleware('can:restore,post', only: ['restore']),
            new Middleware('can:forceDelete,post', only: ['delete']),
            new Middleware('can:viewTrash,App\Models\Post', only: ['viewTrash']),
            new Middleware('can:emptyTrash,App\Models\Post', only: ['emptyTrash']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $posts = Post::with('author')
            ->orderByDesc('created_at')
            ->get();

        $postsTrashCount = Post::onlyTrashed()->count();

        return view('cms.posts.index', compact('posts', 'postsTrashCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('cms.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        // The request is already validated through the StorePostRequest class
        $post = $this->postService->createPost(
            $request->validated(),
            auth()->id()
        );

        return redirect()->route(config('cms.route_name_prefix').'.posts.show', $post);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): View
    {
        return view('cms.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post): View
    {
        return view('cms.posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        // The request is already validated through the UpdatePostRequest class
        $updatedPost = $this->postService->updatePost(
            $post,
            $request->validated()
        );

        return redirect()->route(config('cms.route_name_prefix').'.posts.show', $updatedPost);
    }

    /**
     * (un-)Publish the specified resource.
     */
    public function publish(Request $request, Post $post): RedirectResponse
    {
        $post = $this->postService->togglePublishStatus(
            $post,
            $request->published_at,
            auth()->id()
        );

        return redirect()->route(config('cms.route_name_prefix').'.posts.show', $post);
    }

    /**
     * Soft delete the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->postService->deletePost($post);

        return redirect()->route(config('cms.route_name_prefix').'.posts.index');
    }

    /**
     * Display a listing of soft deleted resource.
     */
    public function viewTrash(): View
    {
        $posts = Post::onlyTrashed()
            ->with('author')
            ->orderByDesc('created_at')
            ->get();

        return view('cms.posts.trash', compact('posts'));
    }

    /**
     * Restore the specified resource in storage.
     */
    public function restore(Post $post): RedirectResponse
    {
        $post = $this->postService->restorePost($post);

        return redirect()->route(config('cms.route_name_prefix').'.posts.show', $post);
    }

    /**
     * Delete the specified resource from storage.
     */
    public function delete(Post $post): RedirectResponse
    {
        $this->postService->forceDeletePost($post);

        return redirect()->route(config('cms.route_name_prefix').'.posts.viewTrash');
    }

    /**
     * Delete all soft deleted resource from storage.
     */
    public function emptyTrash(): RedirectResponse
    {
        $this->postService->emptyTrash();

        return redirect()->route(config('cms.route_name_prefix').'.posts.index');
    }
}
