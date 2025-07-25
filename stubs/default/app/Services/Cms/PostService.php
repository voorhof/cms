<?php

namespace App\Services\Cms;

use App\Models\Post;
use App\Services\Cms\Contracts\PostServiceInterface;
use Voorhof\Flash\Facades\Flash;

class PostService implements PostServiceInterface
{
    /**
     * Create a new post with the given data
     *
     * @param  array  $postData  Validated post data
     * @param  int  $userId  ID of the user creating the post
     * @return Post The created post
     */
    public function createPost(array $postData, int $userId): Post
    {
        // Create a new post
        $post = new Post($postData);
        $post->user_id = $userId;

        // Save post
        $post->save();

        // Flash message
        Flash::success(__('Successful creation!'));

        // Return post
        return $post;
    }

    /**
     * Update an existing post with the given data
     *
     * @param  Post  $post  The post to update
     * @param  array  $postData  Validated post data
     * @return Post The updated post
     */
    public function updatePost(Post $post, array $postData): Post
    {
        // Update post
        $post->fill($postData);
        $post->save();

        // Flash message
        Flash::success(__('Successful update!'));

        // Return post
        return $post;
    }

    /**
     * Publish or unpublish a post
     *
     * @param  Post  $post  The post to publish/unpublish
     * @param  string|null  $publishedAt  The date/time to publish the post, or null to use the current time
     * @param  int  $userId  ID of the user publishing the post
     * @return Post The published/unpublished post
     */
    public function togglePublishStatus(Post $post, ?string $publishedAt, int $userId): Post
    {
        if ($post->published_at) {
            // Unpublish
            $post->published_at = null;
            $post->published_by = null;

            Flash::warning(__('Post unpublished!'));
        } else {
            // Publish
            $post->published_at = $publishedAt ?? now();
            $post->published_by = $userId;

            Flash::success(__('Post published!'));
        }

        $post->save();

        return $post;
    }

    /**
     * Delete a post (soft delete)
     *
     * @param  Post  $post  The post to delete
     * @return Post The deleted post
     */
    public function deletePost(Post $post): Post
    {
        $post->delete();

        Flash::warning(__('Successful delete!'));

        return $post;
    }

    /**
     * Restore a soft-deleted post
     *
     * @param  Post  $post  The post to restore
     * @return Post The restored post
     */
    public function restorePost(Post $post): Post
    {
        $post->restore();

        Flash::success(__('Successful restore!'));

        return $post;
    }

    /**
     * Permanently delete a post
     *
     * @param  Post  $post  The post to permanently delete
     */
    public function forceDeletePost(Post $post): void
    {
        $post->forceDelete();

        Flash::warning(__('Successful delete!'));
    }

    /**
     * Permanently delete all soft-deleted posts
     */
    public function emptyTrash(): void
    {
        foreach (Post::onlyTrashed()->get() as $post) {
            $post->forceDelete();
        }

        Flash::warning(__('Successful delete!'));
    }
}
