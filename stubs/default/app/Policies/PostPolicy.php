<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Determine whether the user can view any posts.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Anyone can view the list of posts
    }

    /**
     * Determine whether the user can view the post.
     */
    public function view(?User $user, Post $post): bool
    {
        return true; // Anyone can view a post
    }

    /**
     * Determine whether the user can create posts.
     */
    public function create(User $user): bool
    {
        return $user->can('manage posts') || $user->can('create post');
    }

    /**
     * Determine whether the user can update the post.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->can('manage posts') || $user->can('edit post');
    }

    /**
     * Determine whether the user can publish the post.
     */
    public function publish(User $user, Post $post): bool
    {
        return $user->can('manage posts') || $user->can('publish post');
    }

    /**
     * Determine whether the user can soft-delete the post.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->can('manage posts');
    }

    /**
     * Determine whether the user can restore the post.
     */
    public function restore(User $user, Post $post): bool
    {
        return $user->can('manage posts');
    }

    /**
     * Determine whether the user can permanently delete the soft-deleted post.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return $user->can('manage posts');
    }

    /**
     * Determine whether the user can view soft-deleted posts.
     */
    public function viewTrash(User $user): bool
    {
        return $user->can('manage posts');
    }

    /**
     * Determine whether the user can permanently delete all soft-deleted posts.
     */
    public function emptyTrash(User $user): bool
    {
        return $user->can('manage posts');
    }
}
