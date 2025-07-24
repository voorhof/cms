<?php

namespace App\Http\Controllers\Cms;

use App\Http\Requests\Cms\StoreRoleRequest;
use App\Http\Requests\Cms\UpdateRoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Voorhof\Flash\Facades\Flash;

class CmsRoleController extends BaseCmsController implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage roles'),
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
        $role = $request->actions();

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
        $role = $request->actions($role);

        return redirect()->route(config('cms.route_name_prefix').'.roles.show', $role);
    }

    /**
     * Delete the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        // Only delete the role when it is not in secured_roles
        if (! in_array($role->name, config('cms.secured_roles'))) {
            $role->delete();

            Flash::warning(__('Successful delete!'));

        } else {
            Flash::danger(__('Unable to delete!'));

            return redirect()->route(config('cms.route_name_prefix').'.roles.show', $role);
        }

        return redirect()->route(config('cms.route_name_prefix').'.roles.index');
    }
}
