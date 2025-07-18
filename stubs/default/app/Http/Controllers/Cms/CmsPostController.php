<?php

namespace App\Http\Controllers\Cms;

use App\Http\Requests\Cms\StorePostRequest;
use App\Http\Requests\Cms\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class CmsPostController extends BaseCmsController implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage posts|create post', only: ['create', 'store']),
            new Middleware('permission:manage posts|edit post', only: ['edit', 'update']),
            new Middleware('permission:manage posts|publish post', only: ['publish']),
            new Middleware('permission:manage posts', only: ['destroy', 'trash', 'restore', 'delete', 'emptyTrash']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $posts = Post::with('user')
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
        $post = $request->actions();

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
        $post = $request->actions($post);

        return redirect()->route(config('cms.route_name_prefix').'.posts.show', $post);
    }

    /**
     * (un-)Publish the specified resource.
     */
    public function publish(Request $request, Post $post): RedirectResponse
    {
        if ($post->published_at === null) {
            $post->published_by = auth()->id();
            $post->published_at = $request->published_at ?? now();

            session()->flash('flash_message', __('Published!'));
            session()->flash('flash_level', 'success');
        } else {
            $post->published_by = null;
            $post->published_at = null;

            session()->flash('flash_message', __('Unpublished!'));
            session()->flash('flash_level', 'warning');
        }

        $post->save();

        return redirect()->route(config('cms.route_name_prefix').'.posts.show', $post);
    }

    /**
     * Soft delete the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        $post->published_at = null;
        $post->save();
        $post->delete();

        session()->flash('flash_message', __('Successful delete!'));
        session()->flash('flash_level', 'warning');

        return redirect()->route(config('cms.route_name_prefix').'.posts.index');
    }

    /**
     * Display a listing of soft deleted resource.
     */
    public function trash(): View
    {
        $posts = Post::onlyTrashed()
            ->with('user')
            ->orderByDesc('created_at')
            ->get();

        return view('cms.posts.trash', compact('posts'));
    }

    /**
     * Restore the specified resource in storage.
     */
    public function restore(Post $post): RedirectResponse
    {
        $post->restore();

        session()->flash('flash_message', __('Successful restore!'));
        session()->flash('flash_level', 'success');

        return redirect()->route(config('cms.route_name_prefix').'.posts.show', $post);
    }

    /**
     * Delete the specified resource from storage.
     */
    public function delete(Post $post): RedirectResponse
    {
        $post->forceDelete();

        session()->flash('flash_message', __('Successful delete!'));
        session()->flash('flash_level', 'warning');

        return redirect()->route(config('cms.route_name_prefix').'.posts.trash');
    }

    /**
     * Delete all soft deleted resource from storage.
     */
    public function emptyTrash(): RedirectResponse
    {
        defer(function () {
            foreach (Post::onlyTrashed()->get() as $post) {
                $post->forceDelete();
            }
        });

        session()->flash('flash_message', __('Successful delete!'));
        session()->flash('flash_level', 'warning');

        return redirect()->route(config('cms.route_name_prefix').'.posts.index');
    }
}
