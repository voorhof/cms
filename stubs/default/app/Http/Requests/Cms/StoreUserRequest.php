<?php

namespace App\Http\Requests\Cms;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'email' => 'required|email|max:255|unique:users',
            'role' => ['nullable', 'string', 'max:255', 'exists:roles,name', Rule::notIn(config('cms.secured_roles'))],
        ];
    }

    /**
     * Actions to perform after validation passes
     */
    public function actions(): User
    {
        // Create a new user
        $user = new User($this->safe()->only([
            'name',
            'email',
        ]));

        // Create a random password
        $user->password = Hash::make(str()->random(12));

        // Save user
        $user->save();

        // Assign a role to user
        if (Auth::user()->can('manage roles')) {
            $user->syncRoles([$this->safe()->role ?? null]);
        }

        // Flash message:
        session()->flash('flash_message', __('Successful creation!'));
        session()->flash('flash_level', 'success');

        // Return user
        return $user;
    }
}
