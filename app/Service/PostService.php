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
}
