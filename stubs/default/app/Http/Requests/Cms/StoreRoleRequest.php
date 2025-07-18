<?php

namespace App\Http\Requests\Cms;

use App\Facades\Flash;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class StoreRoleRequest extends FormRequest
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
            'name' => 'required|string|max:32|unique:roles',
            'permissions.*' => 'nullable|string|max:225|exists:permissions,name',
        ];
    }

    /**
     * Actions to perform after validation passes
     */
    public function actions(): Role
    {
        // Create a new role
        $role = Role::create(['name' => $this->safe()->name]);

        // Sync permissions
        $role->syncPermissions($this->safe()->permissions ?? []);

        // Flash message:
        Flash::success(__('Successful creation!'));

        // Return role
        return $role;
    }
}
