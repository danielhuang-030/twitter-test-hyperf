<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Service;

use App\Model\Post;

class PostService
{
    /**
     * create post.
     */
    public function createPost(array $data, int $userId): ?Post
    {
        $data['user_id'] = $userId;

        return Post::create($data);
    }

    /**
     * get post.
     */
    public function getPost(int $id): ?Post
    {
        return Post::find($id);
    }

    /**
     * update post.
     */
    public function updatePost(array $data, int $postId, int $userId): ?Post
    {
        $post = Post::find($postId);
        if ($post === null) {
            return null;
        }

        if ($post->user_id != $userId) {
            return null;
        }

        if (! $post->update($data)) {
            return null;
        }

        return $post;
    }

    /**
     * delete post.
     */
    public function deletePost(int $postId, int $userId): bool
    {
        $post = Post::find($postId);
        if ($post === null) {
            return false;
        }

        if ($post->user_id != $userId) {
            return false;
        }

        if ($post->delete($postId) === 0) {
            return false;
        }

        return true;
    }

    /**
     * like post.
     */
    public function likePost(int $postId, int $userId): bool
    {
        $post = Post::find($postId);
        if ($post === null || $post->user_id === $userId) {
            return false;
        }
        $post->likedUsers()->syncWithoutDetaching((array) $userId);

        return true;
    }
}
