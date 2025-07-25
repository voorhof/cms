<?php

namespace App\Services\Cms\Contracts;

use App\Models\Post;

interface PostServiceInterface
{
    /**
     * Create a new post with the given data
     *
     * @param  array  $postData  Validated post data
     * @param  int  $userId  ID of the user creating the post
     * @return Post The created post
     */
    public function createPost(array $postData, int $userId): Post;

    /**
     * Update an existing post with the given data
     *
     * @param  Post  $post  The post to update
     * @param  array  $postData  Validated post data
     * @return Post The updated post
     */
    public function updatePost(Post $post, array $postData): Post;

    /**
     * Publish or unpublish a post
     *
     * @param  Post  $post  The post to publish/unpublish
     * @param  string|null  $publishedAt  The date/time to publish the post, or null to use the current time
     * @param  int  $userId  ID of the user publishing the post
     * @return Post The published/unpublished post
     */
    public function togglePublishStatus(Post $post, ?string $publishedAt, int $userId): Post;

    /**
     * Delete a post (soft delete)
     *
     * @param  Post  $post  The post to delete
     * @return Post The deleted post
     */
    public function deletePost(Post $post): Post;

    /**
     * Restore a soft-deleted post
     *
     * @param  Post  $post  The post to restore
     * @return Post The restored post
     */
    public function restorePost(Post $post): Post;

    /**
     * Permanently delete a post
     *
     * @param  Post  $post  The post to permanently delete
     */
    public function forceDeletePost(Post $post): void;

    /**
     * Permanently delete all soft-deleted posts
     */
    public function emptyTrash(): void;
}
