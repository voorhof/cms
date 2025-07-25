<?php

namespace App\Http\Controllers\Cms;

use App\Http\Requests\Cms\StoreUserRequest;
use App\Http\Requests\Cms\UpdateUserRequest;
use App\Models\User;
use App\Services\Cms\Contracts\UserServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class CmsUserController extends BaseCmsController implements HasMiddleware
{
    /**
     * The user service implementation.
     */
    protected UserServiceInterface $userService;

    /**
     * Create a new controller instance.
     *
     * @param UserServiceInterface $userService
     * @return void
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

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
        // The request is already validated through the StoreUserRequest class
        $user = $this->userService->createUser(
            $request->safe()->only(['name', 'email']),
            $request->safe()->role ?? null
        );

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
        // The request is already validated through the UpdateUserRequest class
        $updatedUser = $this->userService->updateUser(
            $user,
            $request->safe()->only(['name', 'email']),
            $request->safe()->role ?? null
        );

        return redirect()->route(config('cms.route_name_prefix').'.users.show', $updatedUser);
    }

    /**
     * Soft delete the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $success = $this->userService->deleteUser($user);

        if (! $success) {
            return redirect()->route(config('cms.route_name_prefix').'.users.show', $user);
        }

        return redirect()->route(config('cms.route_name_prefix').'.users.index');
    }

    /**
     * Display a listing of soft deleted resource.
     */
    public function trash(): View
    {
        $users = User::onlyTrashed()
            ->orderBy('name')
            ->get();

        return view('cms.users.trash', compact('users'));
    }

    /**
     * Restore the specified resource in storage.
     */
    public function restore(User $user): RedirectResponse
    {
        $user = $this->userService->restoreUser($user);

        return redirect()->route(config('cms.route_name_prefix').'.users.show', $user);
    }

    /**
     * Delete the specified resource from storage.
     */
    public function delete(User $user): RedirectResponse
    {
        $this->userService->forceDeleteUser($user);

        return redirect()->route(config('cms.route_name_prefix').'.users.trash');
    }

    /**
     * Delete all soft deleted resource from storage.
     */
    public function emptyTrash(): RedirectResponse
    {
        $this->userService->emptyTrash();

        return redirect()->route(config('cms.route_name_prefix').'.users.index');
    }
}
