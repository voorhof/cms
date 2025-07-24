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
use Voorhof\Flash\Facades\Flash;

class CmsUserController extends BaseCmsController implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage users', except: ['index', 'show']),
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
        $roles = Role::whereNotIn('name', config('cms.secured_roles'))
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

        return redirect()->route(config('cms.route_name_prefix').'.users.show', $user);
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
        $roles = Role::whereNotIn('name', config('cms.secured_roles'))
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

        return redirect()->route(config('cms.route_name_prefix').'.users.show', $user);
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

            Flash::warning(__('Successful delete!'));

        } else {
            Flash::danger(__('Unable to delete!'));

            return redirect()->route(config('cms.route_name_prefix').'.users.show', $user);
        }

        return redirect()->route(config('cms.route_name_prefix').'.users.index');
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

        Flash::success(__('Successful restore!'));

        return redirect()->route(config('cms.route_name_prefix').'.users.show', $user);
    }

    /**
     * Delete the specified resource from storage.
     */
    public function delete(User $user): RedirectResponse
    {
        $user->forceDelete();

        Flash::warning(__('Successful delete!'));

        return redirect()->route(config('cms.route_name_prefix').'.users.trash');
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

        Flash::warning(__('Successful delete!'));

        return redirect()->route(config('cms.route_name_prefix').'.users.index');
    }
}
