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

use App\Model\User;

class FollowService
{
    /**
     * follow.
     */
    public function follow(int $followId, int $userId): bool
    {
        $user = User::find($userId);
        if ($user === null) {
            return false;
        }
        $user->following()->syncWithoutDetaching((array) $followId);

        return true;
    }

    /**
     * unfollow.
     */
    public function unfollow(int $followId, int $userId): bool
    {
        $user = User::find($userId);
        if ($user === null) {
            return false;
        }
        if ($user->following()->detach((array) $followId) === 0) {
            return false;
        }

        return true;
    }
}
