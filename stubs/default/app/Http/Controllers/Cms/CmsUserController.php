<?php

namespace App\Http\Controllers\Cms;

use App\Http\Requests\Cms\StoreUserRequest;
use App\Http\Requests\Cms\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class CmsUserController extends BaseCmsController implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:manage users', except: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $users = User::with('roles')
            ->orderBy('name')
            ->get();
        $usersTrashCount = User::onlyTrashed()->count();

        return view('cms.users.index', compact('users', 'usersTrashCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $roles = Role::whereNotIn('name', ['super-admin', 'admin'])
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('cms.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = $request->actions();

        return redirect()->route('cms.users.show', $user);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        return view('cms.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $roles = Role::whereNotIn('name', ['super-admin', 'admin'])
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('cms.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user = $request->actions($user);

        return redirect()->route('cms.users.show', $user);
    }

    /**
     * Soft delete the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // only delete the user when it does not have the super-admin role (or when the auth user is a super-admin)
        if (! $user->hasRole('super-admin') || Auth::user()->hasRole('super-admin')) {
            $user->email_verified_at = null;
            $user->save();
            $user->delete();

            session()->flash('flash_message', __('Successful delete!'));
            session()->flash('flash_level', 'warning');

        } else {
            session()->flash('flash_message', __('Unable to delete!'));
            session()->flash('flash_level', 'danger');

            return redirect()->route('cms.users.show', $user);
        }

        return redirect()->route('cms.users.index');
    }

    /**
     * Display a listing of soft deleted resource.
     */
    public function trash(): View
    {
        $users = User::onlyTrashed()->orderBy('name')->get();

        return view('cms.users.trash', compact('users'));
    }

    /**
     * Restore the specified resource in storage.
     */
    public function restore(User $user): RedirectResponse
    {
        $user->restore();

        session()->flash('flash_message', __('Successful restore!'));
        session()->flash('flash_level', 'success');

        return redirect()->route('cms.users.show', $user);
    }

    /**
     * Delete the specified resource from storage.
     */
    public function delete(User $user): RedirectResponse
    {
        $user->forceDelete();

        session()->flash('flash_message', __('Successful delete!'));
        session()->flash('flash_level', 'warning');

        return redirect()->route('cms.users.trash');
    }

    /**
     * Delete all soft deleted resource from storage.
     */
    public function emptyTrash(): RedirectResponse
    {
        defer(function () {
            foreach (User::onlyTrashed()->get() as $user) {
                $user->forceDelete();
            }
        });

        session()->flash('flash_message', __('Successful delete!'));
        session()->flash('flash_level', 'warning');

        return redirect()->route('cms.users.index');
    }
}
