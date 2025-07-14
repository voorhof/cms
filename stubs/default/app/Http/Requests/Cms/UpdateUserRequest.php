<?php

namespace App\Http\Requests\Cms;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
            'role' => 'nullable|string|max:255|exists:roles,name|not_in:super-admin,admin',
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
            if (Auth::user()->can('manage roles') && ! $user->hasRole(['super-admin', 'admin'])) {
                $user->syncRoles([$this->safe()->role ?? null]);
            }

            session()->flash('flash_message', __('Successful update!'));
            session()->flash('flash_level', 'success');

        } else {
            session()->flash('flash_message', __('Unable to update!'));
            session()->flash('flash_level', 'danger');
        }

        // Return user
        return $user;
    }
}
