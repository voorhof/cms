<?php

namespace App\Http\Requests\Cms;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Voorhof\Flash\Facades\Flash;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (Auth::user()->can('manage posts')) {
            return true;
        }

        if (Auth::user()->can('create post')) {
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
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ];
    }

    /**
     * Actions to perform after validation passes
     */
    public function actions(): Post
    {
        // Create a new post for the user
        $post = Auth::user()->posts()
            ->create($this->safe()->only([
                'title',
                'body',
            ]));

        // Flash message:
        Flash::success(__('Successful creation!'));

        // Return post
        return $post;
    }
}
