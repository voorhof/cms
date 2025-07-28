<?php

namespace App\Http\Controllers\Cms;

use App\Http\Requests\Cms\StoreRoleRequest;
use App\Http\Requests\Cms\UpdateRoleRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\Cms\Contracts\RoleServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class CmsRoleController extends BaseCmsController implements HasMiddleware
{
    /**
     * Create a new controller instance,
     * with the role service implementation.
     *
     * @return void
     */
    public function __construct(
        protected RoleServiceInterface $roleService
    ) {}

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,App\Models\Role', only: ['index']),
            new Middleware('can:view,role', only: ['show']),
            new Middleware('can:create,App\Models\Role', only: ['create', 'store']),
            new Middleware('can:update,role', only: ['edit', 'update']),
            new Middleware('can:delete,role', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $roles = Role::orderBy('name')
            ->get();

        return view('cms.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $permissions = Permission::orderBy('name')
            ->pluck('name');

        return view('cms.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        // The request is already validated through the StoreRoleRequest class
        $role = $this->roleService->createRole(
            $request->safe()->name,
            $request->safe()->permissions ?? []
        );

        return redirect()->route(config('cms.route_name_prefix').'.roles.show', $role);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): View
    {
        $users = User::role($role->name)
            ->orderBy('name')
            ->get();

        return view('cms.roles.show', compact('role', 'users'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): View
    {
        $permissions = Permission::orderBy('name')
            ->pluck('name');

        return view('cms.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        // The request is already validated through the UpdateRoleRequest class
        $updatedRole = $this->roleService->updateRole(
            $role,
            $request->safe()->name,
            $request->safe()->permissions ?? []
        );

        return redirect()->route(config('cms.route_name_prefix').'.roles.show', $updatedRole);
    }

    /**
     * Permanently delete the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        $this->roleService->deleteRole($role);

        return redirect()->route(config('cms.route_name_prefix').'.roles.index');
    }
}
