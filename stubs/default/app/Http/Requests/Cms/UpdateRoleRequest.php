<?php

namespace App\Http\Requests\Cms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Voorhof\Flash\Facades\Flash;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (Auth::user()->can('manage roles')) {
            return true;
        }

        return false;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => Str::slug($this->name),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:32', Rule::unique('roles')->ignore($this->role)],
            'permissions.*' => 'nullable|string|max:225|exists:permissions,name',
        ];
    }

    /**
     * Actions to perform after validation passes
     */
    public function actions(Role $role): Role
    {
        // Only update the role when it is not in secured_roles
        if (! in_array($role->name, config('cms.secured_roles'))) {
            $role->name = $this->safe()->name;
            $role->save();

            // Sync permissions
            $role->syncPermissions($this->safe()->permissions ?? []);

            Flash::success(__('Successful update!'));

        } else {
            // Re-sync all permissions with the admin role as a safety measure
            // the super-admin doesn't need syncing; permissions are handled by a global gate inside the AppServiceProvider
            if ($role->name === 'admin') {
                $role->givePermissionTo(Permission::all());
            }

            Flash::danger(__('Unable to update!'));
        }

        // Return role
        return $role;
    }
}
