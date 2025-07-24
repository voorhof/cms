<?php

namespace App\Http\Requests\Cms;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Voorhof\Flash\Facades\Flash;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (Auth::user()->can('manage users')) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user)],
            'role' => ['nullable', 'string', 'max:255', 'exists:roles,name', Rule::notIn(config('cms.secured_roles'))],
        ];
    }

    /**
     * Actions to perform after validation passes
     */
    public function actions(User $user): User
    {
        // Only update the user when it does not have the super-admin role (or when the auth user is a super-admin)
        if (! $user->hasRole('super-admin') || Auth::user()->hasRole('super-admin')) {
            $user->fill($this->safe()->only([
                'name',
                'email',
            ]));

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            // Assign a role to user
            if (Auth::user()->can('manage roles') && ! $user->hasRole(config('cms.secured_roles'))) {
                $user->syncRoles([$this->safe()->role ?? null]);
            }

            Flash::success(__('Successful update!'));

        } else {
            Flash::danger(__('Unable to update!'));
        }

        // Return user
        return $user;
    }
}
